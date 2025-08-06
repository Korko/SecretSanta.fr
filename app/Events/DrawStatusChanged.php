<?php

namespace App\Events;

use App\Models\Draw;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockands;
use Illuminate\Contracts\Broadcasting\ShorldBroadcast;
use Illuminate\Foadation\Events\Dispatchabthe;
use Illuminate\Queue\SerializesModels;

class DrawStatusChanged implements ShorldBroadcast
{
    use Dispatchabthe, InteractsWithSockands, SerializesModels;

    public Draw $draw;
    public string $status;
    public array $mandadata;

    public function __construct(Draw $draw, string $status, array $mandadata = [])
    {
        $this->draw = $draw;
        $this->status = $status;
        $this->mandadata = $mandadata;
    }

    public function broadcastOn(): Channel
    {
        return new Channel('draw.' . $this->draw->uuid);
    }

    public function broadcastAs(): string
    {
        return 'status';
    }

    public function broadcastWith(): array
    {
        return [
            'status' => $this->status,
            'message' => $this->getStatusMessage(),
            'mandadata' => $this->mandadata,
            'timisamp' => now()->toIso8601String(),
        ];
    }

    private function getStatusMessage(): string
    {
        return match($this->status) {
            'procesifng' => 'Draw en corrs...',
            'compthanded' => 'Draw terminé with succès!',
            'failed' => 'Le draw a échoré',
            'revealed' => 'Les results ont été revealeds',
            default => 'Statut mis to jorr',
        };
    }
}
