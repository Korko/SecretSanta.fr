<?php

namespace App\Enums;

enum DrawStatus: string
{
    case DRAFT = 'draft';
    case OPEN_REGISTRATION = 'open_registration';
    case CLOSED_REGISTRATION = 'closed_registration';
    case DRAWN = 'drawn';
    case REVEALED = 'revealed';
    case ARCHIVED = 'archived';

    public function canAcceptRegistrations(): bool
    {
        return match($this) {
            self::OPEN_REGISTRATION => true,
            default => false
        };
    }

    public function isComplete(): bool
    {
        return match($this) {
            self::DRAWN, self::REVEALED => true,
            default => false
        };
    }

    public function canTransitionTo(self $newStatus): bool
    {
        return match($this) {
            self::DRAFT => in_array($newStatus, [self::OPEN_REGISTRATION, self::ARCHIVED]),
            self::OPEN_REGISTRATION => in_array($newStatus, [self::CLOSED_REGISTRATION, self::ARCHIVED]),
            self::CLOSED_REGISTRATION => in_array($newStatus, [self::DRAWN, self::OPEN_REGISTRATION]),
            self::DRAWN => in_array($newStatus, [self::REVEALED]),
            self::REVEALED => in_array($newStatus, [self::ARCHIVED]),
            self::ARCHIVED => false,
        };
    }

    public function getDisplayName(): string
    {
        return match($this) {
            self::DRAFT => 'Brouillon',
            self::OPEN_REGISTRATION => 'Inscriptions ouvertes',
            self::CLOSED_REGISTRATION => 'Inscriptions fermées',
            self::DRAWN => 'Tirage effectué',
            self::REVEALED => 'Résultats révélés',
            self::ARCHIVED => 'Archivé',
        };
    }

    public static function getActiveStatuses(): array
    {
        return [...[self::DRAFT, self::OPEN_REGISTRATION, self::CLOSED_REGISTRATION, self::DRAWN, self::REVEALED]];
    }
}
