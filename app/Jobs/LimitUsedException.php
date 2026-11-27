<?php

declare(strict_types=1);

namespace App\Jobs;

use RuntimeException;

// The participant limit code was used by another draw since the form was sent
final class LimitUsedException extends RuntimeException {}
