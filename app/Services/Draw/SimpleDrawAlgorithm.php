<?php

namespace App\Services\Draw;

use App\Results\DrawResult;
use Illuminate\Support\Collection;

/**
 * Simplified Draw Algorithm for basic cases
 */
class SimpleDrawAlgorithm
{
    public function performDraw(Collection $participants, array $exclusions): DrawResult
    {
        $participantIds = $participants->pluck('id')->toArray();
        $n = count($participantIds);
        
        // Handle case with too few participants
        if ($n < 2) {
            return DrawResult::failed(['Need at least 2 participants']);
        }
        
        $startTime = microtime(true);
        $assignments = [];
        
        // Simple algorithm: try random assignments with exclusion checks
        $maxAttempts = 1000;
        $attempts = 0;
        
        while ($attempts < $maxAttempts) {
            $assignments = [];
            $availableReceivers = $participantIds;
            $success = true;
            
            foreach ($participantIds as $giver) {
                $possibleReceivers = array_filter($availableReceivers, function($receiver) use ($giver, $exclusions) {
                    // Can't assign to self
                    if ($giver === $receiver) {
                        return false;
                    }
                    
                    // Check for strong exclusions
                    if (isset($exclusions[$giver][$receiver]) && $exclusions[$giver][$receiver] === 'strong') {
                        return false;
                    }
                    
                    return true;
                });
                
                if (empty($possibleReceivers)) {
                    $success = false;
                    break;
                }
                
                // Pick a random receiver from possible ones
                $receiver = $possibleReceivers[array_rand($possibleReceivers)];
                $assignments[$giver] = $receiver;
                
                // Remove assigned receiver from available pool
                $availableReceivers = array_filter($availableReceivers, fn($r) => $r !== $receiver);
            }
            
            if ($success && $this->validateAssignments($assignments, $participantIds, $exclusions)) {
                $duration = microtime(true) - $startTime;
                return DrawResult::successful($assignments, $duration);
            }
            
            $attempts++;
        }
        
        $duration = microtime(true) - $startTime;
        return DrawResult::failed(['Unable to find valid assignment after ' . $maxAttempts . ' attempts'], $duration);
    }
    
    private function validateAssignments(array $assignments, array $participantIds, array $exclusions): bool
    {
        // Check that every participant gives and receives exactly once
        $receivers = array_values($assignments);
        
        if (count($assignments) !== count($participantIds)) {
            return false;
        }
        
        if (count(array_unique($receivers)) !== count($receivers)) {
            return false;
        }
        
        // Check no self-assignments
        foreach ($assignments as $giver => $receiver) {
            if ($giver === $receiver) {
                return false;
            }
        }
        
        // Check no strong exclusions are violated
        foreach ($assignments as $giver => $receiver) {
            if (isset($exclusions[$giver][$receiver]) && $exclusions[$giver][$receiver] === 'strong') {
                return false;
            }
        }
        
        return true;
    }
}