<?php

use App\Http\Middleware\EnsureBranchSelected;
use App\Http\Middleware\EnsureLegalAccepted;
use App\Http\Middleware\EnsureRole;
use App\Http\Middleware\EnsureUserIsActive;
use App\Http\Middleware\PreventDuplicateSubmission;
use App\Http\Middleware\SecurityHeaders;
use App\Http\Middleware\StripMoneySpaces;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->append(StripMoneySpaces::class);
        $middleware->append(SecurityHeaders::class);
        $middleware->appendToGroup('web', \App\Http\Middleware\EnsureSubscriptionWritable::class);
        $middleware->appendToGroup('web', \App\Http\Middleware\ServeLanding::class);
        // Laravel "auth" ni boshqa middleware'lardan oldin ishlatadi; landing esa undan OLDIN, lekin sessiyadan keyin turishi kerak
        $middleware->prependToPriorityList(before: \Illuminate\Contracts\Auth\Middleware\AuthenticatesRequests::class, prepend: \App\Http\Middleware\ServeLanding::class);
        $middleware->encryptCookies(except: ['cookie_consent']);
        $middleware->validateCsrfTokens(except: ['telegram/webhook']);
        $middleware->redirectGuestsTo(fn () => route('login'));
        $middleware->redirectUsersTo(fn () => route('dashboard'));
        $middleware->alias([
            'active' => EnsureUserIsActive::class,
            'role' => EnsureRole::class,
            'branch' => EnsureBranchSelected::class,
            'once' => PreventDuplicateSubmission::class,
            'legal' => EnsureLegalAccepted::class,
            'subscription' => \App\Http\Middleware\EnsureSubscriptionWritable::class,
            'feature' => \App\Http\Middleware\EnsureFeature::class,
        ]);

        // v8 A5: server odatda Nginx (bir xil mashinada) orqasida ishlaydi - shu holatda haqiqiy mijoz
        // IP manzili va https sxemasi to'g'ri aniqlanishi uchun mahalliy proksi ishonchli deb belgilanadi.
        // Diqqat: agar server proksisiz to'g'ridan-to'g'ri internetga ochiq bo'lsa, buni o'zgartirish kerak emas -
        // faqat 127.0.0.1/::1 dan kelgan sarlavhalar ishonchli, tashqi so'rovlar bunga ta'sir qila olmaydi.
        $middleware->trustProxies(
            // Docker/Cloudflare Tunnel orqasida ishlaganda .env da TRUSTED_PROXIES=* qo'ying (so'rov konteynerga proksi IP si bilan keladi).
            at: array_values(array_filter(array_map('trim', explode(',', (string) env('TRUSTED_PROXIES', '127.0.0.1,::1'))))),
            headers: Request::HEADER_X_FORWARDED_FOR | Request::HEADER_X_FORWARDED_HOST | Request::HEADER_X_FORWARDED_PORT | Request::HEADER_X_FORWARDED_PROTO,
        );
    })
    ->withExceptions(function (Exceptions $exceptions) {
        // v22: obunasi tugagan filialda yozish urinishi
        $exceptions->render(function (\App\Exceptions\SubscriptionExpiredException $e, Request $request) {
            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'message' => $e->getMessage()], 403);
            }

            return back()->with('error', $e->getMessage());
        });
    })
    ->create();
