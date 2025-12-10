<?php

namespace App\Http\Middleware;

use Symfony\Component\HttpFoundation\Response;
use Closure;
use Illuminate\Foundation\Application;
use Illuminate\Http\Request;

class Language
{
    private $app;

    public function __construct(Application $app)
    {
        $this->app = $app;
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
