<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Assessment\API\Services;

use Illuminate\Http\Request;
use Illuminate\Routing\RouteCollection;
use Illuminate\Routing\RouteCollectionInterface;
use Illuminate\Routing\UrlGenerator;
use Illuminate\Support\Facades\Route;

class FrontendRouteService
{
    protected UrlGenerator $urlGenerator;

    public function __construct(
        private readonly string $baseUrl,
    ) {
        $this->urlGenerator = $this->getUrlGenerator();
    }

    public function route(string $name, array $parameters = []): string
    {
        return $this->urlGenerator->route($name, $parameters);
    }

    protected function getRoutes(): array
    {
        return [
            Route::get('/wachtwoord-reset')
                ->name('password-reset'),
        ];
    }

    protected function getRouteCollection(): RouteCollectionInterface
    {
        $collection = new RouteCollection();

        foreach ($this->getRoutes() as $route) {
            $collection->add($route);
        }

        return $collection;
    }

    protected function getUrlGenerator(): UrlGenerator
    {
        $urlGenerator = new UrlGenerator(
            routes: $this->getRouteCollection(),
            request: new Request(),
        );
        $urlGenerator->forceRootUrl($this->baseUrl);

        return $urlGenerator;
    }
}
