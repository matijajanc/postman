<?php

declare(strict_types=1);

namespace Matijajanc\Postman;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Illuminate\Routing\Route as RoutingRoute;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Matijajanc\Postman\Helpers\StringTools;

class Postman
{
    public function generateEnvironmentData(): void
    {
        $data = $this->setEnvironmentInfoBlock();
        $this->setVariable($data, 'host', env('APP_URL'));
        $token = Auth::guard('api')->attempt([
            'email' => config('postman.login.username'),
            'password' => config('postman.login.password'),
        ]);
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

    private function getMethodParameters(RoutingRoute $route): array
    {
        $parameters = [];
        $reflector = new \ReflectionMethod($route->getController(), $route->getActionMethod());
        foreach ($reflector->getAttributes() as $attributes) {
            if (str_contains($attributes->getName(), 'Postman')) {
                $parameters = $attributes->getArguments()[0] ?? [];
            }
        }

        return $parameters;
    }

    private function getRequestData(array $parameters): array
    {
        $data = [];
        if ($parameters['factory'] ?? false) {
            $factoryClass = new $parameters['factory'];
            $data = $factoryClass->{method_exists(
                $parameters['factory'],
                $parameters['method']
            ) ? $parameters['method'] : 'definition'}();
        }

        return $data;
    }

    private function isUriExcluded(string $uri): bool
    {
        $skip = false;
        foreach (config('postman.excluded_routes') as $uriPart) {
            if (str_contains($uri, $uriPart)) {
                $skip = true;
                break;
            }
        }

        return $skip;
    }

    private function addAuthBlock(?array $middlewares): bool
    {
        return in_array(config('postman.middleware'), $middlewares, true);
    }

    private function setEnvironmentInfoBlock(): array
    {
        return [
            'id' => Str::uuid()->toString(),
            'name' => env('APP_NAME'),
            '_postman_variable_scope' => 'environment',
        ];
    }

    private function setInfoBlock(): array
    {
        return [
            'info' => [
                '_postman_id' => Str::uuid()->toString(),
                'name' => env('APP_NAME'),
                'schema' => 'https://schema.getpostman.com/json/collection/v2.1.0/collection.json',
            ],
        ];
    }

    private function setVariable(array &$data, string $name, string $value): void
    {
        $data['values'][] = [
            'key' => $name,
            'value' => $value,
            'description' => '',
            'enabled' => true,
        ];
    }

    private function setItem(
        string $uri,
        string $apiName,
        string $method,
        string $mode,
        array $data = [],
        bool $setAuth = true
    ): array {
        return array_merge(
            $this->setApiName($apiName),
            $this->setRequest($uri, $method, $mode, $data, $setAuth),
            $this->setResponse(),
        );
    }

    private function setApiName(string $name): array
    {
        return [
            'name' => $name,
        ];
    }

    private function setLoginEventBlock(): array
    {
        return [
            'event' => [
                [
                    'listen' => 'test',
                    'script' => [
                        'exec' => [
                            'var data = pm.response.json();',
                            'pm.environment.set("token", data.records.access_token);'
                        ],
                        'type' => 'text/javascript',
                    ],
                ],
            ],
        ];
    }

    private function setRequest(string $uri, string $method, string $mode, array $data, bool $setAuth): array
    {
        return array_merge(
            $uri === config('postman.login.uri') ? $this->setLoginEventBlock() : [],
            [
                'request' => array_merge(
                    $setAuth ? $this->setAuth() : [],
                    $this->setMethod($method),
                    $this->setJsonHeader(),
                    $this->setBody($data, $mode),
                    $this->setUrlBlock($uri)
                )
            ]
        );
    }

    private function setAuth(): array
    {
        return [
            'auth' => [
                'type' => 'bearer',
                'bearer' => [[
                    'key' => 'token',
                    'value' => '{{token}}',
                    'type' => 'text',
                ]],
            ],
        ];
    }

    private function setMethod(string $type): array
    {
        return ['method' => $type];
    }

    private function setJsonHeader(): array
    {
        return [
            'header' => [
                [
                    'key' => 'Accept',
                    'value' => 'application/json',
                ],
                [
                    'key' => 'Content-Type',
                    'value' => 'application/json',
                ],
            ],
        ];
    }

    private function setBody(array $data, string $mode): array
    {
        return [
            'body' => [
                'mode' => $mode,
                $mode => $mode !== 'raw' ? $this->setFormData($data) : $this->setRawData($data),
            ],
        ];
    }

    private function setFormData(array $data): array
    {
        $structure = [];
        foreach ($data as $key => $value) {
            $structure[] = [
                'key' => StringTools::camelToSnakeCase($key),
                'value' => $value,
                'type' => '',
            ];
        }

        return $structure;
    }

    private function setRawData(array $data): string
    {
        return json_encode($data, JSON_PRETTY_PRINT);
    }

    private function setUrlBlock(string $uri): array
    {
        return [
            'url' => [
                'raw' => '{{host}}' . (str_starts_with($uri, '/') ? $uri : '/' . $uri),
                'protocol' => null,
                'host' => '{{host}}',
                'path' => $this->getUriPathArray($uri),
            ],
        ];
    }

    private function setResponse(): array
    {
        return [
            'response' => [],
        ];
    }

    private function getUriPathArray(string $url): array
    {
        return explode('/', $url);
    }

    private function getFolderName(string $uri): string
    {
        $removeApiPart = explode('/', $uri);

        return ucfirst($removeApiPart[1] ?? 'other');
    }

    private function getFolderIndex(string $uri, array $postmanJson): int
    {
        if (false !== $index = array_search($this->getFolderName($uri), array_column($postmanJson, 'name'), false)) {
            return $index;
        }

        return 0;
    }

    private function getApiName(string $uri): string
    {
        $uri = str_replace('-', '', $uri);
        $removeApiPart = explode('/', $uri);
        unset($removeApiPart[0]);

        return StringTools::snakeToCamelCase(implode('_', $removeApiPart), true);
    }

    private function saveJsonFile(array $data, string $fileName): void
    {
        Storage::disk(config('postman.storage_disk'))->put($fileName . '.json', json_encode($data, JSON_PRETTY_PRINT));
    }
}
