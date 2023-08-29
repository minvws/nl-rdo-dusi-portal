<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Application\Backend\Providers;

use Illuminate\Filesystem\FilesystemManager;
use Illuminate\Foundation\Application;
use Illuminate\Support\ServiceProvider;
use MinVWS\DUSi\Application\Backend\Handlers\FileUploadHandler;
use MinVWS\DUSi\Application\Backend\Handlers\FormSubmitHandler;
use MinVWS\DUSi\Application\Backend\Repositories\ApplicationFileRepository;
use MinVWS\DUSi\Application\Backend\Services\ApplicationService;
use MinVWS\DUSi\Shared\Application\Models\Disk;
use MinVWS\DUSi\Shared\Serialisation\Handlers\FileUploadHandlerInterface;
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
        $this->app->bind(
            ApplicationFileRepository::class,
            function (Application $app) {
                return new ApplicationFileRepository(
                    filesystem: $app->get(FilesystemManager::class)->disk(Disk::APPLICATION_FILES),
                );
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
