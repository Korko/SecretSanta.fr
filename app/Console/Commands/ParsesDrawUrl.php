<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\Draw;
use App\Models\Participant;
use App\Services\URLParser;
use RuntimeException;

use function Laravel\Prompts\text;

trait ParsesDrawUrl
{
    protected function setCryptIVFromUrl(string $url): void
    {
        throw_unless(resolve(URLParser::class)->setIVFromUrl($url), RuntimeException::class, 'No draw secret in this URL (the part after #)');
    }

    protected function findOrAskDrawFromUrl(?string $url): Draw
    {
        $url = $url ?: text(
            label: 'What is the URL?',
            placeholder: config()->string('app.url'),
            required: true,
            hint: 'This URL was received by one of the participants to write to their santa or is the link to the organizer panel'
        );

        $this->setCryptIVFromUrl($url);

        $parser = resolve(URLParser::class);

        return $parser->participant($url)->draw ?? $parser->draw($url) ?? throw new RuntimeException('No draw found for this URL');
    }

    protected function findParticipant(Draw $draw, mixed $id): Participant
    {
        return $draw->participants->firstWhere('id', is_numeric($id) ? (int) $id : 0) ?? throw new RuntimeException('Participant not found');
    }
}
