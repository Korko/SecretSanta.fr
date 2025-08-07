<?php

namespace App\DTOs;

use App\Enums\DrawStatus;

final readonly class DrawCreationRequest
{
    public function __construct(
        public string $title {
            set(string $value) => strlen(trim($value)) >= 3
                ? trim($value)
                : throw new \InvalidArgumentException('Title must be at least 3 characters'),
        },

        public ?string $description {
            set(?string $value) => $value ? trim($value) : null,
        },

        public string $organizerName {
            set(string $value) => strlen(trim($value)) >= 2
                ? trim($value)
                : throw new \InvalidArgumentException('Organizer name must be at least 2 characters'),
        },

        public string $organizerEmail {
            set(string $value) => filter_var($value, FILTER_VALIDATE_EMAIL)
                ? strtolower(trim($value))
                : throw new \InvalidArgumentException('Invalid email address'),
        },

        public ?\DateTime $registrationDeadline {
            set(?\DateTime $value) => $value?->isFuture() !== false
                ? $value
                : throw new \InvalidArgumentException('Registration deadline must be in the future'),
        } = null,

        public bool $autoAcceptParticipants = true,
        public bool $allowTargetMessages = true,

        public DrawStatus $initialStatus {
            set(DrawStatus $value) => in_array($value, [DrawStatus::DRAFT, DrawStatus::OPEN_REGISTRATION])
                ? $value
                : throw new \InvalidArgumentException('Initial status must be draft or open_registration'),
        } = DrawStatus::DRAFT,
    ) {
        $this->validateBusinessRules();
    }

    /**
     * Business logic validation in constructor body
     */
    private function validateBusinessRules(): void
    {
        // Validate deadline with status
        if ($this->initialStatus === DrawStatus::OPEN_REGISTRATION && !$this->registrationDeadline) {
            throw new \InvalidArgumentException('Registration deadline is required when opening registrations immediately');
        }

        // Validate email domain if needed
        $allowedDomains = config('app.allowed_organizer_domains');
        if ($allowedDomains && !empty($allowedDomains)) {
            $domain = substr(strrchr($this->organizerEmail, '@'), 1);
            if (!in_array($domain, $allowedDomains)) {
                throw new \InvalidArgumentException('Email domain not allowed');
            }
        }
    }

    /**
     * Factory method
     */
    public static function fromArray(array $data): self
    {
        return new self(
            title: $data['title'],
            description: $data['description'] ?? null,
            organizerName: $data['organizer_name'],
            organizerEmail: $data['organizer_email'],
            registrationDeadline: isset($data['registration_deadline'])
                ? new \DateTime($data['registration_deadline'])
                : null,
            autoAcceptParticipants: $data['auto_accept_participants'] ?? true,
            allowTargetMessages: $data['allow_target_messages'] ?? true,
            initialStatus: isset($data['status'])
                ? DrawStatus::from($data['status'])
                : DrawStatus::DRAFT,
            ...array_filter($data, fn($key) => in_array($key, ['title', 'description']), ARRAY_FILTER_USE_KEY)
        );
    }

    /**
     * Convert to array for persistence
     */
    public function toArray(): array
    {
        return [
            'title_encrypted' => $this->title, // Will be encrypted by the model
            'description_encrypted' => $this->description,
            'organizer_name_encrypted' => $this->organizerName,
            'organizer_email_encrypted' => $this->organizerEmail,
            'status' => $this->initialStatus,
            'registration_deadline' => $this->registrationDeadline,
            'auto_accept_participants' => $this->autoAcceptParticipants,
            'allow_target_messages' => $this->allowTargetMessages,
        ];
    }
}
