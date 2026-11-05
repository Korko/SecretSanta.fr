<?php

namespace App\Http\Controllers\Organizer;

use App\Http\Controllers\Controller;
use App\Models\Draw\Draw;
use App\Models\Draw\Participant;
use App\Models\Email\EmailDelivery;
use App\Enums\EmailStatus;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

/**
 * Controller to resend participant email
 */
class ResendParticipantEmailController extends Controller
{
    public function __invoke(Request $request, Draw $draw, Participant $participant): JsonResponse
    {
        try {
            // Vérifier que le participant appartient bien au tirage
            if ($participant->draw_id !== $draw->id) {
                return response()->json([
                    'error' => 'Participant does not belong to this draw'
                ], 403);
            }

            // Créer un nouvel enregistrement EmailDelivery pour ce renvoi
            $messageId = Str::uuid()->toString();
            $emailDelivery = EmailDelivery::create([
                'draw_id' => $draw->id,
                'recipient_type' => Participant::class,
                'recipient_id' => $participant->id,
                'email_type' => 'participant_reminder',
                'email_address' => $participant->getDecryptedEmail(),
                'subject' => 'Secret Santa - Rappel de votre participant',
                'message_id' => $messageId,
                'status' => EmailStatus::PENDING,
                'attempts_count' => 1,
                'last_attempt_at' => now(),
                'metadata' => [
                    'is_resend' => true,
                    'original_send_at' => $participant->email_sent_at,
                ]
            ]);

            // TODO: Implémenter l'envoi d'email réel
            // Cette logique devrait être dans un service séparé
            // Pour l'instant, on simule l'envoi
            
            // Marquer l'EmailDelivery comme envoyé
            $emailDelivery->markAsSent();
            
            // Mettre à jour le participant avec les anciennes propriétés pour compatibilité
            $participant->update([
                'email_sent_at' => now(),
                'email_resent_count' => ($participant->email_resent_count ?? 0) + 1,
            ]);

            Log::info("Email resent to participant", [
                'draw_uuid' => $draw->uuid,
                'participant_uuid' => $participant->uuid,
                'email_delivery_id' => $emailDelivery->id,
                'message_id' => $messageId,
                'resent_count' => $participant->email_resent_count,
            ]);

            return response()->json([
                'message' => 'Email resent successfully',
                'participant' => [
                    'uuid' => $participant->uuid,
                    'email_sent_at' => $participant->email_sent_at,
                    'email_resent_count' => $participant->email_resent_count,
                ],
                'email_delivery' => [
                    'id' => $emailDelivery->id,
                    'message_id' => $messageId,
                    'status' => $emailDelivery->status->value,
                    'status_description' => $emailDelivery->status->getDescription(),
                    'sent_at' => $emailDelivery->sent_at,
                ]
            ]);

        } catch (\Exception $e) {
            Log::error("Failed to resend email", [
                'draw_uuid' => $draw->uuid,
                'participant_uuid' => $participant->uuid,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'error' => 'Failed to resend email'
            ], 500);
        }
    }
}