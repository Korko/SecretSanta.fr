<?php

namespace App\Configuration;

use App\Enums\DrawStatus;

/**
 * Configuration Class with constructor hooks and validation
 */
final readonly class DrawConfiguration
{
    /**
     * Asymmetric visibility for configuration values
     */
    public private(set) array $validatedConfig;
    public private(set) \DateTime $createdAt;

    /**
     * Constructor with comprehensive validation and defaults
     */
    public function __construct(
        // Core settings with defaults and validation
        public int $maxParticipants = 100,
        public int $minParticipants = 3,
        public int $maxTitleLength = 100,
        public int $maxDescriptionLength = 1000,
        public int $maxParticipantNameLength = 50,

        // Security settings
        public int $keyExpirationDays = 30,
        public int $sessionTimeoutMinutes = 60,
        public bool $requireEmailVerification = true,
        public bool $enableRateLimiting = true,

        // Feature flags
        public bool $allowMessagesBetweenParticipants = true,
        public bool $allowAnonymousParticipation = false,
        public bool $enableExclusionGroups = true,
        public bool $enableHistoryTracking = true,
        public bool $enableNotifications = true,

        // Timing constraints
        public int $defaultRegistrationPeriodDays = 14,
        public int $maxRegistrationPeriodDays = 365,
        public int $drawProcessingTimeoutSeconds = 300,

        // Exclusion rules
        public int $maxExclusionsPerParticipant = 10,
        public float $maxExclusionRatio = 0.8, // Max 80% of participants can be excluded

        // Array configurations with enhanced validation
        public array $allowedEmailDomains = [],
        public array $blockedEmailDomains = ['example.com', 'test.com'],
        public array $supportedLanguages = ['en', 'fr'],
        public array $allowedFileTypes = ['png', 'jpg', 'jpeg', 'gif'],

        // Advanced settings
        public string $defaultTimezone = 'UTC',
        public string $encryptionAlgorithm = 'AES-256-GCM',
        public ?int $customSeed = null,
    ) {
        $this->createdAt = new \DateTime();
        $this->validateConfiguration();
        $this->validatedConfig = $this->buildValidatedConfig();
    }

    /**
     * Validation with detailed error reporting
     */
    private function validateConfiguration(): void
    {
        $errors = [];

        // Validate participant constraints
        if ($this->maxParticipants <= $this->minParticipants) {
            $errors[] = 'Max participants must be greater than min participants';
        }

        if ($this->minParticipants < 3) {
            $errors[] = 'Minimum participants must be at least 3';
        }

        if ($this->maxParticipants > 10000) {
            $errors[] = 'Maximum participants cannot exceed 10,000';
        }

        // Validate string length constraints
        if ($this->maxTitleLength < 10 || $this->maxTitleLength > 500) {
            $errors[] = 'Max title length must be between 10 and 500 characters';
        }

        if ($this->maxDescriptionLength < 50 || $this->maxDescriptionLength > 5000) {
            $errors[] = 'Max description length must be between 50 and 5,000 characters';
        }

        // Validate timing constraints
        if ($this->defaultRegistrationPeriodDays < 1 || $this->defaultRegistrationPeriodDays > $this->maxRegistrationPeriodDays) {
            $errors[] = 'Default registration period must be between 1 and max registration period';
        }

        if ($this->drawProcessingTimeoutSeconds < 30 || $this->drawProcessingTimeoutSeconds > 3600) {
            $errors[] = 'Draw processing timeout must be between 30 seconds and 1 hour';
        }

        // Validate exclusion constraints
        if ($this->maxExclusionRatio < 0.1 || $this->maxExclusionRatio > 0.9) {
            $errors[] = 'Max exclusion ratio must be between 0.1 and 0.9';
        }

        // Validate arrays
        if (!empty($this->allowedEmailDomains) && !empty($this->blockedEmailDomains)) {
            $overlap = array_intersect($this->allowedEmailDomains, $this->blockedEmailDomains);
            if (!empty($overlap)) {
                $errors[] = 'Allowed and blocked email domains cannot overlap: ' . implode(', ', $overlap);
            }
        }

        // Validate supported languages
        foreach ($this->supportedLanguages as $lang) {
            if (!preg_match('/^[a-z]{2}$/', $lang)) {
                $errors[] = "Invalid language code: {$lang}";
            }
        }

        // Validate timezone
        if (!in_array($this->defaultTimezone, timezone_identifiers_list())) {
            $errors[] = "Invalid timezone: {$this->defaultTimezone}";
        }

        // Validate encryption algorithm
        $supportedAlgorithms = ['AES-256-GCM', 'AES-256-CBC', 'ChaCha20-Poly1305'];
        if (!in_array($this->encryptionAlgorithm, $supportedAlgorithms)) {
            $errors[] = "Unsupported encryption algorithm: {$this->encryptionAlgorithm}";
        }

        if (!empty($errors)) {
            throw new \InvalidArgumentException(
                'Configuration validation failed: ' . implode('; ', $errors)
            );
        }
    }

    /**
     * Build validated configuration array
     */
    private function buildValidatedConfig(): array
    {
        return [
            'participants' => [
                'max' => $this->maxParticipants,
                'min' => $this->minParticipants,
                'max_name_length' => $this->maxParticipantNameLength,
            ],
            'content' => [
                'max_title_length' => $this->maxTitleLength,
                'max_description_length' => $this->maxDescriptionLength,
            ],
            'security' => [
                'key_expiration_days' => $this->keyExpirationDays,
                'session_timeout_minutes' => $this->sessionTimeoutMinutes,
                'require_email_verification' => $this->requireEmailVerification,
                'enable_rate_limiting' => $this->enableRateLimiting,
                'encryption_algorithm' => $this->encryptionAlgorithm,
            ],
            'features' => [
                'allow_messages' => $this->allowMessagesBetweenParticipants,
                'anonymous_participation' => $this->allowAnonymousParticipation,
                'exclusion_groups' => $this->enableExclusionGroups,
                'history_tracking' => $this->enableHistoryTracking,
                'notifications' => $this->enableNotifications,
            ],
            'timing' => [
                'default_registration_days' => $this->defaultRegistrationPeriodDays,
                'max_registration_days' => $this->maxRegistrationPeriodDays,
                'processing_timeout_seconds' => $this->drawProcessingTimeoutSeconds,
                'timezone' => $this->defaultTimezone,
            ],
            'exclusions' => [
                'max_per_participant' => $this->maxExclusionsPerParticipant,
                'max_ratio' => $this->maxExclusionRatio,
            ],
            'email' => [
                'allowed_domains' => $this->allowedEmailDomains,
                'blocked_domains' => $this->blockedEmailDomains,
            ],
            'localization' => [
                'supported_languages' => $this->supportedLanguages,
            ],
            'files' => [
                'allowed_types' => $this->allowedFileTypes,
            ],
        ];
    }

    /**
     * Factory methods for common configurations
     */
    public static function development(): self
    {
        return new self(
            maxParticipants: 50,
            requireEmailVerification: false,
            enableRateLimiting: false,
            blockedEmailDomains: [],
            drawProcessingTimeoutSeconds: 60,
        );
    }

    public static function production(): self
    {
        return new self(
            maxParticipants: 1000,
            requireEmailVerification: true,
            enableRateLimiting: true,
            keyExpirationDays: 7,
            sessionTimeoutMinutes: 30,
        );
    }

    public static function testing(): self
    {
        return new self(
            maxParticipants: 10,
            minParticipants: 2,
            requireEmailVerification: false,
            enableRateLimiting: false,
            drawProcessingTimeoutSeconds: 30,
            customSeed: 12345,
        );
    }

    /**
     * Create from array with validation
     */
    public static function fromArray(array $config): self
    {
        return new self(
            maxParticipants: $config['max_participants'] ?? 100,
            minParticipants: $config['min_participants'] ?? 3,
            maxTitleLength: $config['max_title_length'] ?? 100,
            maxDescriptionLength: $config['max_description_length'] ?? 1000,
            maxParticipantNameLength: $config['max_participant_name_length'] ?? 50,
            keyExpirationDays: $config['key_expiration_days'] ?? 30,
            sessionTimeoutMinutes: $config['session_timeout_minutes'] ?? 60,
            requireEmailVerification: $config['require_email_verification'] ?? true,
            enableRateLimiting: $config['enable_rate_limiting'] ?? true,
            allowMessagesBetweenParticipants: $config['allow_messages'] ?? true,
            allowAnonymousParticipation: $config['anonymous_participation'] ?? false,
            enableExclusionGroups: $config['exclusion_groups'] ?? true,
            enableHistoryTracking: $config['history_tracking'] ?? true,
            enableNotifications: $config['notifications'] ?? true,
            defaultRegistrationPeriodDays: $config['default_registration_days'] ?? 14,
            maxRegistrationPeriodDays: $config['max_registration_days'] ?? 365,
            drawProcessingTimeoutSeconds: $config['processing_timeout_seconds'] ?? 300,
            maxExclusionsPerParticipant: $config['max_exclusions_per_participant'] ?? 10,
            maxExclusionRatio: $config['max_exclusion_ratio'] ?? 0.8,
            allowedEmailDomains: $config['allowed_email_domains'] ?? [],
            blockedEmailDomains: $config['blocked_email_domains'] ?? ['example.com', 'test.com'],
            supportedLanguages: $config['supported_languages'] ?? ['en', 'fr'],
            allowedFileTypes: $config['allowed_file_types'] ?? ['png', 'jpg', 'jpeg', 'gif'],
            defaultTimezone: $config['default_timezone'] ?? 'UTC',
            encryptionAlgorithm: $config['encryption_algorithm'] ?? 'AES-256-GCM',
            customSeed: $config['custom_seed'] ?? null,
        );
    }

    /**
     * Get configuration value by path
     */
    public function get(string $path, mixed $default = null): mixed
    {
        $keys = explode('.', $path);
        $value = $this->validatedConfig;

        foreach ($keys as $key) {
            if (!isset($value[$key])) {
                return $default;
            }
            $value = $value[$key];
        }

        return $value;
    }

    /**
     * Check if feature is enabled
     */
    public function isEnabled(string $feature): bool
    {
        return match($feature) {
            'messages' => $this->allowMessagesBetweenParticipants,
            'anonymous' => $this->allowAnonymousParticipation,
            'exclusion_groups' => $this->enableExclusionGroups,
            'history' => $this->enableHistoryTracking,
            'notifications' => $this->enableNotifications,
            'email_verification' => $this->requireEmailVerification,
            'rate_limiting' => $this->enableRateLimiting,
            default => false,
        };
    }

    /**
     * Export to array
     */
    public function toArray(): array
    {
        return $this->validatedConfig;
    }
}
