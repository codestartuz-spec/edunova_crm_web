<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/** `feature:legacy_import` - config('features.legacy_import') o'chiq bo'lsa 404 qaytaradi. */
class EnsureFeature
{
    public function handle(Request $request, Closure $next, string $feature): Response
    {
        abort_unless((bool) config("features.{$feature}"), 404);

        return $next($request);
    }
}
