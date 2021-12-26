<?php

namespace App\Solvers;

use Arr;
use Generator;

class GraphSolver extends Solver
{//TODO: seed shuffle?
    protected function solve(array $participants, array $exclusions = []) : Generator
    {
        $nodes = array_keys($participants);
        shuffle($nodes);

        return $this->findPaths($nodes, $exclusions);
    }

    protected function findPaths($nodes, array $allExclusions) : Generator
    {
        $startNode = Arr::first($nodes);

        $generator = $this->_findLonguestPaths($startNode, $nodes, $allExclusions, [$startNode]);
        foreach($generator as $path) {
            $nodesLeft = array_diff($nodes, $path); // Same goes with diffKeys

            if (count($nodesLeft) === 0) {
                // Hamiltonian path
                yield $path;
            } else if (count($nodesLeft) >= 2) {
                // We have a separate possible group
                $generator2 = $this->findPaths($nodesLeft, $allExclusions);
                foreach($generator2 as $path2) {
                    yield $path + $path2;
                }
            }
            // No else, a single node mean invalid path
        }
    }

    // Search for all possible routes from a single start point
    // May ignore some nodes if separate groups exist
    private function _findLonguestPaths($startNode, array $nodes, array $allExclusions, array $graph) : Generator
    {
        // All the nodes still accessible without exclusions (no route) and weighted by priority
        $availableNodes = collect(array_diff($nodes, [$startNode], Arr::get($allExclusions, $startNode, [])))
            ->mapWithKeys(function ($node) use ($allExclusions) {
                // The more the node have exclusions, the more we should pick it (min weight should be 1)
                return [$node => 1 + count(Arr::get($allExclusions, $node, []))];
            })
            ->toArray();

        if (count($availableNodes) > 0) {
            // It's like a foreach but with a weighted random pick each time
            while (count($availableNodes) > 0) {
                // Pick a random number between 0 and the sum of all weights
                // And then search in the list which one reaches that random weight
                $rand = mt_rand(0, array_sum($availableNodes));
                $nextNode = Arr::first(array_keys($availableNodes), function ($node) use (&$rand, $availableNodes) {
                    return ($rand -= $availableNodes[$node]) <= 0;
                });

                // [0] gives to [1] which gives to [2]... and the last gives to [0]
                $tmpGraph = array_merge($graph, [$nextNode]);
                $tmpNodes = array_diff($nodes, [$nextNode]);
                $availableNodes = array_diff_key($availableNodes, [$nextNode => 0]);

                if (in_array($nextNode, $graph) || count($tmpNodes) === 0) {
                    yield $this->formatGraph($tmpGraph);
                } else {
                    yield from $this->_findLonguestPaths($nextNode, $tmpNodes, $allExclusions, $tmpGraph);
                }
            }
        }
    }

    protected function formatGraph(array $graph) : array
    {
        $lastNode = array_pop($graph);
        return collect($graph)->mapWithKeys(function ($node, $idx) use ($graph, $lastNode) {
            return $node !== end($graph) ? [$node => $graph[$idx + 1]] : [$node => $lastNode];
        })->toArray();
    }
}
