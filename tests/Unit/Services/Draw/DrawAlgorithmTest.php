<?php

use App\Services\Draw\DrawAlgorithm;

describe('DrawAlgorithm', function () {
    beforeEach(function () {
        $this->algorithm = new DrawAlgorithm();
    });

    test('simple draw without exclusions', function () {
        $participants = collect([
            (object)['id' => 1],
            (object)['id' => 2],
            (object)['id' => 3],
            (object)['id' => 4],
        ]);

        $result = $this->algorithm->performDraw($participants, []);

        expect($result->isSuccessful())->toBeTrue()
            ->and($result->getAssignments())->toHaveCount(4)
            ->and(array_unique($result->getAssignments()))->toHaveCount(4);

        // Verify that no participant has been self-assigned
        foreach ($result->getAssignments() as $giver => $receiver) {
            expect($giver)->not->toBe($receiver);
        }
    });

    test('draw with strong exclusions', function () {
        $participants = collect([
            (object)['id' => 1],
            (object)['id' => 2],
            (object)['id' => 3],
            (object)['id' => 4],
        ]);

        $exclusions = [
            1 => [2 => 'strong'],
            2 => [3 => 'strong'],
        ];

        $result = $this->algorithm->performDraw($participants, $exclusions);

        expect($result->isSuccessful())->toBeTrue();
        $assignments = $result->getAssignments();

        expect($assignments[1])->not->toBe(2)
            ->and($assignments[2])->not->toBe(3);
    });

    test('draw with weak exclusions', function () {
        $participants = collect([
            (object)['id' => 1],
            (object)['id' => 2],
            (object)['id' => 3],
            (object)['id' => 4],
        ]);

        $exclusions = [
            1 => [2 => 'weak'],
            2 => [3 => 'weak'],
        ];

        $result = $this->algorithm->performDraw($participants, $exclusions);

        expect($result->isSuccessful())->toBeTrue();

        // Weak exclusions can be ignored if necessary
        if (!empty($result->getIgnoredWeakExclusions())) {
            expect($result->getIgnoredWeakExclusions())->toBeArray();
        }
    });

    test('detects impossible configurations early', function () {
        $participants = collect([
            (object)['id' => 1],
            (object)['id' => 2],
            (object)['id' => 3],
        ]);

        // Each participant excludes all others
        $exclusions = [
            1 => [2 => 'strong', 3 => 'strong'],
            2 => [1 => 'strong', 3 => 'strong'],
            3 => [1 => 'strong', 2 => 'strong'],
        ];

        $result = $this->algorithm->performDraw($participants, $exclusions);

        expect($result->isSuccessful())->toBeFalse()
            ->and($result->getFailureReason())->toContain('cannot give to anyone due to strong exclusions');
    });

    test('performs efficiently on large draws', function () {
        $participants = collect(range(1, 50))->map(fn($id) => (object)['id' => $id]);

        // Add some random exclusions
        $exclusions = [];
        for ($i = 1; $i <= 10; $i++) {
            $excluded = rand(1, 50);
            if ($excluded !== $i) {
                $exclusions[$i] = [$excluded => 'strong'];
            }
        }

        $startTime = microtime(true);
        $result = $this->algorithm->performDraw($participants, $exclusions);
        $duration = microtime(true) - $startTime;

        expect($result->isSuccessful())->toBeTrue()
            ->and($result->getAssignments())->toHaveCount(50)
            ->and($duration)->toBeLessThan(1.0); // Less than one second
    });
});
