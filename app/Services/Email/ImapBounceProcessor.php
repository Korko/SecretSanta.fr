<?php

namespace App\Services\Email;

use App\Models\Email\EmailDelivery;
use Illuminate\Support\Facades\Log;

/**
 * Service pour traiter les emails de bounce via IMAP
 */
class ImapBounceProcessor
{
    private $imapConnection;
    private array $config;

    public function __construct()
    {
        $this->config = config('mail.bounce_processing', [
            'enabled' => env('BOUNCE_PROCESSING_ENABLED', false),
            'host' => env('BOUNCE_IMAP_HOST'),
            'port' => env('BOUNCE_IMAP_PORT', 993),
            'username' => env('BOUNCE_IMAP_USERNAME'),
            'password' => env('BOUNCE_IMAP_PASSWORD'),
            'encryption' => env('BOUNCE_IMAP_ENCRYPTION', 'ssl'),
            'mailbox' => env('BOUNCE_IMAP_MAILBOX', 'INBOX'),
        ]);
    }

    /**
     * Process bounce emails from IMAP
     */
    public function processBounces(): array
    {
        if (!$this->config['enabled']) {
            Log::info('Bounce processing is disabled');
            return ['processed' => 0, 'errors' => 0];
        }

        $processed = 0;
        $errors = 0;

        try {
            $this->connectToImap();
            $messages = $this->getBounceMessages();

            foreach ($messages as $messageId) {
                try {
                    $this->processBounceMessage($messageId);
                    $processed++;
                } catch (\Exception $e) {
                    Log::error('Failed to process bounce message', [
                        'message_id' => $messageId,
                        'error' => $e->getMessage()
                    ]);
                    $errors++;
                }
            }

            $this->closeImapConnection();

        } catch (\Exception $e) {
            Log::error('Failed to connect to IMAP server', [
                'error' => $e->getMessage(),
                'host' => $this->config['host']
            ]);
            $errors++;
        }

        Log::info('Bounce processing completed', [
            'processed' => $processed,
            'errors' => $errors
        ]);

        return ['processed' => $processed, 'errors' => $errors];
    }

    /**
     * Connect to IMAP server
     */
    private function connectToImap(): void
    {
        $server = sprintf(
            '{%s:%s/imap/%s}%s',
            $this->config['host'],
            $this->config['port'],
            $this->config['encryption'],
            $this->config['mailbox']
        );

        $this->imapConnection = imap_open(
            $server,
            $this->config['username'],
            $this->config['password']
        );

        if (!$this->imapConnection) {
            throw new \Exception('Failed to connect to IMAP server: ' . imap_last_error());
        }
    }

    /**
     * Get bounce messages from inbox
     */
    private function getBounceMessages(): array
    {
        $messages = [];
        $messageCount = imap_num_msg($this->imapConnection);

        for ($i = 1; $i <= $messageCount; $i++) {
            $header = imap_headerinfo($this->imapConnection, $i);

            // Vérifier si c'est un bounce (sujet contient "Delivery", "Undelivered", etc.)
            if ($this->isBounceMessage($header)) {
                $messages[] = $i;
            }
        }

        return $messages;
    }

    /**
     * Check if message is a bounce notification
     */
    private function isBounceMessage(object $header): bool
    {
        $subject = $header->subject ?? '';
        $from = $header->from[0]->mailbox ?? '' . '@' . ($header->from[0]->host ?? '');

        $bounceIndicators = [
            'delivery',
            'undelivered',
            'bounce',
            'returned mail',
            'mail delivery',
            'failure notice',
            'postmaster',
            'mailer-daemon'
        ];

        foreach ($bounceIndicators as $indicator) {
            if (stripos($subject, $indicator) !== false || stripos($from, $indicator) !== false) {
                return true;
            }
        }

        return false;
    }

    /**
     * Process a single bounce message
     */
    private function processBounceMessage(int $messageId): void
    {
        $body = imap_body($this->imapConnection, $messageId);
        $header = imap_headerinfo($this->imapConnection, $messageId);

        // Extraire l'ID du message original depuis les headers ou le corps
        $originalMessageId = $this->extractOriginalMessageId($header, $body);

        if (!$originalMessageId) {
            Log::warning('Could not extract original message ID from bounce', [
                'bounce_message_id' => $messageId,
                'subject' => $header->subject ?? 'N/A'
            ]);
            return;
        }

        // Trouver le EmailDelivery correspondant
        $emailDelivery = EmailDelivery::findByMessageId($originalMessageId);

        if (!$emailDelivery) {
            Log::warning('No EmailDelivery found for bounced message', [
                'original_message_id' => $originalMessageId
            ]);
            return;
        }

        // Analyser la raison du bounce
        $bounceReason = $this->analyzeBounceReason($body);

        // Marquer comme bounced
        $emailDelivery->markAsBounced($bounceReason, [
            'bounce_message_id' => $messageId,
            'bounce_subject' => $header->subject ?? '',
            'bounce_date' => $header->date ?? '',
            'raw_bounce_body' => substr($body, 0, 1000), // Garder les premiers 1000 caractères
        ]);

        Log::info('Email marked as bounced', [
            'email_delivery_id' => $emailDelivery->id,
            'original_message_id' => $originalMessageId,
            'bounce_reason' => $bounceReason,
            'recipient_email' => $emailDelivery->email_address,
        ]);

        // Marquer le message comme lu (optionnel)
        imap_setflag_full($this->imapConnection, $messageId, '\\Seen');
    }

    /**
     * Extract original message ID from bounce headers or body
     */
    private function extractOriginalMessageId(object $header, string $body): ?string
    {
        // Méthode 1: Chercher dans les headers références
        if (isset($header->references)) {
            $references = explode(' ', $header->references);
            foreach ($references as $ref) {
                $ref = trim($ref, '<>');
                if (preg_match('/^[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}$/i', $ref)) {
                    return $ref;
                }
            }
        }

        // Méthode 2: Chercher dans le In-Reply-To
        if (isset($header->in_reply_to)) {
            $inReplyTo = trim($header->in_reply_to, '<>');
            if (preg_match('/^[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}$/i', $inReplyTo)) {
                return $inReplyTo;
            }
        }

        // Méthode 3: Chercher dans le corps du message
        if (preg_match('/Message-ID:\s*<?([0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12})>?/i', $body, $matches)) {
            return $matches[1];
        }

        // Méthode 4: Chercher des patterns UUID dans le corps
        if (preg_match('/([0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12})/i', $body, $matches)) {
            // Vérifier que cet UUID correspond à un message existant
            if (EmailDelivery::findByMessageId($matches[1])) {
                return $matches[1];
            }
        }

        return null;
    }

    /**
     * Analyze bounce reason from message body
     */
    private function analyzeBounceReason(string $body): string
    {
        $body = strtolower($body);

        $reasons = [
            'user unknown' => 'Unknown user',
            'mailbox not found' => 'Mailbox not found',
            'mailbox full' => 'Mailbox full',
            'message too large' => 'Message too large',
            'relay access denied' => 'Relay access denied',
            'spam' => 'Detected as spam',
            'virus' => 'Virus detected',
            'timeout' => 'Connection timeout',
            'connection refused' => 'Connexion refused',
            'dns' => 'DNS error',
            'mx record' => 'MX record not found',
        ];

        foreach ($reasons as $pattern => $reason) {
            if (strpos($body, $pattern) !== false) {
                return $reason;
            }
        }

        return 'Unknown rason';
    }

    /**
     * Close IMAP connection
     */
    private function closeImapConnection(): void
    {
        if ($this->imapConnection) {
            imap_close($this->imapConnection);
        }
    }

    /**
     * Process delivery confirmations (read receipts)
     */
    public function processDeliveryConfirmations(): array
    {
        if (!$this->config['enabled']) {
            return ['processed' => 0, 'errors' => 0];
        }

        $processed = 0;
        $errors = 0;

        try {
            $this->connectToImap();
            $messageCount = imap_num_msg($this->imapConnection);

            for ($i = 1; $i <= $messageCount; $i++) {
                $header = imap_headerinfo($this->imapConnection, $i);

                // Chercher les confirmations de lecture
                if ($this->isDeliveryConfirmation($header)) {
                    try {
                        $this->processDeliveryConfirmation($i);
                        $processed++;
                    } catch (\Exception $e) {
                        Log::error('Failed to process delivery confirmation', [
                            'message_id' => $i,
                            'error' => $e->getMessage()
                        ]);
                        $errors++;
                    }
                }
            }

            $this->closeImapConnection();

        } catch (\Exception $e) {
            Log::error('Failed to process delivery confirmations', [
                'error' => $e->getMessage()
            ]);
            $errors++;
        }

        return ['processed' => $processed, 'errors' => $errors];
    }

    /**
     * Check if message is a delivery confirmation
     */
    private function isDeliveryConfirmation(object $header): bool
    {
        $subject = $header->subject ?? '';

        return stripos($subject, 'read receipt') !== false ||
               stripos($subject, 'delivery receipt') !== false ||
               stripos($subject, 'return receipt') !== false;
    }

    /**
     * Process a delivery confirmation
     */
    private function processDeliveryConfirmation(int $messageId): void
    {
        $body = imap_body($this->imapConnection, $messageId);
        $header = imap_headerinfo($this->imapConnection, $messageId);

        $originalMessageId = $this->extractOriginalMessageId($header, $body);

        if (!$originalMessageId) {
            return;
        }

        $emailDelivery = EmailDelivery::findByMessageId($originalMessageId);

        if (!$emailDelivery || $emailDelivery->isDelivered()) {
            return;
        }

        $emailDelivery->markAsDelivered($body);

        Log::info('Email marked as delivered via read receipt', [
            'email_delivery_id' => $emailDelivery->id,
            'original_message_id' => $originalMessageId,
        ]);

        imap_setflag_full($this->imapConnection, $messageId, '\\Seen');
    }
}
