<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\Admin\GenarateTeacherSchedulesService; // 実在クラス
use App\Services\Admin\GenerateTeacherSchedulesService; // 参照される名前（未実在でも文字列bindで救済可）

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
