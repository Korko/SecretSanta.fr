<?php

use App\Services\Draw\DrawAlgorithm;

beforeEach(function () {
    $this->algorithm = new DrawAlgorithm();
});

describe('Optimized Draw Algorithm', function () {

    test('performs simple draw without exclusions', function () {
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

        // Vérifier qu'aucun participant ne s'est auto-assigné
        foreach ($result->getAssignments() as $giver => $receiver) {
            expect($giver)->not->toBe($receiver);
        }
    });

    test('respects strong exclusions', function () {
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

    test('detects impossible configurations early', function () {
        $participants = collect([
            (object)['id' => 1],
            (object)['id' => 2],
            (object)['id' => 3],
        ]);

        // Chaque participant exclut tous les autres
        $exclusions = [
            1 => [2 => 'strong', 3 => 'strong'],
            2 => [1 => 'strong', 3 => 'strong'],
            3 => [1 => 'strong', 2 => 'strong'],
        ];

        $result = $this->algorithm->performDraw($participants, $exclusions);

        expect($result->isSuccessful())->toBeFalse()
            ->and($result->getErrors())->toContain('cannot give to anyone due to strong exclusions');
    });

    test('handles weak exclusions when possible', function () {
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

        // Les exclusions faibles peuvent être ignorées si nécessaire
        if (!empty($result->getIgnoredWeakExclusions())) {
            expect($result->getIgnoredWeakExclusions())->toBeArray();
        }
    });

    test('performs efficiently on large draws', function () {
        $participants = collect(range(1, 100))->map(fn($id) => (object)['id' => $id]);

        // Ajouter quelques exclusions aléatoires
        $exclusions = [];
        for ($i = 1; $i <= 20; $i++) {
            $excluded = rand(1, 100);
            if ($excluded !== $i) {
                $exclusions[$i] = [$excluded => 'strong'];
            }
        }

        $startTime = microtime(true);
        $result = $this->algorithm->performDraw($participants, $exclusions);
        $duration = microtime(true) - $startTime;

        expect($result->isSuccessful())->toBeTrue()
            ->and($result->getAssignments())->toHaveCount(100)
            ->and($duration)->toBeLessThan(1.0); // Moins d'une seconde
    });
});
