<?php

namespace App\Providers;

use App\Models\User;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // AdminLTE 3 é baseado em Bootstrap 4
        Paginator::useBootstrapFour();

        Gate::define('admin', fn (User $user) => $user->isAdmin());
    }
}
