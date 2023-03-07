<?php

namespace App\Http\Middleware;

use Symfony\Component\HttpFoundation\Response;
use Illuminate\Http\Request;
use Closure;
use Illuminate\Foundation\Application;

class Language
{
    private $app;

    public function __construct(Application $app)
    {
        $this->app = $app;
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
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
