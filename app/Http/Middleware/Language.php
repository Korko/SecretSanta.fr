<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Foundation\Application;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

final readonly class Language
{
    public function __construct(private Application $app) {}

    /** @param Closure(Request): Response $next */
    public function handle(Request $request, Closure $next): Response
    {
        /** @var array<string, ?string> $handledDomains */
        $handledDomains = config()->array('app.domains');
        $currentDomain = $request->getHost();

        foreach (array_filter($handledDomains) as $locale => $domain) {
            if (preg_match('#'.preg_quote($domain, '#').'$#', $currentDomain)) {
                $this->app->setLocale($locale);
            }
        }

        return $next($request);
    }
}
