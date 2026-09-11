<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\Admin\TeacherScheduleGenerationService; // 実在クラス

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
        //
    }
}
