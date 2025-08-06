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

        expect($result->isSuccessful())->toBeTrue();
        $assignments = $result->getAssignments();

        expect($assignments)->toHaveCount(4);

        $assigned = array_values($assignments);
        expect(array_unique($assigned))->toHaveCount(4);

        foreach ($assignments as $giver => $receiver) {
            expect($giver)->not->toEqual($receiver);
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

        expect($assignments[1])->not->toEqual(2);
        expect($assignments[2])->not->toEqual(3);
    });

    test('draw with weak exclusions', function () {
        $participants = collect([
            (object)['id' => 1],
            (object)['id' => 2],
            (object)['id' => 3],
        ]);

        $exclusions = [
            1 => [2 => 'weak', 3 => 'weak'],
            2 => [1 => 'weak', 3 => 'weak'],
            3 => [1 => 'weak', 2 => 'weak'],
        ];

        $result = $this->algorithm->performDraw($participants, $exclusions);

        expect($result->isSuccessful())->toBeTrue();
    });

    test('impossible draw with too many strong exclusions', function () {
        $participants = collect([
            (object)['id' => 1],
            (object)['id' => 2],
            (object)['id' => 3],
        ]);

        $exclusions = [
            1 => [2 => 'strong', 3 => 'strong'],
            2 => [1 => 'strong', 3 => 'strong'],
            3 => [1 => 'strong', 2 => 'strong'],
        ];

        $result = $this->algorithm->performDraw($participants, $exclusions);

        expect($result->isSuccessful())->toBeFalse();
        expect($result->getFailureReason())->toContain('Impossible');
    });
});
