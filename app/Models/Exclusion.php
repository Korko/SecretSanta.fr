<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\WithoutTimestamps;

#[Fillable(['participant_id', 'exclusion_id'])]
#[WithoutTimestamps]
final class Exclusion extends Model {}
