<?php

namespace App\Http\Controllers\Organizer;

use App\Http\Controllers\Controller;
use App\Models\Draw\Draw;
use App\Models\Draw\Participant;
use App\Models\Email\EmailDelivery;
use App\Enums\EmailStatus;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

/**
 * Controller to update participant email
 */
class UpdateParticipantEmailController extends Controller
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

            // Validation de l'email
            $validator = Validator::make($request->all(), [
                'email' => 'required|email',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'error' => 'Validation failed',
                    'details' => $validator->errors()
                ], 422);
            }

            $newEmail = $request->input('email');
            $oldEmail = $participant->email ?? '[Encrypted]';

            // Marquer tous les anciens EmailDelivery comme obsolètes dans les métadonnées
            EmailDelivery::where('recipient_type', Participant::class)
                ->where('recipient_id', $participant->id)
                ->update([
                    'metadata' => DB::raw("JSON_SET(COALESCE(metadata, '{}'), '$.obsoleted_by_email_change', true)")
                ]);

            // Mettre à jour l'email (chiffré)
            // TODO: Récupérer la master key depuis le contexte
            // $participant->email = $newEmail;
            
            // Pour l'instant, on stocke temporairement l'email en clair pour les tests
            $participant->update([
                'temp_new_email' => $newEmail, // Champ temporaire pour les tests
                'email_updated_at' => now(),
                'email_sent_at' => null, // Réinitialiser le statut d'envoi
            ]);

            // Créer un nouveau EmailDelivery pour le nouvel email (en attente d'envoi)
            $messageId = Str::uuid()->toString();
            $emailDelivery = EmailDelivery::create([
                'draw_id' => $draw->id,
                'recipient_type' => Participant::class,
                'recipient_id' => $participant->id,
                'email_type' => 'participant_invitation',
                'email_address' => $newEmail,
                'subject' => 'Secret Santa - Invitation mise à jour',
                'message_id' => $messageId,
                'status' => EmailStatus::PENDING,
                'attempts_count' => 0,
                'metadata' => [
                    'email_updated' => true,
                    'previous_email_hash' => hash('sha256', $oldEmail),
                    'updated_by_organizer' => true,
                ]
            ]);

            Log::info("Participant email updated", [
                'draw_uuid' => $draw->uuid,
                'participant_uuid' => $participant->uuid,
                'email_delivery_id' => $emailDelivery->id,
                'message_id' => $messageId,
                'old_email_hash' => hash('sha256', $oldEmail),
                'new_email_hash' => hash('sha256', $newEmail),
            ]);

            return response()->json([
                'message' => 'Email updated successfully',
                'participant' => [
                    'uuid' => $participant->uuid,
                    'email' => $newEmail, // En attendant l'implémentation complète du chiffrement
                    'email_updated_at' => $participant->email_updated_at,
                    'email_sent_at' => $participant->email_sent_at,
                ],
                'email_delivery' => [
                    'id' => $emailDelivery->id,
                    'message_id' => $messageId,
                    'status' => $emailDelivery->status->value,
                    'status_description' => $emailDelivery->status->getDescription(),
                    'email_address' => $newEmail,
                ]
            ]);

        } catch (\Exception $e) {
            Log::error("Failed to update participant email", [
                'draw_uuid' => $draw->uuid,
                'participant_uuid' => $participant->uuid,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'error' => 'Failed to update email'
            ], 500);
        }
    }
}