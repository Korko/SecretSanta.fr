<?php

declare(strict_types=1);

namespace App\Queue;

use DateTimeInterface;
use Illuminate\Queue\Failed\CountableFailedJobProvider;
use Illuminate\Queue\Failed\FailedJobProviderInterface;
use Illuminate\Queue\Failed\PrunableFailedJobProvider;

/**
 * Stores the failed jobs without the draw secret their payload carries in clear (data.iv, see
 * AppServiceProvider::bootJobs): it is only lent for the time of the sending, not for as long as a failed job is kept.
 *
 * Such a job can't decrypt its draw anymore, so retrying it fails again: the email is resent from the
 * organizer panel instead, with the secret of its link.
 */
final readonly class FailedJobProviderWithoutSecret implements CountableFailedJobProvider, FailedJobProviderInterface, PrunableFailedJobProvider
{
    public function __construct(private FailedJobProviderInterface $failer) {}

    public function log($connection, $queue, $payload, $exception)
    {
        return $this->failer->log($connection, $queue, $this->withoutSecret($payload), $exception);
    }

    public function ids($queue = null)
    {
        return $this->failer->ids($queue);
    }

    public function all()
    {
        return $this->failer->all();
    }

    public function find($id)
    {
        return $this->failer->find($id);
    }

    public function forget($id)
    {
        return $this->failer->forget($id);
    }

    /** @param  int|null  $hours */
    public function flush($hours = null, ?string $queue = null): void
    {
        // @phpstan-ignore arguments.count (given by queue:flush --queue, taken by every Laravel provider but missing from the interface)
        $this->failer->flush($hours, $queue);
    }

    public function count($connection = null, $queue = null)
    {
        return $this->failer instanceof CountableFailedJobProvider ? $this->failer->count($connection, $queue) : 0;
    }

    public function prune(DateTimeInterface $before)
    {
        return $this->failer instanceof PrunableFailedJobProvider ? $this->failer->prune($before) : 0;
    }

    private function withoutSecret(string $payload): string
    {
        $decoded = json_decode($payload, true);
        if (! is_array($decoded) || ! is_array($decoded['data'] ?? null)) {
            return $payload;
        }

        unset($decoded['data']['iv']);

        // Same flags as the queue payloads (Illuminate\Queue\Queue::createPayload)
        return json_encode($decoded, JSON_UNESCAPED_UNICODE) ?: $payload;
    }
}
