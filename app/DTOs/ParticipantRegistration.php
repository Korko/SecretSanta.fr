<?php

namespace App\DTOs;

use App\Enums\ParticipantStatus;
use App\Models\Draw\Draw;

/**
 * Constructor for Participant Registration
 */
final readonly class ParticipantRegistration
{
    /**
     * Constructor with property hooks and validation
     */
    public function __construct(
        public Draw $draw,

        public string $name {
            set(string $value) => $this->validateName($value),
        },

        public string $email {
            set(string $value) => $this->validateEmail($value),
        },

        public ParticipantStatus $initialStatus {
            set(ParticipantStatus $value) => $value === ParticipantStatus::PENDING
                || ($this->draw->auto_accept_participants && $value === ParticipantStatus::ACCEPTED)
                    ? $value
                    : throw new \InvalidArgumentException('Invalid initial participant status'),
        } = ParticipantStatus::PENDING,

        public bool $isOrganizer = false,

        public ?string $invitationKey {
            set(?string $value) => $value && strlen($value) >= 8
                ? $value
                : ($value ? throw new \InvalidArgumentException('Invitation key too short') : null),
        } = null,
    ) {
        $this->validateDrawState();
        $this->validateUniqueEmail();
    }

    /**
     * Name validation with enhanced rules
     */
    private function validateName(string $name): string
    {
        $cleaned = trim($name);

        if (strlen($cleaned) < 2) {
            throw new \InvalidArgumentException('Name must be at least 2 characters');
        }

        if (strlen($cleaned) > 50) {
            throw new \InvalidArgumentException('Name cannot exceed 50 characters');
        }

        // Check for valid characters (letters, spaces, hyphens, apostrophes)
        if (!preg_match("/^[a-zA-ZÀ-ÿ\s\-']+$/u", $cleaned)) {
            throw new \InvalidArgumentException('Name contains invalid characters');
        }

        return $cleaned;
    }

    /**
     * Email validation with business rules
     */
    private function validateEmail(string $email): string
    {
        $cleaned = strtolower(trim($email));

        if (!filter_var($cleaned, FILTER_VALIDATE_EMAIL)) {
            throw new \InvalidArgumentException('Invalid email format');
        }

        // Check against blocked domains
        $blockedDomains = config('app.blocked_email_domains', []);
        $domain = substr(strrchr($cleaned, '@'), 1);

        if (in_array($domain, $blockedDomains)) {
            throw new \InvalidArgumentException('Email domain is not allowed');
        }

        return $cleaned;
    }

    /**
     * Validate draw can accept registrations
     */
    private function validateDrawState(): void
    {
        if (!$this->draw->canAcceptRegistrations()) {
            throw new \InvalidArgumentException('Draw is not accepting registrations');
        }
    }

    /**
     * Validate email uniqueness within draw
     */
    private function validateUniqueEmail(): void
    {
        $existingParticipant = $this->draw->participants()
            ->whereRaw('SHA2(LOWER(email_encrypted), 256) = ?', [hash('sha256', $this->email)])
            ->exists();

        if ($existingParticipant) {
            throw new \InvalidArgumentException('Email already registered for this draw');
        }
    }

    /**
     * Factory method for invitation-based registration
     */
    public static function fromInvitation(Draw $draw, string $invitationKey, array $data): self
    {
        return new self(
            draw: $draw,
            name: $data['name'],
            email: $data['email'],
            initialStatus: $draw->auto_accept_participants
                ? ParticipantStatus::ACCEPTED
                : ParticipantStatus::PENDING,
            isOrganizer: false,
            invitationKey: $invitationKey,
        );
    }

    /**
     * Factory method for organizer registration
     */
    public static function createOrganizer(Draw $draw, string $name, string $email): self
    {
        return new self(
            draw: $draw,
            name: $name,
            email: $email,
            initialStatus: ParticipantStatus::ACCEPTED,
            isOrganizer: true,
        );
    }

    /**
     * Convert to model attributes
     */
    public function toModelAttributes(string $masterKey): array
    {
        return [
            'draw_id' => $this->draw->id,
            'name_encrypted' => $this->name, // Will be encrypted by the model
            'email_encrypted' => $this->email,
            'status' => $this->initialStatus,
            'is_organizer' => $this->isOrganizer,
            'uuid' => \Illuminate\Support\Str::uuid(),
        ];
    }
}
