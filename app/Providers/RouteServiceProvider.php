<?php

namespace App\Providers;

use App\Services\ApplicationService;
use App\Services\Exceptions\ApplicationNotFoundException;
use App\Services\Exceptions\SubsidyStageNotFoundException;
use App\Services\SubsidyStageService;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * The path to the "home" route for your application.
     *
     * Typically, users are redirected here after authentication.
     *
     * @var string
     */
    public const HOME = '/logged_in';

    public function boot(): void
    {
        $this->configureRateLimiting();

        $this->registerBindings();

        $this->routes(function () {
            Route::middleware('web')
                ->group(base_path('routes/web.php'));
            Route::middleware('api')
                ->prefix('api')
                ->as('api.')
                ->group(base_path('routes/api.php'));
        });
    }

    private function registerBindings(): void
    {
        Route::bind('form', function (string $id) {
            try {
                return app()->get(SubsidyStageService::class)->getSubsidyStage($id);
            } catch (SubsidyStageNotFoundException $e) {
                abort(404, $e->getMessage());
            }
        });

        Route::bind('application', function (string $id) {
            try {
                return app()->get(ApplicationService::class)->getApplication($id);
            } catch (ApplicationNotFoundException $e) {
                abort(404, $e->getMessage());
            }
        });
    }

    protected function configureRateLimiting()
    {
        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(60)->by($request->user()?->id ?: $request->ip());
        });
    }
}
