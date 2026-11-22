<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\MorphOne;

/**
 * A model whose email's delivery is followed (target email, organizer recap, message to a santa):
 * its Mail row is created and deleted with it.
 */
trait HasTrackedMail
{
    /** @return MorphOne<Mail, $this> */
    public function mail(): MorphOne
    {
        return $this->morphOne(Mail::class, 'mailable');
    }

    protected static function bootHasTrackedMail(): void
    {
        static::created(function (self $model): void {
            $model->mail()->save(new Mail);
        });

        static::deleting(function (self $model): void {
            $model->mail()->delete();
        });
    }
}
