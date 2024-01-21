<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Foundation\Application;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class Language
{
    public function __construct(
        protected readonly Application $app
    ) {
    }

    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $handledDomains = config('app.domains');
        $currentDomain = $request->getHost();

        foreach ($handledDomains as $locale => $domain) {
            if (preg_match('#'.preg_quote($domain, '#').'$#', $currentDomain)) {
                $this->app->setLocale($locale);
            }
        }

        return $next($request);
    }
}
