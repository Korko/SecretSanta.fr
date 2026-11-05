<?php

namespace App\Enums;

enum EmailStatus: string
{
    case PENDING = 'pending';
    case SENT = 'sent';
    case DELIVERED = 'delivered';
    case BOUNCED = 'bounced';

    /**
     * Get human-readable description
     */
    public function getDescription(): string
    {
        return match($this) {
            self::PENDING => 'En attente d\'envoi',
            self::SENT => 'Envoyé',
            self::DELIVERED => 'Reçu/Lu',
            self::BOUNCED => 'Erreur de livraison',
        };
    }

    /**
     * Get CSS class for status display
     */
    public function getCssClass(): string
    {
        return match($this) {
            self::PENDING => 'text-yellow-600',
            self::SENT => 'text-blue-600',
            self::DELIVERED => 'text-green-600',
            self::BOUNCED => 'text-red-600',
        };
    }

    /**
     * Check if email is in a final state
     */
    public function isFinal(): bool
    {
        return in_array($this, [self::DELIVERED, self::BOUNCED]);
    }

    /**
     * Check if email was successfully delivered
     */
    public function isSuccessful(): bool
    {
        return $this === self::DELIVERED;
    }

    /**
     * Check if email failed to deliver
     */
    public function isFailed(): bool
    {
        return $this === self::BOUNCED;
    }

    /**
     * Get all possible statuses
     */
    public static function all(): array
    {
        return [
            self::PENDING,
            self::SENT,
            self::DELIVERED,
            self::BOUNCED,
        ];
    }

    /**
     * Get statuses that indicate the email was processed
     */
    public static function processed(): array
    {
        return [
            self::SENT,
            self::DELIVERED,
            self::BOUNCED,
        ];
    }
}