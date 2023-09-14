<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class UserEmail extends Model
{
    use HasFactory;

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function email(): Attribute
    {
        return Attribute::make(
            set: fn($value) => hash(
                algo: 'sha256',
                data: $value,
                options: ['seed' => config('app.key')]
            )
        );
    }
}
