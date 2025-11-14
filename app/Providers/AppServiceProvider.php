<?php

namespace App\Providers;

use App\Repositories\ArticleRepository;
use Illuminate\Support\ServiceProvider;
use App\Services\Sources\NyTimesService;
use App\Services\Sources\GuardianService;
use App\Services\Sources\NewsApiAiService;
use App\Services\Sources\NewsApiOrgService;
use App\Interfaces\SourceRepositoryInterface;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(SourceRepositoryInterface::class, ArticleRepository::class);
        $this->app->bind(NyTimesService::class);
        $this->app->bind(GuardianService::class);
        $this->app->bind(NewsApiAiService::class);
        $this->app->bind(NewsApiOrgService::class);

    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
