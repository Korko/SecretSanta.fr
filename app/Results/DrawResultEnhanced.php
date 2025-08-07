<?php

namespace App\Results;

/**
 * PHP 8.4 Enhanced Result class with property hooks and asymmetric visibility
 */
final readonly class DrawResultEnhanced
{
    /**
     * PHP 8.4 Asymmetric visibility for result data
     */
    public private(set) bool $isSuccessful;
    public private(set) array $assignments;
    public private(set) array $errors;
    public private(set) float $executionTime;
    public private(set) array $ignoredWeakExclusions;
    public private(set) array $statistics;
    public private(set) \DateTime $timestamp;
    
    /**
     * PHP 8.4 Enhanced constructor with comprehensive result building
     */
    private function __construct(
        bool $successful,
        array $assignments = [],
        array $errors = [],
        float $executionTime = 0.0,
        array $ignoredWeakExclusions = [],
        array $additionalData = []
    ) {
        $this->isSuccessful = $successful;
        $this->assignments = $assignments;
        $this->errors = $errors;
        $this->executionTime = $executionTime;
        $this->ignoredWeakExclusions = $ignoredWeakExclusions;
        $this->timestamp = new \DateTime();
        $this->statistics = $this->calculateStatistics($assignments, $errors, $additionalData);
    }
    
    /**
     * Calculate comprehensive statistics
     */
    private function calculateStatistics(array $assignments, array $errors, array $additionalData): array
    {
        $participantCount = count($assignments);
        
        return [
            'participant_count' => $participantCount,
            'assignment_count' => count(array_filter($assignments)),
            'error_count' => count($errors),
            'success_rate' => $participantCount > 0 ? (count(array_filter($assignments)) / $participantCount) * 100 : 0,
            'has_circular_assignments' => $this->hasCircularAssignments($assignments),
            'max_chain_length' => $this->calculateMaxChainLength($assignments),
            'weak_exclusions_ignored' => count($this->ignoredWeakExclusions),
            'execution_time_ms' => round($this->executionTime * 1000, 2),
            'performance_rating' => $this->calculatePerformanceRating($this->executionTime, $participantCount),
            'complexity_score' => $additionalData['complexity_score'] ?? 0,
            'backtrack_count' => $additionalData['backtrack_count'] ?? 0,
        ];
    }
    
    /**
     * Check for circular assignments
     */
    private function hasCircularAssignments(array $assignments): bool
    {
        foreach ($assignments as $giver => $receiver) {
            if (isset($assignments[$receiver]) && $assignments[$receiver] == $giver) {
                return true;
            }
        }
        return false;
    }
    
    /**
     * Calculate maximum chain length
     */
    private function calculateMaxChainLength(array $assignments): int
    {
        $maxLength = 0;
        $visited = [];
        
        foreach (array_keys($assignments) as $start) {
            if (isset($visited[$start])) {
                continue;
            }
            
            $length = 0;
            $current = $start;
            
            while (isset($assignments[$current]) && !isset($visited[$current])) {
                $visited[$current] = true;
                $current = $assignments[$current];
                $length++;
                
                if ($current == $start) { // Full circle
                    break;
                }
            }
            
            $maxLength = max($maxLength, $length);
        }
        
        return $maxLength;
    }
    
    /**
     * Calculate performance rating
     */
    private function calculatePerformanceRating(float $executionTime, int $participantCount): string
    {
        if ($participantCount === 0) return 'N/A';
        
        $timePerParticipant = $executionTime / $participantCount;
        
        return match(true) {
            $timePerParticipant < 0.001 => 'Excellent',
            $timePerParticipant < 0.01 => 'Good',
            $timePerParticipant < 0.1 => 'Average',
            $timePerParticipant < 1.0 => 'Poor',
            default => 'Very Poor'
        };
    }
    
    /**
     * PHP 8.4 Factory methods for different result types
     */
    public static function successful(
        array $assignments,
        float $executionTime = 0.0,
        array $ignoredWeakExclusions = [],
        array $additionalData = []
    ): self {
        return new self(
            successful: true,
            assignments: $assignments,
            errors: [],
            executionTime: $executionTime,
            ignoredWeakExclusions: $ignoredWeakExclusions,
            additionalData: $additionalData
        );
    }
    
    public static function failed(
        array $errors,
        float $executionTime = 0.0,
        array $partialAssignments = [],
        array $additionalData = []
    ): self {
        return new self(
            successful: false,
            assignments: $partialAssignments,
            errors: $errors,
            executionTime: $executionTime,
            ignoredWeakExclusions: [],
            additionalData: $additionalData
        );
    }
    
    /**
     * Enhanced validation methods
     */
    public function isValid(): bool
    {
        if (!$this->isSuccessful) {
            return false;
        }
        
        // Check for self-assignments
        foreach ($this->assignments as $giver => $receiver) {
            if ($giver === $receiver) {
                return false;
            }
        }
        
        // Check for duplicate receivers
        $receivers = array_values($this->assignments);
        if (count($receivers) !== count(array_unique($receivers))) {
            return false;
        }
        
        return true;
    }
    
    /**
     * Get detailed result information
     */
    public function getSummary(): array
    {
        return [
            'success' => $this->isSuccessful,
            'valid' => $this->isValid(),
            'statistics' => $this->statistics,
            'execution_time' => $this->executionTime,
            'timestamp' => $this->timestamp->format('Y-m-d H:i:s'),
            'summary' => $this->getTextSummary(),
        ];
    }
    
    public function getTextSummary(): string
    {
        if (!$this->isSuccessful) {
            return sprintf(
                'Draw failed with %d error(s) in %.3f seconds',
                count($this->errors),
                $this->executionTime
            );
        }
        
        $summary = sprintf(
            'Draw completed successfully with %d assignments in %.3f seconds',
            count($this->assignments),
            $this->executionTime
        );
        
        if (!empty($this->ignoredWeakExclusions)) {
            $summary .= sprintf(' (%d weak exclusions ignored)', count($this->ignoredWeakExclusions));
        }
        
        return $summary;
    }
    
    /**
     * Export methods
     */
    public function toArray(): array
    {
        return [
            'successful' => $this->isSuccessful,
            'assignments' => $this->assignments,
            'errors' => $this->errors,
            'execution_time' => $this->executionTime,
            'ignored_weak_exclusions' => $this->ignoredWeakExclusions,
            'statistics' => $this->statistics,
            'timestamp' => $this->timestamp->format('Y-m-d H:i:s'),
        ];
    }
}