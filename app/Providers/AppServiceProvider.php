<?php

namespace App\Providers;

use App\Services\AuditService;
use App\Services\ModuleActivationService;
use App\Services\ModuleService;
use App\Services\NotificationService;
use App\Services\TenantSelectionService;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Mendaftarkan ModuleService
        $this->app->singleton(ModuleService::class, function ($app) {
            return new ModuleService();
        });

        // Mendaftarkan AuditService
        $this->app->singleton(AuditService::class, function ($app) {
            return new AuditService();
        });

        // Mendaftarkan ModuleActivationService
        $this->app->singleton(ModuleActivationService::class, function ($app) {
            return new ModuleActivationService(
                $app->make(ModuleService::class),
                $app->make(AuditService::class)
            );
        });

        // Mendaftarkan TenantSelectionService
        $this->app->singleton(TenantSelectionService::class, function ($app) {
            return new TenantSelectionService();
        });

        // Mendaftarkan NotificationService
        $this->app->singleton(NotificationService::class, function ($app) {
            return new NotificationService();
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
