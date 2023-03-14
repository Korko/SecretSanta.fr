<?php

namespace App\Solvers;

use Generator;
use Illuminate\Support\Collection;

class GraphSolver extends Solver
{
    //TODO: seed shuffle?
    protected function solve(Collection $participantsIdx, Collection $exclusions): Generator
    {
        return $this->findPaths($participantsIdx, $exclusions);
    }

    /**
     * @param  Collection<int>  $nodesLeft
     * @param  Collection<array<int>>  $exclusions
     */
    protected function findPaths(Collection $nodesLeft, Collection $exclusions)
    {
        // Preformat nodes to weight them by the amount of exclusions for each one
        $nodesLeft = $nodesLeft
            ->shuffle() // Remove keys so to do BEFORE the mapWithKeys
            ->mapWithKeys(function ($nodeIdx) use ($exclusions) {
                // The more the nodeIdx have exclusions, the more we should pick it (min weight should be 1)
                return [$nodeIdx => 1 + count($exclusions->get($nodeIdx, []))];
            })
            ->sortDesc(); // We both shuffle and sort to handle very picky participants first

        // Preformat the exclusions to have the same format as participants [node => [exclusion1 => 0, exclusion2 => 0, ...]]
        foreach ($exclusions as $idx => $participantExclusions) {
            $exclusions[$idx] = array_flip($participantExclusions);
        }

        return $this->findPathsFrom(
            startNode: $nodesLeft->firstKey(),
            nodesLeft: $nodesLeft,
            exclusions: $exclusions,
            list: []
        );
    }

    private function findPathsFrom($startNode, Collection $nodesLeft, Collection $exclusions, $list = [])
    {
        // nodesLeft is 1 based sql table indexed
        $availableNodes = $nodesLeft
            ->diffKeys([$startNode => 0])
            ->diffKeys($exclusions->get($startNode, []));

        while (count($availableNodes) > 0) {
            // Pick a random number between 0 and the sum of all weights
            // And then search in the list which one reaches that random weight
            $rand = mt_rand(0, $availableNodes->sum());
            $nextNode = $availableNodes->firstKey(function ($weight, $nextNode) use (&$rand) {
                return ($rand -= $weight) <= 0;
            });
            unset($availableNodes[$nextNode]);

            // Keep the weight if we need to remove and add it back again later on
            $nextNodeWeight = $nodesLeft[$nextNode];

            $nodesLeft->offsetUnset($nextNode);

            $list[$startNode] = $nextNode;

            if (! isset($list[$nextNode])) {
                // We need to go deeper
                yield from $this->findPathsFrom(startNode: $nextNode, nodesLeft: $nodesLeft, exclusions: $exclusions, list: $list);
            } elseif ($nodesLeft->count() >= 2) {
                // Another partial graph is possible
                yield from $this->findPathsFrom(startNode: $nodesLeft->firstKey(), nodesLeft: $nodesLeft, exclusions: $exclusions, list: $list);
            } elseif ($nodesLeft->count() === 0) {
                // Solution found
                yield $list;
            }

            // Now that we've explored that node, add it back in the total list of nodes (but keep it out of available ones)
            $nodesLeft[$nextNode] = $nextNodeWeight;
        }
    }
}
