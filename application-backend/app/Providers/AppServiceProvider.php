<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Application\Backend\Providers;

use Illuminate\Support\ServiceProvider;
use MinVWS\DUSi\Application\Backend\Handlers\FileUploadHandler;
use MinVWS\DUSi\Application\Backend\Handlers\FormSubmitHandler;
use MinVWS\DUSi\Application\Backend\Services\ApplicationService;
use MinVWS\DUSi\Shared\Serialisation\Handlers\FileUploadHandlerInterface;
use Illuminate\Foundation\Application;
use MinVWS\DUSi\Shared\Serialisation\Handlers\FormSubmitHandlerInterface;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(
            FileUploadHandlerInterface::class,
            function (Application $app) {
                return new FileUploadHandler($app->get(ApplicationService::class));
            }
        );
        $this->app->bind(
            FormSubmitHandlerInterface::class,
            function (Application $app) {
                return new FormSubmitHandler($app->get(ApplicationService::class));
            }
        );
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
