<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Http;
use Throwable;

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
    public function handle()
    {
        if (!empty(config('discord.webhook'))) {
            Http::post(config('discord.webhook'), [
                'content' => $this->content,
                'embeds' => [
                    [
                        'title' => $this->title,
                        'description' => $this->description,
                        'color' => $this->color,
                    ]
                ],
            ]);
        }
    }
}
