<?php

namespace App\Providers;

use App\Models\Anggota;
use App\Models\User;
use App\Observers\AnggotaObserver;
use App\Observers\UserObserver;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Daftarkan observer Anggota
        Anggota::observe(AnggotaObserver::class);
        Blade::component('filament-user-menu', \App\View\Components\Filament\UserMenu::class);
        User::observe(UserObserver::class);
    }
}
