<?php

namespace App\Solvers;

use Arr;
use Generator;

function abc($idx) {
    return implode('', array_intersect_key(str_split('ABCDEFGHI', 1), array_flip((array) $idx)));
}
function pair(array $list) {
    return collect($list)->mapWithKeys(function ($b, $a) {
        return [abc($a) => abc($b)];
    })->toArray();
}

class GraphSolver extends Solver
{//TODO: seed shuffle?
    protected function solve(array $participants, array $allExclusions = []) : Generator
    {
        // Preformat nodes to weight them by the amount of exclusions for each one
        $participants = collect($participants)
            ->keys()
            ->shuffle()
            ->mapWithKeys(function ($participantIdx) use ($allExclusions) {
                // The more the participantIdx have exclusions, the more we should pick it (min weight should be 1)
                return [$participantIdx => 1 + count(Arr::get($allExclusions, $participantIdx, []))];
            })
            ->toArray();

        // Preformat the exclusions to have the same format as participants [node => [exclusion1 => 0, exclusion2 => 0, ...]]
        array_walk($allExclusions, function (&$exclusions) {
            $exclusions = array_flip($exclusions);
        });

        return $this->findPaths($participants, $allExclusions);
    }

    protected function findPaths($nodes, array $allExclusions, array $list = []) : Generator
    {
        yield from $this->findPathsFrom(array_key_first($nodes), $nodes, $allExclusions, $list);
    }

    protected function findPathsFrom($startNode, $allNodesLeft, $allExclusions, $list)
    {
        // All the nodes still accessible without exclusions (no route)
        $availableNodes = array_diff_key($allNodesLeft, [$startNode => 0], Arr::get($allExclusions, $startNode, []));

        if (count($availableNodes) > 0) {
            while (count($availableNodes) > 0) {
                // Pick a random number between 0 and the sum of all weights
                // And then search in the list which one reaches that random weight
                $rand = mt_rand(0, array_sum($availableNodes));
                $nextNode = Arr::first(array_keys($availableNodes), function ($nextNode) use (&$rand, $availableNodes) {
                    return ($rand -= $availableNodes[$nextNode]) <= 0;
                });
                $availableNodes = array_diff_key($availableNodes, [$nextNode => 0]);

                $possibleList = $list + [$startNode => $nextNode];
                $nodesLeft = array_diff_key($allNodesLeft, [$nextNode => 0]);

                if (! isset($list[$nextNode])) {
                    // We need to go deeper
                    yield from $this->findPathsFrom($nextNode, $nodesLeft, $allExclusions, $possibleList);
                } else if (count($nodesLeft) >= 2) {
                    // Another partial graph is possible
                    yield from $this->findPaths($nodesLeft, $allExclusions, $possibleList);
                } else if (count($nodesLeft) === 0) {
                    // Solution found
                    yield $possibleList;
                } else {
                    // Wrong path, single node alone
                }
            }
        } else {
            // Wrong path, no solution
        }
    }


}
