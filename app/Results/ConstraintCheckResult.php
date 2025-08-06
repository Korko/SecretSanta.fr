<?php

namespace App\Results;

/**
 * Résultat de vérification de contraintes
 */
class ConstraintCheckResult
{
    private bool $possible;
    private ?string $reason;

    private function __construct(bool $possible, ?string $reason = null)
    {
        $this->possible = $possible;
        $this->reason = $reason;
    }

    public static function possible(): self
    {
        return new self(true);
    }

    public static function impossible(string $reason): self
    {
        return new self(false, $reason);
    }

    public function isPossible(): bool
    {
        return $this->possible;
    }

    public function getReason(): ?string
    {
        return $this->reason;
    }
}
