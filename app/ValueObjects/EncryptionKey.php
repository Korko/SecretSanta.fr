<?php

namespace App\ValueObjects;

/**
 * Value Object with asymmetric visibility and constructor hooks
 */
final readonly class EncryptionKey
{
    /**
     * Asymmetric visibility - public read, private write
     */
    public private(set) string $value;
    public private(set) string $hash;
    public private(set) \DateTime $createdAt;
    public private(set) ?\DateTime $expiresAt;

    /**
     * Constructor with comprehensive validation
     */
    public function __construct(
        string $rawKey,
        ?int $ttlSeconds = null,
        private readonly string $algorithm = 'sha256',
        private readonly int $minLength = 32,
    ) {
        $this->validateKey($rawKey);
        $this->value = $rawKey;
        $this->hash = hash($this->algorithm, $rawKey);
        $this->createdAt = new \DateTime();
        $this->expiresAt = $ttlSeconds ?
            (new \DateTime())->add(new \DateInterval("PT{$ttlSeconds}S")) : null;
    }

    /**
     * Enhanced validation with detailed error messages
     */
    private function validateKey(string $key): void
    {
        if (strlen($key) < $this->minLength) {
            throw new \InvalidArgumentException(
                "Encryption key must be at least {$this->minLength} characters long"
            );
        }

        if (!preg_match('/^[A-Za-z0-9+\/=]+$/', $key)) {
            throw new \InvalidArgumentException('Encryption key contains invalid characters');
        }

        // Check entropy
        if ($this->calculateEntropy($key) < 4.0) {
            throw new \InvalidArgumentException('Encryption key has insufficient entropy');
        }
    }

    /**
     * Calculate key entropy for security validation
     */
    private function calculateEntropy(string $key): float
    {
        $chars = array_count_values(str_split($key));
        $length = strlen($key);
        $entropy = 0.0;

        foreach ($chars as $count) {
            $probability = $count / $length;
            $entropy -= $probability * log($probability, 2);
        }

        return $entropy;
    }

    /**
     * Factory methods with enhanced type safety
     */
    public static function generate(
        int $length = 32,
        ?int $ttlSeconds = 3600,
        string $algorithm = 'sha256'
    ): self {
        if ($length < 16) {
            throw new \InvalidArgumentException('Generated key length must be at least 16 characters');
        }

        $key = base64_encode(random_bytes($length));
        return new self($key, $ttlSeconds, $algorithm, 16);
    }

    public static function fromBase64(
        string $base64Key,
        ?int $ttlSeconds = null,
        string $algorithm = 'sha256'
    ): self {
        $decoded = base64_decode($base64Key, true);
        if ($decoded === false) {
            throw new \InvalidArgumentException('Invalid base64 key format');
        }

        return new self($base64Key, $ttlSeconds, $algorithm);
    }

    public static function temporary(int $ttlSeconds = 300): self
    {
        return self::generate(32, $ttlSeconds);
    }

    /**
     * State validation methods
     */
    public function isExpired(): bool
    {
        return $this->expiresAt && new \DateTime() > $this->expiresAt;
    }

    public function isValid(): bool
    {
        return !$this->isExpired() && strlen($this->value) >= $this->minLength;
    }

    public function timeUntilExpiry(): ?\DateInterval
    {
        if (!$this->expiresAt) {
            return null;
        }

        $now = new \DateTime();
        return $now < $this->expiresAt ? $now->diff($this->expiresAt) : null;
    }

    /**
     * Secure comparison
     */
    public function equals(self $other): bool
    {
        return hash_equals($this->hash, $other->hash);
    }

    /**
     * Export for storage (excludes the raw key)
     */
    public function toStorageArray(): array
    {
        return [
            'hash' => $this->hash,
            'algorithm' => $this->algorithm,
            'created_at' => $this->createdAt->format('Y-m-d H:i:s'),
            'expires_at' => $this->expiresAt?->format('Y-m-d H:i:s'),
        ];
    }
}
