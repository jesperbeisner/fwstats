<?php

declare(strict_types=1);

namespace App\Providers;

use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;

final class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
    }

    public function boot(): void
    {
        $this->configureCommands();
        $this->configureDates();
        $this->configureModels();
        $this->configureUrls();
    }

    private function configureCommands(): void
    {
        DB::prohibitDestructiveCommands(
            prohibit: $this->app->environment('production') === true,
        );
    }

    private function configureDates(): void
    {
        Date::use(CarbonImmutable::class);
    }

    private function configureModels(): void
    {
        Model::shouldBeStrict(
            shouldBeStrict: $this->app->environment('local', 'testing', 'stage') === true,
        );
    }

    private function configureUrls(): void
    {
        if ($this->app->environment('stage', 'production') === true) {
            URL::forceScheme('https');
        }
    }
}
