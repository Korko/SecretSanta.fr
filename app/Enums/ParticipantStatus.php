<?php

namespace App\Enums;

enum ParticipantStatus: string
{
    case PENDING = 'pending';
    case ACCEPTED = 'accepted';
    case REJECTED = 'rejected';

    public function canTransitionTo(self $newStatus): bool
    {
        return match($this) {
            self::PENDING => in_array($newStatus, [self::ACCEPTED, self::REJECTED]),
            self::ACCEPTED => in_array($newStatus, [self::REJECTED]), // Can still be rejected after acceptance
            self::REJECTED => false, // Final state
        };
    }

    public function isActive(): bool
    {
        return $this === self::ACCEPTED;
    }

    public function getDisplayName(): string
    {
        return match($this) {
            self::PENDING => 'En attente',
            self::ACCEPTED => 'Accepté',
            self::REJECTED => 'Rejeté',
        };
    }

    public function getColor(): string
    {
        return match($this) {
            self::PENDING => 'yellow',
            self::ACCEPTED => 'green',
            self::REJECTED => 'red',
        };
    }
}
