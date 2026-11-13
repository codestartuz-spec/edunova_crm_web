<?php

namespace App\Providers;

use App\Enums\Role;
use App\Models\User;
use App\Support\PermissionRegistry;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Route;
use Illuminate\Validation\Rules\Password;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        Carbon::setLocale('uz_Latn');

        // v15: yangi to'lov / murojaat / foydalanuvchi bo'lsa statistika keshi yangilanadi
        \App\Models\User::created(fn () => \App\Http\Controllers\StatisticsController::bump());
        foreach ([\App\Models\Payment::class, \App\Models\Lead::class] as $m) {
            $m::saved(fn () => \App\Http\Controllers\StatisticsController::bump());
            $m::deleted(fn () => \App\Http\Controllers\StatisticsController::bump());
        }

        // Parol siyosati: kamida 10 belgi, harf va raqam; productionda ma'lum sizib chiqqan parollar rad etiladi
        Password::defaults(fn () => $this->app->isProduction()
            ? Password::min(10)->letters()->numbers()->uncompromised()
            : Password::min(10)->letters()->numbers());

        Paginator::defaultView('vendor.pagination.edunova');
        Model::preventLazyLoading(! $this->app->isProduction());

        // sAdmin uchun barcha ruxsatlar ochiq
        Gate::before(fn (User $user) => $user->isSuperAdmin() ? true : null);

        // Har bir ruxsat kaliti Gate sifatida ro'yxatdan o'tadi: @can('students.view'), can:students.view
        foreach (PermissionRegistry::keys() as $key) {
            Gate::define($key, fn (User $user) => $user->hasPermission($key));
        }

        // Foydalanuvchini faqat joriy filial kontekstida topish (boshqa filial -> 404)
        Route::bind('student', fn ($value) => User::visibleToContext()->ofRole(Role::Student)->findOrFail($value));
        Route::bind('user', fn ($value) => User::visibleToContext()->findOrFail($value));
    }
}
