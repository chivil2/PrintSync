<?php

namespace App\Providers;

use App\Models\PrintingService;
use App\Models\TechnicalService;
use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\Rules\Password;

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
        $this->configureDefaults();
    }

    /**
     * Configure default behaviors for production-ready applications.
     */
    protected function configureDefaults(): void
    {
        Date::use(CarbonImmutable::class);

        DB::prohibitDestructiveCommands(
            app()->isProduction(),
        );

        Relation::morphMap([
            'printing_service' => PrintingService::class,
            'technical_service' => TechnicalService::class,
        ]);

        View::addNamespace('pages', resource_path('views/pages'));

        if (app()->environment('local') && ! app()->runningInConsole()) {
            $request = request();

            if ($request !== null) {
                URL::forceRootUrl($request->getSchemeAndHttpHost());

                if ($request->isSecure()) {
                    URL::forceScheme('https');
                }
            }
        }

        Password::defaults(fn (): ?Password => app()->isProduction()
            ? Password::min(12)
                ->mixedCase()
                ->letters()
                ->numbers()
                ->symbols()
                ->uncompromised()
            : null,
        );
    }
}
