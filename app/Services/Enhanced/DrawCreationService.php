<?php

namespace App\Services\Enhanced;

use App\DTOs\DrawCreationRequestFixed;
use App\Models\Draw\Draw;
use App\ValueObjects\EncryptionKey;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Service with constructor dependency injection and hooks
 */
final readonly class DrawCreationService
{
    /**
     * Constructor with property promotion and validation hooks
     */
    public function __construct(
        private \App\Services\Encryption\EncryptionService $encryptionService,
        private \App\Managers\Encryption\SecretSantaEncryptionManager $encryptionManager,
        private array $config = [],
    ) {
        $this->validateConfiguration();
    }

    /**
     * Factory method with dependency resolution
     */
    public static function create(
        ?\App\Services\Encryption\EncryptionService $encryptionService = null,
        ?\App\Managers\Encryption\SecretSantaEncryptionManager $encryptionManager = null,
        array $config = []
    ): self {
        return new self(
            encryptionService: $encryptionService ?? app(\App\Services\Encryption\EncryptionService::class),
            encryptionManager: $encryptionManager ?? app(\App\Managers\Encryption\SecretSantaEncryptionManager::class),
            config: array_merge([
                'max_title_length' => 100,
                'max_description_length' => 1000,
                'default_registration_period_days' => 7,
                'require_email_verification' => true,
            ], $config)
        );
    }

    /**
     * Validate service configuration
     */
    private function validateConfiguration(): void
    {
        $requiredKeys = ['max_title_length', 'max_description_length', 'default_registration_period_days'];

        foreach ($requiredKeys as $key) {
            if (!isset($this->config[$key]) || !is_numeric($this->config[$key])) {
                throw new \InvalidArgumentException("Configuration key '{$key}' is required and must be numeric");
            }
        }
    }

    /**
     * Method with comprehensive error handling
     */
    public function createDraw(DrawCreationRequestFixed $request, ?int $userId = null): Draw
    {
        return DB::transaction(function () use ($request, $userId) {
            // Generate encryption keys
            $masterKey = EncryptionKey::generate(64, 86400); // 24 hours
            $organizerKey = EncryptionKey::generate(32, 86400 * 7); // 1 week

            // Create the draw with enhanced validation
            $draw = $this->createDrawModel($request, $masterKey, $organizerKey, $userId);

            // Setup initial organizer participant
            $this->createOrganizerParticipant($draw, $request, $masterKey);

            // Log creation with detailed context
            $this->logDrawCreation($draw, $request);

            return $draw;
        });
    }

    /**
     * Enhanced draw model creation with validation
     */
    private function createDrawModel(
        DrawCreationRequestFixed $request,
        EncryptionKey $masterKey,
        EncryptionKey $organizerKey,
        ?int $userId
    ): Draw {
        $attributes = $request->toModelAttributes();

        // Add encryption keys and metadata
        $attributes = array_merge($attributes, [
            'user_id' => $userId,
            'uuid' => \Illuminate\Support\Str::uuid(),
            'organizer_key_hash' => $organizerKey->hash,
            'master_key_encrypted' => $this->encryptionService->encrypt(
                $masterKey->value,
                $organizerKey->value
            ),
        ]);

        // Encrypt sensitive data
        $attributes['title_encrypted'] = $this->encryptionService->encrypt(
            $request->title,
            $masterKey->value
        );

        if ($request->description) {
            $attributes['description_encrypted'] = $this->encryptionService->encrypt(
                $request->description,
                $masterKey->value
            );
        }

        $attributes['organizer_name_encrypted'] = $this->encryptionService->encrypt(
            $request->organizerName,
            $masterKey->value
        );

        $attributes['organizer_email_encrypted'] = $this->encryptionService->encrypt(
            $request->organizerEmail,
            $masterKey->value
        );

        return Draw::create($attributes);
    }

    /**
     * Create organizer participant with enhanced security
     */
    private function createOrganizerParticipant(
        Draw $draw,
        DrawCreationRequestFixed $request,
        EncryptionKey $masterKey
    ): void {
        $participantKey = EncryptionKey::generate(32, 86400 * 30); // 30 days

        $draw->participants()->create([
            'uuid' => \Illuminate\Support\Str::uuid(),
            'individual_key_hash' => $participantKey->hash,
            'master_key_encrypted' => $this->encryptionService->encrypt(
                $masterKey->value,
                $participantKey->value
            ),
            'name_encrypted' => $this->encryptionService->encrypt(
                $request->organizerName,
                $masterKey->value
            ),
            'email_encrypted' => $this->encryptionService->encrypt(
                $request->organizerEmail,
                $masterKey->value
            ),
            'status' => \App\Enums\ParticipantStatus::ACCEPTED,
            'is_organizer' => true,
            'accepted_at' => now(),
        ]);
    }

    /**
     * Enhanced logging with structured data
     */
    private function logDrawCreation(Draw $draw, DrawCreationRequestFixed $request): void
    {
        Log::info('Draw created successfully', [
            'draw_id' => $draw->id,
            'draw_uuid' => $draw->uuid,
            'status' => $request->initialStatus->value,
            'has_deadline' => !is_null($request->registrationDeadline),
            'auto_accept' => $request->autoAcceptParticipants,
            'allow_messages' => $request->allowTargetMessages,
            'organizer_domain' => substr(strrchr($request->organizerEmail, '@'), 1),
            'created_at' => now()->toISOString(),
        ]);
    }

    /**
     * Batch creation with array unpacking
     */
    public function createMultipleDraws(array $requests, ?int $userId = null): array
    {
        $results = [];

        foreach ($requests as $requestData) {
            try {
                $request = is_array($requestData)
                    ? DrawCreationRequestFixed::fromArray($requestData)
                    : $requestData;

                $results[] = [
                    'success' => true,
                    'draw' => $this->createDraw($request, $userId),
                    'request' => $request,
                ];
            } catch (\Exception $e) {
                $results[] = [
                    'success' => false,
                    'error' => $e->getMessage(),
                    'request' => $requestData,
                ];
            }
        }

        return $results;
    }

    /**
     * Enhanced validation with detailed feedback
     */
    public function validateDrawCreation(DrawCreationRequestFixed $request): array
    {
        $issues = [];

        // Validate title length against config
        if (strlen($request->title) > $this->config['max_title_length']) {
            $issues[] = "Title exceeds maximum length of {$this->config['max_title_length']} characters";
        }

        // Validate description length if provided
        if ($request->description && strlen($request->description) > $this->config['max_description_length']) {
            $issues[] = "Description exceeds maximum length of {$this->config['max_description_length']} characters";
        }

        // Validate registration deadline
        if ($request->registrationDeadline) {
            $maxFutureDate = now()->addDays(365); // 1 year max
            if ($request->registrationDeadline > $maxFutureDate) {
                $issues[] = "Registration deadline cannot be more than 1 year in the future";
            }
        }

        return [
            'valid' => empty($issues),
            'issues' => $issues,
            'summary' => empty($issues) ? 'Validation passed' : 'Validation failed with ' . count($issues) . ' issue(s)',
        ];
    }
}
