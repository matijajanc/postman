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
            if (!$this->isUriExcluded($route->uri) && str_starts_with($route->uri, config('postman.filter_uri')))
            {
                // Create folder
                if (!in_array($this->getFolderName($route->uri), array_column($postmanJson['item'], 'name'), false)) {
                    $postmanJson['item'][] = ['name' => $this->getFolderName($route->uri)];
                }

                $parameters = $this->getMethodParameters($route);
                $postmanJson['item'][$this->getFolderIndex($route->uri, $postmanJson['item'])]['item'][] = $this->setItem(
                    $route->uri,
                    $this->getApiName($route->uri),
                    $route->methods[0],
                    $parameters['mode'] ?? config('postman.default_body_type'),
                    $this->getRequestData($parameters),
                    $this->addAuthBlock($route->gatherMiddleware()),
                );
            }
        }

        $this->saveJsonFile($postmanJson, 'postman');
    }
}
