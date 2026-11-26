<?php

declare(strict_types=1);

namespace App\Contracts;

// A queued job or notification that reads the draw's encrypted data: only these get the draw secret in their payload
// (see AppServiceProvider::bootJobs). Any other job runs without it
interface WithDrawSecret {}
