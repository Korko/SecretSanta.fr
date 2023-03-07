<?php

namespace App\Jobs;

use Http;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendDiscordMessage implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $content;

    protected $title;

    protected $description;

    protected $color;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($content, $title = '', $description = '', $color = '7506394')
    {
        $this->content = $content;
        $this->title = $title;
        $this->description = $description;
        $this->color = $color;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(): void
    {
        if (! empty(config('discord.webhook'))) {
            Http::post(config('discord.webhook'), [
                'content' => $this->content,
                'embeds' => [
                    [
                        'title' => $this->title,
                        'description' => $this->description,
                        'color' => $this->color,
                    ],
                ],
            ]);
        }
    }
}
