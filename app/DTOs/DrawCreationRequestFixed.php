<?php

namespace App\DTOs;

use App\Enums\DrawStatus;

/**
 * Constructor with readonly and validation
 */
final readonly class DrawCreationRequestFixed
{
    /**
     * Constructor with enhanced property promotion
     */
    public function __construct(
        public string $title,
        public ?string $description,
        public string $organizerName,
        public string $organizerEmail,
        public ?\DateTime $registrationDeadline = null,
        public bool $autoAcceptParticipants = true,
        public bool $allowTargetMessages = true,
        public DrawStatus $initialStatus = DrawStatus::DRAFT,
    ) {
        // Constructor validation
        $this->validateTitle($this->title);
        $this->validateOrganizerName($this->organizerName);
        $this->validateOrganizerEmail($this->organizerEmail);
        $this->validateRegistrationDeadline($this->registrationDeadline);
        $this->validateInitialStatus($this->initialStatus);
        $this->validateBusinessRules();
    }

    /**
     * Validation methods
     */
    private function validateTitle(string $title): void
    {
        $cleaned = trim($title);
        if (strlen($cleaned) < 3) {
            throw new \InvalidArgumentException('Title must be at least 3 characters');
        }
        if (strlen($cleaned) > 100) {
            throw new \InvalidArgumentException('Title cannot exceed 100 characters');
        }
    }

    private function validateOrganizerName(string $name): void
    {
        $cleaned = trim($name);
        if (strlen($cleaned) < 2) {
            throw new \InvalidArgumentException('Organizer name must be at least 2 characters');
        }
        if (strlen($cleaned) > 50) {
            throw new \InvalidArgumentException('Organizer name cannot exceed 50 characters');
        }
    }

    private function validateOrganizerEmail(string $email): void
    {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new \InvalidArgumentException('Invalid email address');
        }
    }

    private function validateRegistrationDeadline(?\DateTime $deadline): void
    {
        if ($deadline && $deadline <= new \DateTime()) {
            throw new \InvalidArgumentException('Registration deadline must be in the future');
        }
    }

    private function validateInitialStatus(DrawStatus $status): void
    {
        if (!in_array($status, [DrawStatus::DRAFT, DrawStatus::OPEN_REGISTRATION])) {
            throw new \InvalidArgumentException('Initial status must be draft or open_registration');
        }
    }

    private function validateBusinessRules(): void
    {
        // Validate deadline with status
        if ($this->initialStatus === DrawStatus::OPEN_REGISTRATION && !$this->registrationDeadline) {
            throw new \InvalidArgumentException('Registration deadline is required when opening registrations immediately');
        }
    }

    /**
     * Factory methods with named parameters and validation
     */
    public static function createDraft(
        string $title,
        string $organizerName,
        string $organizerEmail,
        ?string $description = null,
        bool $autoAcceptParticipants = true,
        bool $allowTargetMessages = true,
    ): self {
        return new self(
            title: trim($title),
            description: $description ? trim($description) : null,
            organizerName: trim($organizerName),
            organizerEmail: strtolower(trim($organizerEmail)),
            registrationDeadline: null,
            autoAcceptParticipants: $autoAcceptParticipants,
            allowTargetMessages: $allowTargetMessages,
            initialStatus: DrawStatus::DRAFT,
        );
    }

    public static function createWithRegistration(
        string $title,
        string $organizerName,
        string $organizerEmail,
        \DateTime $registrationDeadline,
        ?string $description = null,
        bool $autoAcceptParticipants = true,
        bool $allowTargetMessages = true,
    ): self {
        return new self(
            title: trim($title),
            description: $description ? trim($description) : null,
            organizerName: trim($organizerName),
            organizerEmail: strtolower(trim($organizerEmail)),
            registrationDeadline: $registrationDeadline,
            autoAcceptParticipants: $autoAcceptParticipants,
            allowTargetMessages: $allowTargetMessages,
            initialStatus: DrawStatus::OPEN_REGISTRATION,
        );
    }

    /**
     * Array unpacking with validation
     */
    public static function fromArray(array $data): self
    {
        return new self(
            title: $data['title'] ?? throw new \InvalidArgumentException('Title is required'),
            description: $data['description'] ?? null,
            organizerName: $data['organizer_name'] ?? throw new \InvalidArgumentException('Organizer name is required'),
            organizerEmail: $data['organizer_email'] ?? throw new \InvalidArgumentException('Organizer email is required'),
            registrationDeadline: isset($data['registration_deadline'])
                ? new \DateTime($data['registration_deadline'])
                : null,
            autoAcceptParticipants: $data['auto_accept_participants'] ?? true,
            allowTargetMessages: $data['allow_target_messages'] ?? true,
            initialStatus: isset($data['status'])
                ? DrawStatus::from($data['status'])
                : DrawStatus::DRAFT,
        );
    }

    /**
     * Convert to normalized array
     */
    public function toArray(): array
    {
        return [
            'title' => trim($this->title),
            'description' => $this->description ? trim($this->description) : null,
            'organizer_name' => trim($this->organizerName),
            'organizer_email' => strtolower(trim($this->organizerEmail)),
            'status' => $this->initialStatus->value,
            'registration_deadline' => $this->registrationDeadline?->format('Y-m-d H:i:s'),
            'auto_accept_participants' => $this->autoAcceptParticipants,
            'allow_target_messages' => $this->allowTargetMessages,
        ];
    }

    /**
     * Convert to model attributes with encryption placeholders
     */
    public function toModelAttributes(): array
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
