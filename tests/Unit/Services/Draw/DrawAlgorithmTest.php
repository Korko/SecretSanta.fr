<?php

namespace Tests\Unit\Services\Draw;

use App\Services\Draw\DrawAlgorithm;
use Tests\TestCase;

class DrawAlgorithmTest extends TestCase
{
    private DrawAlgorithm $algorithm;

    protected function setUp(): void
    {
        parent::setUp();
        $this->algorithm = new DrawAlgorithm();
    }

    public function test_simple_draw_without_exclusions()
    {
        $participants = collect([
            (object)['id' => 1],
            (object)['id' => 2],
            (object)['id' => 3],
            (object)['id' => 4],
        ]);

        $result = $this->algorithm->performDraw($participants, []);

        $this->assertTrue($result->isSuccessful());
        $assignments = $result->getAssignments();

        // Vérifier que chaque participant a une assignation
        $this->assertCount(4, $assignments);

        // Vérifier que chaque participant est assigné une seule fois
        $assigned = array_values($assignments);
        $this->assertCount(4, array_unique($assigned));

        // Vérifier qu'aucun participant ne s'est auto-assigné
        foreach ($assignments as $giver => $receiver) {
            $this->assertNotEquals($giver, $receiver);
        }
    }

    public function test_draw_with_strong_exclusions()
    {
        $participants = collect([
            (object)['id' => 1],
            (object)['id' => 2],
            (object)['id' => 3],
            (object)['id' => 4],
        ]);

        $exclusions = [
            1 => [2 => 'strong'], // 1 ne peut pas piocher 2
            2 => [3 => 'strong'], // 2 ne peut pas piocher 3
        ];

        $result = $this->algorithm->performDraw($participants, $exclusions);

        $this->assertTrue($result->isSuccessful());
        $assignments = $result->getAssignments();

        // Vérifier que les exclusions fortes sont respectées
        $this->assertNotEquals(2, $assignments[1]);
        $this->assertNotEquals(3, $assignments[2]);
    }

    public function test_draw_with_weak_exclusions()
    {
        $participants = collect([
            (object)['id' => 1],
            (object)['id' => 2],
            (object)['id' => 3],
        ]);

        $exclusions = [
            1 => [2 => 'weak', 3 => 'weak'], // 1 préfère ne pas piocher 2 ou 3
            2 => [1 => 'weak', 3 => 'weak'], // 2 préfère ne pas piocher 1 ou 3
            3 => [1 => 'weak', 2 => 'weak'], // 3 préfère ne pas piocher 1 ou 2
        ];

        $result = $this->algorithm->performDraw($participants, $exclusions);

        // Le tirage devrait réussir même si toutes les exclusions faibles ne peuvent pas être respectées
        $this->assertTrue($result->isSuccessful());
    }

    public function test_impossible_draw_with_too_many_strong_exclusions()
    {
        $participants = collect([
            (object)['id' => 1],
            (object)['id' => 2],
            (object)['id' => 3],
        ]);

        $exclusions = [
            1 => [2 => 'strong', 3 => 'strong'], // 1 ne peut piocher personne sauf lui-même
            2 => [1 => 'strong', 3 => 'strong'], // 2 ne peut piocher personne sauf lui-même
            3 => [1 => 'strong', 2 => 'strong'], // 3 ne peut piocher personne sauf lui-même
        ];

        $result = $this->algorithm->performDraw($participants, $exclusions);

        $this->assertFalse($result->isSuccessful());
        $this->assertStringContainsString('Impossible', $result->getFailureReason());
    }
}
