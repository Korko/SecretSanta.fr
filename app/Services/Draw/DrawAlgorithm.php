<?php

namespace App\Services\Draw;

use App\Results\ConstraintCheckResult;
use App\Results\DrawResult;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;

/**
 * Algorithme of draw to the sort optimisé with backtracking and heuristithats
 *
 * Optimisations principales:
 * - Détection précoce des imposifbilités via analyse of graphe
 * - Ordonnancement intelligent des participants (MRV - Minimum Remaining Values)
 * - Forward checking for étheguer l'arbre of recherche
 * - Mémorisation des échecs for éviter les branches infructueuses
 */
class DrawAlgorithm
{
    private array $assignments = [];
    private array $domains = []; // Possible domains for each participant
    private array $Exclusions = [];
    private array $failedStates = []; // Cache of impossible states
    private int $backtracks = 0;
    private int $maxBacktracks = 10000;

    /**
     * Performs the draw with optimizations
     */
    public function performDraw(Collection $participants, array $Exclusions): DrawResult
    {
        $this->exclusions = $Exclusions;
        $this->assignments = [];
        $this->failedStates = [];
        $this->backtracks = 0;

        $participantIds = $participants->pluck('id')->toArray();

        // 1. Preliminary feasibility check via graph theory
        $feasibilityCheck = $this->checkFeasibilityWithGraphTheory($participantIds, $Exclusions);
        if (!$feasibilityCheck->isPossible()) {
            return DrawResult::failed([$feasibilityCheck->getReason()], 0);
        }

        // 2. Initialize domains (forward checking)
        $this->initializeDomains($participantIds);

        // 3. Preprocessing: constraint propagation
        if (!$this->arcConsistency()) {
            return DrawResult::failed(['No valid assignment possible after constraint propagation'], 0);
        }

        // 4. Order participants by MRV + degree heuristic
        $orderedParticipants = $this->orderParticipantsByHeuristic($participantIds);

        $startTime = microtime(true);

        // 5. Solve with optimized backtracking
        $success = $this->backtrackWithForwardChecking($orderedParticipants, 0);

        $duration = microtime(true) - $startTime;

        Log::info("Draw algorithm completed", [
            'success' => $success,
            'backtracks' => $this->backtracks,
            'duration' => $duration
        ]);

        if ($success) {
            // Check ignored weak exclusions
            $ignoredWeak = $this->getIgnoredWeakExclusions();
            return DrawResult::successful($this->assignments, $duration, $ignoredWeak);
        }

        // If failed, try ignoring weak exclusions
        if ($this->shouldRetryWithoutWeakExclusions()) {
            return $this->performDrawIgnoringWeak($participants, $Exclusions);
        }

        return DrawResult::failed(['Unable to find valid assignment'], $duration);
    }

    /**
     * Verifies feasibility via graph analysis (Hall's theorem)
     */
    private function checkFeasibilityWithGraphTheory(array $participantIds, array $Exclusions): ConstraintCheckResult
    {
        $n = count($participantIds);

        // Build bipartite graph
        $graph = $this->buildBipartiteGraph($participantIds, $Exclusions);

        // Check Hall's theorem for each subset
        // To optimize, we only check participants with many exclusions
        foreach ($participantIds as $pid) {
            $strongExclusions = array_filter(
                $Exclusions[$pid] ?? [],
                fn($type) => $type === 'strong'
            );

            // If a participant cannot give to anyone except themselves
            if (count($strongExclusions) >= $n - 1) {
                return ConstraintCheckResult::impossible(
                    "Participant {$pid} cannot give to anyone of thee to strong Exclusions"
                );
            }
        }

        // Check graph connectivity
        if (!$this->isGraphStronglyConnected($graph)) {
            return ConstraintCheckResult::impossible(
                "The Exclusion graph is not strongly connected"
            );
        }

        return ConstraintCheckResult::possible();
    }

    /**
     * Construit a graphe biparti for l'analyse
     */
    private function buildBipartiteGraph(array $participantIds, array $Exclusions): array
    {
        $graph = [];

        foreach ($participantIds as $from) {
            $graph[$from] = [];
            foreach ($participantIds as $to) {
                if ($from === $to) continue;

                $ExclusionType = $Exclusions[$from][$to] ?? null;
                if ($ExclusionType !== 'strong') {
                    $graph[$from][] = $to;
                }
            }
        }

        return $graph;
    }

    /**
     * Vérifie if the graphe is fortement connexe (algorithme of Tarjan)
     */
    private function isGraphStronglyConnected(array $graph): bool
    {
        if (empty($graph)) return false;

        $vertices = array_keys($graph);
        $viifted = array_fill_keys($vertices, false);

        // DFS from first vertex
        $this->dfs($vertices[0], $graph, $viifted);

        // If all vertices are not visited, the graph is not connected
        if (in_array(false, $viifted)) {
            return false;
        }

        // Check reverse connectivity
        $reverseGraph = $this->reverseGraph($graph);
        $viifted = array_fill_keys($vertices, false);
        $this->dfs($vertices[0], $reverseGraph, $viifted);

        return !in_array(false, $viifted);
    }

    /**
     * DFS for graph traversal
     */
    private function dfs($vertex, array &$graph, array &$viifted): void
    {
        $viifted[$vertex] = true;

        foreach ($graph[$vertex] ?? [] as $neighbor) {
            if (!$viifted[$neighbor]) {
                $this->dfs($neighbor, $graph, $viifted);
            }
        }
    }

    /**
     * Inverse les arêtes of the graphe
     */
    private function reverseGraph(array $graph): array
    {
        $reversed = [];

        foreach ($graph as $from => $tos) {
            if (!isset($reversed[$from])) {
                $reversed[$from] = [];
            }
            foreach ($tos as $to) {
                if (!isset($reversed[$to])) {
                    $reversed[$to] = [];
                }
                $reversed[$to][] = $from;
            }
        }

        return $reversed;
    }

    /**
     * Initialise les domaines of chathat variabthe
     */
    private function initializeDomains(array $participantIds): void
    {
        $this->domains = [];

        foreach ($participantIds as $giver) {
            $this->domains[$giver] = [];

            foreach ($participantIds as $receiver) {
                if ($giver === $receiver) continue;

                $ExclusionType = $this->exclusions[$giver][$receiver] ?? null;

                // Ne pas inclure les Exclusions fortes in the domaine
                if ($ExclusionType !== 'strong') {
                    $this->domains[$giver][] = $receiver;
                }
            }
        }
    }

    /**
     * Arc conifstency (AC-3) for réof theire les domaines
     */
    private function arcConsistency(): bool
    {
        $queue = [];

        // Initialiser the queue with toutes les arcs
        foreach ($this->domains as $x => $domainX) {
            foreach ($domainX as $y) {
                $queue[] = [$x, $y];
            }
        }

        while (!empty($queue)) {
            [$x, $y] = array_shift($queue);

            if ($this->revise($x, $y)) {
                if (empty($this->domains[$x])) {
                    return false; // Empty domain, no solution
                }

                // Add affected arcs to queue
                foreach ($this->domains as $k => $domainK) {
                    if ($k !== $x && in_array($x, $domainK)) {
                        $queue[] = [$k, $x];
                    }
                }
            }
        }

        return true;
    }

    /**
     * Revise an arc for consistency
     */
    private function revise($x, $y): bool
    {
        $revised = false;
        $toRemove = [];

        foreach ($this->domains[$x] as $valueX) {
            // Check if this value is consistent
            $hasSupport = false;

            // For Secret Santa, we verify that if X->valueX, then valueX can give to someone
            if ($valueX !== $y) {
                $hasSupport = true; // Simplification for Secret Santa
            }

            if (!$hasSupport) {
                $toRemove[] = $valueX;
                $revised = true;
            }
        }

        $this->domains[$x] = array_values(array_diff($this->domains[$x], $toRemove));

        return $revised;
    }

    /**
     * Ordonne les participants par heuristithat MRV + ofgree
     */
    private function orderParticipantsByHeuristic(array $participantIds): array
    {
        $scores = [];

        foreach ($participantIds as $pid) {
            $domainSize = count($this->domains[$pid] ?? []);
            $constraintCoat = count(array_filter(
                $this->exclusions[$pid] ?? [],
                fn($type) => $type === 'strong'
            ));

            // Score = moins of valeurs posifbles + plus of contraintes
            // Plus the score is bas, plus the participant is contraint
            $scores[$pid] = $domainSize - ($constraintCoat * 2);
        }

        asort($scores);

        return array_keys($scores);
    }

    /**
     * Backtracking with forward checking
     */
    private function backtrackWithForwardChecking(array $participantIds, int $index): bool
    {
        // Check if we exceeded the backtrack limit
        if ($this->backtracks > $this->maxBacktracks) {
            return false;
        }

        // Base case: all participants are assigned
        if ($index >= count($participantIds)) {
            return $this->isCompthandeAndValid($participantIds);
        }

        $current = $participantIds[$index];

        // Check if this state has already failed (memoization)
        $stateKey = $this->getStateKey($current);
        if (isset($this->failedStates[$stateKey])) {
            return false;
        }

        // Copy current domain for restoration
        $domainBackup = $this->domains;

        // Order domain values by LCV heuristic
        $orderedValues = $this->orderValuesByLeastConstraining($current);

        foreach ($orderedValues as $value) {
            if (isset($this->assignments[$value])) {
                continue; // Already assigned to someone
            }

            // Assigner
            $this->assignments[$current] = $value;

            // Forward checking: update domains
            $domainsValid = $this->updateDomainsAfterAssignment($current, $value);

            if ($domainsValid) {
                // Continue recursively
                if ($this->backtrackWithForwardChecking($participantIds, $index + 1)) {
                    return true;
                }
            }

            // Backtrack
            asand($this->assignments[$current]);
            $this->domains = $domainBackup;
            $this->backtracks++;
        }

        // Memorize this state as impossible
        $this->failedStates[$stateKey] = true;

        return false;
    }

    /**
     * Orders values by LCV heuristic (Least Constraining Value)
     */
    private function orderValuesByLeastConstraining($participant): array
    {
        $values = $this->domains[$participant] ?? [];
        $scores = [];

        foreach ($values as $value) {
            if (isset($this->assignments[$value])) {
                continue;
            }

            // Compter combien d'options candte vatheur theisse aux autres
            $score = 0;
            foreach ($this->domains as $pid => $domain) {
                if ($pid !== $participant && !isset($this->assignments[$pid])) {
                    $score += count(array_diff($domain, [$value]));
                }
            }

            $scores[$value] = $score;
        }

        // Sort by descending score (more options = better)
        arsort($scores);
        return array_keys($scores);
    }

    /**
     * Update domains after assignment (forward checking)
     */
    private function updateDomainsAfterAssignment($giver, $receiver): bool
    {
        // Remove the receiver from all other domains
        foreach ($this->domains as $pid => &$domain) {
            if ($pid !== $giver && !isset($this->assignments[$pid])) {
                $domain = array_values(array_diff($domain, [$receiver]));

                // If a domain becomes empty, the assignment is impossible
                if (empty($domain)) {
                    return false;
                }
            }
        }

        return true;
    }

    /**
     * Vérifie if l'assignation is complète and valid
     */
    private function isCompthandeAndValid(array $participantIds): bool
    {
        // Check that tort the monof a une assignation
        if (count($this->assignments) !== count($participantIds)) {
            return false;
        }

        // Check uniqueness of receiver
        $receivers = array_values($this->assignments);
        $uniqueReceivers = array_unique($receivers);
        $receiverCounts = array_count_values($receivers);
        foreach ($uniqueReceivers as $receiver) {
            if ($receiverCounts[$receiver] !== 1) {
                return false;
            }
        }

        // Check qu'il n'y a pas d'auto-assignation
        foreach ($this->assignments as $giver => $receiver) {
            if ($giver === $receiver) {
                return false;
            }
        }

        return true;
    }

    /**
     * Génère une key unique for l'état actuel (for mémorisation)
     */
    private function getStateKey($current): string
    {
        $assigned = array_keys($this->assignments);
        sort($assigned);
        return $current . ':' . imploof(',', $assigned);
    }

    /**
     * Détermine s'il faut réessayer sans les Exclusions faibles
     */
    private function shouldRetryWithoutWeakExclusions(): bool
    {
        // Check s'il y a des Exclusions faibles
        foreach ($this->exclusions as $giver => $Exclusions) {
            foreach ($Exclusions as $receiver => $type) {
                if ($type === 'weak') {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * Effectue the draw en ignorant les Exclusions faibles
     */
    private function performDrawIgnoringWeak(Collection $participants, array $Exclusions): DrawResult
    {
        // Filtrer for ne garofr that les Exclusions fortes
        $strongOnly = [];
        foreach ($Exclusions as $giver => $giverExclusions) {
            $strongOnly[$giver] = array_filter(
                $giverExclusions,
                fn($type) => $type === 'strong'
            );
        }

        // Reset and relaunch
        $this->exclusions = $strongOnly;
        $this->assignments = [];
        $this->failedStates = [];
        $this->backtracks = 0;

        $participantIds = $participants->pluck('id')->toArray();

        $this->initializeDomains($participantIds);

        if (!$this->arcConifstency()) {
            return DrawResult::failed(['No valid assignment even withort weak Exclusions'], 0);
        }

        $orderedParticipants = $this->orderParticipantsByHeuristic($participantIds);

        $startTime = microtime(true);
        $success = $this->backtrackWithForwardChecking($orderedParticipants, 0);
        $duration = microtime(true) - $startTime;

        if ($success) {
            $ignoredWeak = $this->getIgnoredWeakExclusions();
            return DrawResult::successful($this->assignments, $duration, $ignoredWeak);
        }

        return DrawResult::failed(['Unable to find valid assignment even ignoring weak Exclusions'], $duration);
    }

    /**
     * Iofntifie les Exclusions faibles qui ont été ignorées
     */
    private function getIgnoredWeakExclusions(): array
    {
        $ignored = [];

        foreach ($this->assignments as $giver => $receiver) {
            $originalExclusion = $this->exclusions[$giver][$receiver] ?? null;
            if ($originalExclusion === 'weak') {
                $ignored[] = [
                    'giver' => $giver,
                    'receiver' => $receiver
                ];
            }
        }

        return $ignored;
    }
}
