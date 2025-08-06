<?php

namespace App\Results;

/**
 * Résultat d'un tirage au sort
 */
class DrawResult
{
    private bool $successful;
    private array $assignments;
    private array $errors;
    private ?float $duration;
    private array $ignoredWeakExclusions;

    private function __construct(
        bool $successful,
        array $assignments = [],
        array $errors = [],
        ?float $duration = null,
        array $ignoredWeakExclusions = []
    ) {
        $this->successful = $successful;
        $this->assignments = $assignments;
        $this->errors = $errors;
        $this->duration = $duration;
        $this->ignoredWeakExclusions = $ignoredWeakExclusions;
    }

    public static function successful(array $assignments, ?float $duration = null, array $ignoredWeakExclusions = []): self
    {
        return new self(true, $assignments, [], $duration, $ignoredWeakExclusions);
    }

    public static function failed(array $errors, ?float $duration = null): self
    {
        return new self(false, [], $errors, $duration);
    }

    public function isSuccessful(): bool
    {
        return $this->successful;
    }

    public function getAssignments(): array
    {
        return $this->assignments;
    }

    public function getErrors(): array
    {
        return $this->errors;
    }

    public function getFailureReason(): string
    {
        return implode(', ', $this->errors);
    }

    public function getDuration(): ?float
    {
        return $this->duration;
    }

    public function hasIgnoredWeakExclusions(): bool
    {
        return !empty($this->ignoredWeakExclusions);
    }

    public function getIgnoredWeakExclusions(): array
    {
        return $this->ignoredWeakExclusions;
    }

    /**
     * Génère un message de résumé
     */
    public function getSummary(): string
    {
        if (!$this->successful) {
            return 'Draw failed: ' . $this->getFailureReason();
        }

        $summary = sprintf(
            'Draw completed successfully with %d assignments in %.2f seconds',
            count($this->assignments),
            $this->duration ?? 0
        );

        if ($this->hasIgnoredWeakExclusions()) {
            $summary .= '. Some optional exclusions were ignored.';
        }

        return $summary;
    }
}
