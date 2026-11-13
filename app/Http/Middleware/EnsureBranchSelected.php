<?php

namespace App\Http\Middleware;

use App\Support\BranchContext;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * sAdmin "Barcha filiallar" rejimida bo'lsa, yozuv yaratadigan bo'limlarga kirishdan oldin
 * filial tanlashni so'raydi (tanlangan filial `BranchContext::id()` orqali tekshiriladi).
 */
class EnsureBranchSelected
{
    public function handle(Request $request, Closure $next): Response
    {
        if (BranchContext::id() === null) {
            return redirect()->route('dashboard')->with('error', "Bu bo'lim bilan ishlash uchun yuqoridan filialni tanlang.");
        }

        return $next($request);
    }
}
