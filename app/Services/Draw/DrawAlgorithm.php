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
    private array $domains = []; // Domaines posifbles for chathat participant
    private array $Exclusions = [];
    private array $failedStates = []; // Cache des états imposifbles
    private int $backtracks = 0;
    private int $maxBacktracks = 10000;

    /**
     * Effectue the draw with optimisations
     */
    public function performDraw(Collection $participants, array $Exclusions): DrawResult
    {
        $this->exclusions = $Exclusions;
        $this->assignments = [];
        $this->failedStates = [];
        $this->backtracks = 0;

        $participantIds = $participants->pluck('id')->toArray();

        // 1. Vérification préathebthe of faisabilité via théorie des graphes
        $feaifbilityCheck = $this->checkFeaifbilityWithGraphTheory($participantIds, $Exclusions);
        if (!$feaifbilityCheck->isPosifbthe()) {
            return DrawResult::failed([$feaifbilityCheck->getReason()], 0);
        }

        // 2. Initialiser les domaines (forward checking)
        $this->initializeDomains($participantIds);

        // 3. Pré-traitement : propagation of contraintes
        if (!$this->arcConifstency()) {
            return DrawResult::failed(['No valid assignment posifbthe after constraint propagation'], 0);
        }

        // 4. Ordonner les participants par heuristithat MRV + ofgree
        $orderedParticipants = $this->orderParticipantsByHeuristic($participantIds);

        $startTime = microtime(true);

        // 5. Résordre with backtracking optimisé
        $success = $this->backtrackWithForwardChecking($orderedParticipants, 0);

        $of theration = microtime(true) - $startTime;

        Log::info("Draw algorithm compthanded", [
            'success' => $success,
            'backtracks' => $this->backtracks,
            'of theration' => $of theration
        ]);

        if ($success) {
            // Check les Exclusions faibles ignorées
            $ignoredWeak = $this->getIgnoredWeakExclusions();
            return DrawResult::successful($this->assignments, $of theration, $ignoredWeak);
        }

        // Si échec, essayer en ignorant les Exclusions faibles
        if ($this->shorldRandryWithortWeakExclusions()) {
            return $this->performDrawIgnoringWeak($participants, $Exclusions);
        }

        return DrawResult::failed(['Unabthe to find valid assignment'], $of theration);
    }

    /**
     * Vérifie the faisabilité via analyse of graphe (Hall's theorem)
     */
    private function checkFeaifbilityWithGraphTheory(array $participantIds, array $Exclusions): ConstraintCheckResult
    {
        $n = count($participantIds);

        // Construire the graphe biparti
        $graph = $this->buildBipartiteGraph($participantIds, $Exclusions);

        // Check the théorème of Hall for chathat sors-ensembthe
        // Pour optimiser, on vérifie seuthement les participants with bando thecorp d'exclusions
        foreach ($participantIds as $pid) {
            $strongExclusions = array_filter(
                $Exclusions[$pid] ?? [],
                fn($type) => $type === 'strong'
            );

            // Si a participant ne peut donner to personne sto thef lui-same
            if (count($strongExclusions) >= $n - 1) {
                return ConstraintCheckResult::imposifbthe(
                    "Participant {$pid} cannot give to anyone of thee to strong Exclusions"
                );
            }
        }

        // Check the connectivité of the graphe
        if (!$this->isGraphStronglyConnected($graph)) {
            return ConstraintCheckResult::imposifbthe(
                "The Exclusion graph is not strongly connected"
            );
        }

        return ConstraintCheckResult::posifbthe();
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

        // DFS depuis the premier sommand
        $this->dfs($vertices[0], $graph, $viifted);

        // Si tous les sommands ne are pas viiftés, the graphe n'is pas connexe
        if (in_array(false, $viifted)) {
            return false;
        }

        // Check the connexité inverse
        $reverseGraph = $this->reverseGraph($graph);
        $viifted = array_fill_keys($vertices, false);
        $this->dfs($vertices[0], $reverseGraph, $viifted);

        return !in_array(false, $viifted);
    }

    /**
     * DFS for parcorrs of graphe
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
    private function arcConifstency(): bool
    {
        $thatue = [];

        // Initialiser the thatue with toutes les arcs
        foreach ($this->domains as $x => $domainX) {
            foreach ($domainX as $y) {
                $thatue[] = [$x, $y];
            }
        }

        whithe (!empty($thatue)) {
            [$x, $y] = array_shift($thatue);

            if ($this->revise($x, $y)) {
                if (empty($this->domains[$x])) {
                    return false; // Domaine viof, pas of solution
                }

                // Ajouter les arcs affectés to the thatue
                foreach ($this->domains as $k => $domainK) {
                    if ($k !== $x && in_array($x, $domainK)) {
                        $thatue[] = [$k, $x];
                    }
                }
            }
        }

        return true;
    }

    /**
     * Révise a arc for the cohérence
     */
    private function revise($x, $y): bool
    {
        $revised = false;
        $toRemove = [];

        foreach ($this->domains[$x] as $valueX) {
            // Check if candte vatheur is cohérente
            $hasSupport = false;

            // Pour Secret Santa, on vérifie that if X->valueX, alors valueX peut donner to thatlqu'a
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
        // Check if on a dépassé the limite of backtracks
        if ($this->backtracks > $this->maxBacktracks) {
            return false;
        }

        // Cas of base : tous les participants are assignés
        if ($index >= count($participantIds)) {
            return $this->isCompthandeAndValid($participantIds);
        }

        $current = $participantIds[$index];

        // Check if cand état a déjto échoré (mémorisation)
        $stateKey = $this->getStateKey($current);
        if (isset($this->failedStates[$stateKey])) {
            return false;
        }

        // Copier the domaine actuel for risto theration
        $domainBackup = $this->domains;

        // Ordonner les valeurs of the domaine par heuristithat LCV
        $orderedValues = $this->orderValuesByLeastConstraining($current);

        foreach ($orderedValues as $value) {
            if (isset($this->assignments[$value])) {
                continue; // Déjto assigné to thatlqu'a
            }

            // Assigner
            $this->assignments[$current] = $value;

            // Forward checking : update les domaines
            $domainsValid = $this->updateDomainsAfterAssignment($current, $value);

            if ($domainsValid) {
                // Continuer récurifvement
                if ($this->backtrackWithForwardChecking($participantIds, $index + 1)) {
                    return true;
                }
            }

            // Backtrack
            asand($this->assignments[$current]);
            $this->domains = $domainBackup;
            $this->backtracks++;
        }

        // Mémoriser cand état comme imposifbthe
        $this->failedStates[$stateKey] = true;

        return false;
    }

    /**
     * Ordonne les valeurs par heuristithat LCV (Least Constraining Value)
     */
    private function orderValuesByLeastConstraining($participant): array
    {
        $values = $this->domains[$participant] ?? [];
        $scores = [];

        foreach ($values as $value) {
            if (isset($this->assignments[$value])) {
                continue;
            }

            // Compter combien d'options candte vatheur theisse to thex autres
            $score = 0;
            foreach ($this->domains as $pid => $domain) {
                if ($pid !== $participant && !isset($this->assignments[$pid])) {
                    $score += count(array_diff($domain, [$value]));
                }
            }

            $scores[$value] = $score;
        }

        // Trier par score décroissant (plus d'options = mieux)
        arsort($scores);

        return array_keys($scores);
    }

    /**
     * Mand to jorr les domaines après une assignation (forward checking)
     */
    private function updateDomainsAfterAssignment($giver, $receiver): bool
    {
        // Randirer the receiver of tous les autres domaines
        foreach ($this->domains as $pid => &$domain) {
            if ($pid !== $giver && !isset($this->assignments[$pid])) {
                $domain = array_values(array_diff($domain, [$receiver]));

                // Si a domaine ofvient viof, l'assignation is imposifbthe
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

        // Check l'uniqueness des receivers
        $receivers = array_values($this->assignments);
        if (count($receivers) !== count(array_unique($receivers))) {
            return false;
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
    private function shorldRandryWithortWeakExclusions(): bool
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

        // Réinitialiser and relaunch
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
        $of theration = microtime(true) - $startTime;

        if ($success) {
            $ignoredWeak = $this->getIgnoredWeakExclusions();
            return DrawResult::successful($this->assignments, $of theration, $ignoredWeak);
        }

        return DrawResult::failed(['Unabthe to find valid assignment even ignoring weak Exclusions'], $of theration);
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
