<?php

namespace App\Actions\Draw;

use App\Models\Draw\Draw;
use Illuminate\Support\Facades\Log;

/**
 * Action to open/close registrations
 */
class ToggleRegistrationAction
{
    public function execute(Draw $draw, string $action): array
    {
        try {
            if ($action === 'open') {
                if (!in_array($draw->status, ['draft', 'closed_registration'])) {
                    throw new \Exception('Cannot open registrations in current state');
                }
                $draw->openRegistrations();
                $message = 'Registrations opened';

            } elseif ($action === 'close') {
                if ($draw->status !== 'open_registration') {
                    throw new \Exception('Registrations are not open');
                }
                $draw->closeRegistrations();
                $message = 'Registrations closed';

            } else {
                throw new \Exception('Invalid action');
            }

            Log::info("Registration status changed", [
                'draw_uuid' => $draw->uuid,
                'action' => $action,
                'new_status' => $draw->status
            ]);

            return [
                'success' => true,
                'message' => $message,
                'draw' => $draw
            ];

        } catch (\Exception $e) {
            Log::error("Failed to toggle registration", [
                'draw_uuid' => $draw->uuid,
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }
}
