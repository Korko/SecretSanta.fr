<?php

namespace App\Events;

use App\Draw;
use Illuminate\Queue\SerializesModels;

class DrawDone
{
    use SerializesModels;

    public $draw;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(Draw $draw)
    {
        $this->draw = $draw;
    }
}
