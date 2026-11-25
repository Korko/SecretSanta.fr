<?php

declare(strict_types=1);

namespace App\Jobs\Contracts;

// A job that reads nothing encrypted: its payload does not carry the draw secret (see AppServiceProvider::bootJobs)
interface WithoutDrawSecret {}
