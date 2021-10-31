<?php

declare(strict_types=1);

namespace Matijajanc\Postman;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Illuminate\Routing\Route as RoutingRoute;
use Psr\Log\LoggerInterface;

class Postman extends PostmanAbstract
{
    public function __construct(
        private LoggerInterface $logger
    ) {
    }

    public function generateEnvironmentData(): void
    {
        $data = $this->setEnvironmentInfoBlock();
        $this->setVariable($data, 'host', env('APP_URL'));
        $token = null;
        try {
            $token = Auth::guard('api')->attempt([
                'email' => config('postman.login.username'),
                'password' => config('postman.login.password'),
            ]);
        } catch (\Throwable $e) {
            $this->logger->error('tymon/jwt-auth package not configured correctly.');
        }
        $this->setVariable($data, 'token', $token ?: 'token');

        $this->saveJsonFile($data, 'postman-environment');
    }

    public function generatePostmanJson(): void
    {
        $postmanJson = $this->setInfoBlock();
        $postmanJson['item'] = [];

        /** @var RoutingRoute $route */
        foreach (Route::getRoutes() as $route) {
            $routeUri = $this->getFormattedUri($route->uri ?? $route['uri']);
            if (!$this->isUriExcluded($routeUri) && str_starts_with($routeUri, config('postman.filter_uri')))
            {
                // Create folder
                if (!in_array($this->getFolderName($routeUri), array_column($postmanJson['item'], 'name'), false)) {
                    $postmanJson['item'][] = ['name' => $this->getFolderName($routeUri)];
                }

                $parameters = $this->getMethodParameters($this->getControllerName($route), $this->getMethodName($route));
                $postmanJson['item'][$this->getFolderIndex($routeUri, $postmanJson['item'])]['item'][] = $this->setItem(
                    $routeUri,
                    $this->getApiName($routeUri),
                    $route->methods[0] ?? $route['method'],
                    $parameters['mode'] ?? config('postman.default_body_type'),
                    $this->getRequestData($parameters),
                    $this->addAuthBlock(
                        is_array($route) ? ($route['action']['middleware'] ?? []) : $route->gatherMiddleware()
                    ),
                );
            }
        }

        $this->saveJsonFile($postmanJson, 'postman');
    }
}
