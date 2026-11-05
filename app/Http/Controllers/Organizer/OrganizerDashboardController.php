<?php

namespace App\Http\Controllers\Organizer;

use App\Http\Controllers\Controller;
use App\Models\Draw\Draw;
use App\Models\Draw\Organizer;
use App\Models\Draw\Participant;
use App\Models\Email\EmailDelivery;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Controller for organizer dashboard
 */
class OrganizerDashboardController extends Controller
{
    public function __invoke(Request $request, Draw $draw): JsonResponse
    {
        try {
            // Récupérer l'organisateur (soit dans la table organizers, soit dans participants)
            $organizer = $draw->organizer;
            $organizerParticipant = null;
            
            if (!$organizer && $draw->organizer_participates) {
                // L'organisateur est un participant
                $organizerParticipant = $draw->participants()
                    ->where('is_organizer', true)
                    ->first();
            }

            // Récupérer tous les participants avec leurs données d'email delivery
            $participants = $draw->participants()
                ->with([
                    'messages' => function ($query) {
                        $query->orderBy('created_at', 'desc')->limit(5);
                    },
                    'emailDeliveries' => function ($query) {
                        $query->orderBy('created_at', 'desc')->limit(5);
                    }
                ])
                ->orderBy('created_at')
                ->get()
                ->map(function ($participant) {
                    // Récupérer le dernier EmailDelivery pour ce participant
                    $latestEmailDelivery = $participant->emailDeliveries->first();
                    
                    return [
                        'id' => $participant->id,
                        'uuid' => $participant->uuid,
                        'name' => $participant->name ?? '[Encrypted]',
                        'email' => $participant->email ?? '[Encrypted]',
                        'status' => $participant->status,
                        'is_organizer' => $participant->is_organizer,
                        'email_sent_at' => $participant->email_sent_at,
                        'email_opened_at' => $participant->email_opened_at,
                        'last_access_at' => $participant->last_access_at,
                        'target_name' => $participant->target_name ?? null,
                        'recent_messages_count' => $participant->messages->count(),
                        'created_at' => $participant->created_at,
                        // Nouvelles informations de delivery
                        'email_delivery_status' => $latestEmailDelivery ? [
                            'status' => $latestEmailDelivery->status->value,
                            'status_description' => $latestEmailDelivery->status->getDescription(),
                            'status_css_class' => $latestEmailDelivery->status->getCssClass(),
                            'sent_at' => $latestEmailDelivery->sent_at,
                            'delivered_at' => $latestEmailDelivery->delivered_at,
                            'bounced_at' => $latestEmailDelivery->bounced_at,
                            'bounce_reason' => $latestEmailDelivery->bounce_reason,
                            'attempts_count' => $latestEmailDelivery->attempts_count,
                        ] : null,
                        'email_delivery_history_count' => $participant->emailDeliveries->count(),
                    ];
                });

            // Récupérer les statistiques EmailDelivery pour ce tirage
            $emailDeliveryStats = EmailDelivery::where('draw_id', $draw->id)
                ->selectRaw('status, COUNT(*) as count')
                ->groupBy('status')
                ->pluck('count', 'status')
                ->toArray();
                
            // Statistiques du tirage
            $stats = [
                'total_participants' => $participants->count(),
                'accepted_participants' => $participants->where('status', 'accepted')->count(),
                'pending_participants' => $participants->where('status', 'pending')->count(),
                'emails_sent' => $participants->whereNotNull('email_sent_at')->count(),
                'emails_opened' => $participants->whereNotNull('email_opened_at')->count(),
                'active_participants' => $participants->where('last_access_at', '>=', now()->subDays(7))->count(),
                'draw_status' => $draw->status,
                'organizer_participates' => $draw->organizer_participates,
                'drawn_at' => $draw->drawn_at,
                'revealed_at' => $draw->revealed_at,
                // Nouvelles statistiques d'email delivery
                'email_delivery' => [
                    'pending' => $emailDeliveryStats['pending'] ?? 0,
                    'sent' => $emailDeliveryStats['sent'] ?? 0,
                    'delivered' => $emailDeliveryStats['delivered'] ?? 0,
                    'bounced' => $emailDeliveryStats['bounced'] ?? 0,
                    'total' => array_sum($emailDeliveryStats),
                    'bounce_rate' => array_sum($emailDeliveryStats) > 0 ? 
                        round((($emailDeliveryStats['bounced'] ?? 0) / array_sum($emailDeliveryStats)) * 100, 1) : 0,
                    'delivery_rate' => array_sum($emailDeliveryStats) > 0 ? 
                        round((($emailDeliveryStats['delivered'] ?? 0) / array_sum($emailDeliveryStats)) * 100, 1) : 0,
                ],
            ];

            return response()->json([
                'draw' => [
                    'uuid' => $draw->uuid,
                    'title' => $draw->title ?? '[Encrypted]',
                    'description' => $draw->description ?? '[Encrypted]',
                    'status' => $draw->status,
                    'organizer_participates' => $draw->organizer_participates,
                    'registration_deadline' => $draw->registration_deadline,
                    'auto_accept_participants' => $draw->auto_accept_participants,
                    'allow_target_messages' => $draw->allow_target_messages,
                    'created_at' => $draw->created_at,
                ],
                'participants' => $participants,
                'stats' => $stats,
                'organizer' => $organizer ? [
                    'uuid' => $organizer->uuid,
                    'name' => $organizer->name ?? '[Encrypted]',
                    'email' => $organizer->email ?? '[Encrypted]',
                    'last_access_at' => $organizer->last_access_at,
                ] : null,
                'organizer_participant' => $organizerParticipant ? [
                    'uuid' => $organizerParticipant->uuid,
                    'name' => $organizerParticipant->name ?? '[Encrypted]',
                    'email' => $organizerParticipant->email ?? '[Encrypted]',
                    'last_access_at' => $organizerParticipant->last_access_at,
                ] : null,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to load dashboard'
            ], 500);
        }
    }
}