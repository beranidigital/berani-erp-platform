<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SetIntendedFromQuery
{
    public function handle(Request $request, Closure $next): Response
    {
        // If "intended" query is present, remember it so login redirects there
        if ($request->filled('intended')) {
            $intended = $request->string('intended')->toString();

            if (str_starts_with($intended, '/')) {
                $request->session()->put('url.intended', url($intended));
            } elseif (filter_var($intended, FILTER_VALIDATE_URL)) {
                // Only allow same-host URLs for safety
                $intendedHost = parse_url($intended, PHP_URL_HOST);
                if ($intendedHost === $request->getHost()) {
                    $request->session()->put('url.intended', $intended);
                }
            }
        }

        return $next($request);
    }
}
