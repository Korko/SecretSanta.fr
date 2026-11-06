<?php

declare(strict_types=1);

use App\Models\Draw;
use App\Models\Participant;

it('keeps excluded participants when one is deleted', function (): void {
    $draw = Draw::factory()->hasParticipants(4)->create();
    [$a, $b, $c] = $draw->participants->all();
    $a->exclusions()->attach($b);
    $c->exclusions()->attach($a);

    $a->delete();

    expect(Participant::query()->whereKey([$b->id, $c->id])->count())->toBe(2)
        ->and($c->exclusions()->count())->toBe(0);
});
