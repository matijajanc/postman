<?php

declare(strict_types=1);

namespace Matijajanc\Postman;

use Illuminate\Support\Str;
use Matijajanc\Postman\Helpers\StringTools;

abstract class PostmanAbstract
{
    protected function getControllerName(array|object $route): string
    {
        if (is_array($route)) {
            return explode('@', $route['action']['uses'])[0] ?? '';
        }

        return $route->getController();
    }

    protected function getMethodName(array|object $route): string
    {
        if (is_array($route)) {
            return explode('@', $route['action']['uses'])[1] ?? '';
        }

        return $route->getActionMethod();
    }

    protected function getMethodParameters(string $controller, string $method): array
    {
        $parameters = [];
        try {
            $reflector = new \ReflectionMethod($controller, $method);
            foreach ($reflector->getAttributes() as $attributes) {
                if (str_contains($attributes->getName(), 'Postman')) {
                    $parameters = $attributes->getArguments()[0] ?? [];
                }
            }
        } catch (\Throwable) {
        }

        return $parameters;
    }

    protected function getRequestData(array $parameters): array
    {
        $data = [];
        if ($parameters['factory'] ?? false) {
            $factoryClass = new $parameters['factory'];
            $data = $factoryClass->{method_exists(
                $parameters['factory'],
                $parameters['method'] ?? ''
            ) ? $parameters['method'] : 'definition'}();
        }

        return $data;
    }

    protected function isUriExcluded(string $uri): bool
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

    protected function addAuthBlock(array $middlewares): bool
    {
        return in_array(config('postman.middleware'), $middlewares, true);
    }

    protected function setEnvironmentInfoBlock(): array
    {
        return [
            'id' => Str::uuid()->toString(),
            'name' => env('APP_NAME'),
            '_postman_variable_scope' => 'environment',
        ];
    }

    protected function setInfoBlock(): array
    {
        return [
            'info' => [
                '_postman_id' => Str::uuid()->toString(),
                'name' => env('APP_NAME'),
                'schema' => 'https://schema.getpostman.com/json/collection/v2.1.0/collection.json',
            ],
        ];
    }

    protected function setVariable(array &$data, string $name, string $value): void
    {
        $data['values'][] = [
            'key' => $name,
            'value' => $value,
            'description' => '',
            'enabled' => true,
        ];
    }

    protected function setItem(
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

    protected function setApiName(string $name): array
    {
        return [
            'name' => $name,
        ];
    }

    protected function setLoginEventBlock(): array
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

    protected function setRequest(string $uri, string $method, string $mode, array $data, bool $setAuth): array
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

    protected function setAuth(): array
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

    protected function setMethod(string $type): array
    {
        return ['method' => $type];
    }

    protected function setJsonHeader(): array
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

    protected function setBody(array $data, string $mode): array
    {
        return [
            'body' => [
                'mode' => $mode,
                $mode => $mode !== 'raw' ? $this->setFormData($data) : $this->setRawData($data),
            ],
        ];
    }

    protected function setFormData(array $data): array
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

    protected function setRawData(array $data): string
    {
        return json_encode($data, JSON_PRETTY_PRINT);
    }

    protected function setUrlBlock(string $uri): array
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

    protected function setResponse(): array
    {
        return [
            'response' => [],
        ];
    }

    protected function getUriPathArray(string $uri): array
    {
        return explode('/', $this->getFormattedUri($uri));
    }

    protected function getFolderName(string $uri): string
    {
        $removeApiPart = explode('/', $this->getFormattedUri($uri));

        return ucfirst($removeApiPart[1] ?? 'other');
    }

    protected function getFolderIndex(string $uri, array $postmanJson): int
    {
        if (false !== $index = array_search($this->getFolderName($uri), array_column($postmanJson, 'name'), false)) {
            return $index;
        }

        return 0;
    }

    protected function getApiName(string $uri): string
    {
        $uri = str_replace('-', '', $uri);
        $removeApiPart = explode('/', $uri);
        unset($removeApiPart[0]);

        return StringTools::snakeToCamelCase(implode('_', $removeApiPart), true);
    }
    
    protected function getFormattedUri(string $uri): string
    {
        return str_starts_with($uri, '/') ? substr($uri, 1) : $uri;
    }

    protected function saveJsonFile(array $data, string $fileName): void
    {
        file_put_contents(
            config('postman.storage_location') . '/' . $fileName . '.json',
            json_encode($data, JSON_PRETTY_PRINT)
        );
    }
}
