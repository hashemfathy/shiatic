<?php

namespace App\Providers;

use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;

class RouteServiceProvider extends ServiceProvider
{

    protected $namespace = 'App\Http\Controllers';
    protected $admin_namespace = 'App\Http\Controllers\Admin';
    protected $frontend_namespace = 'App\Http\Controllers\Frontend';



    public function boot()
    {
        //

        parent::boot();
    }


    public function map()
    {
        $this->mapAdminAuthRoutes();
        $this->mapAdminWebRoutes();
        $this->mapAdminApiRoutes();

        $this->mapFrontendWebRoutes();
    }

    protected function mapAdminApiRoutes()
    {
        Route::domain(config('app.app_url'))
            ->middleware(['web', 'admin.auth.check'])
            ->namespace($this->admin_namespace)
            ->prefix('api')
            ->name('admin.api.')
            ->group(base_path('routes/backend/api.php'));
    }

    protected function mapAdminWebRoutes()
    {
        Route::domain(config('app.app_url'))
            ->middleware(['web', 'admin.auth.check'])
            ->namespace($this->admin_namespace)
            ->name('admin.web.')
            ->group(base_path('routes/backend/web.php'));
    }

    protected function mapAdminAuthRoutes()
    {
        Route::domain(config('app.app_url'))
            ->middleware('web')
            ->namespace($this->admin_namespace)
            ->name('auth.')
            ->prefix('auth')
            ->group(base_path('routes/backend/auth.php'));
    }

    protected function mapFrontendWebRoutes()
    {
        Route::domain(config('app.url'))
            ->middleware(['web'])
            ->namespace($this->frontend_namespace)
            ->name('frontend.web.')
            ->group(base_path('routes/frontend/web.php'));
    }
}
