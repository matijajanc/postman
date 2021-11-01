<?php

namespace Matijajanc\Postman\Tests\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Matijajanc\Postman\Tests\Database\Factories\PostRouteFactory;
use Matijajanc\Postman\Tests\Database\Factories\PutRouteFactory;
use Matijajanc\Postman\Tests\Database\Factories\RouteFactory;

class TestController
{
    public function getSimpleRoute(): JsonResponse
    {
        return new JsonResponse();
    }

    #[Postman(['factory' => RouteFactory::class, 'method' => 'getRouteWithParameters'])]
    public function getRouteWithParameters(): JsonResponse
    {
        return new JsonResponse();
    }

    #[Postman(['factory' => PostRouteFactory::class])]
    public function postRouteWithoutMethodName(): JsonResponse
    {
        return new JsonResponse();
    }

    #[Postman(['factory' => PostRouteFactory::class, 'method' => 'postWithMethodName'])]
    public function postRouteWithMethodName(): JsonResponse
    {
        return new JsonResponse();
    }

    #[Postman(['factory' => PostRouteFactory::class, 'method' => 'postWithMethodName', 'mode' => 'raw'])]
    public function postRouteWithMethodNameAndRawMode(): JsonResponse
    {
        return new JsonResponse();
    }

    #[Postman(['factory' => PostRouteFactory::class])]
    public function postRouteWithMiddleware(): JsonResponse
    {
        return new JsonResponse();
    }

    #[Postman(['factory' => PutRouteFactory::class])]
    public function putRouteWithoutMethodName(): JsonResponse
    {
        return new JsonResponse();
    }

    public function deleteRoute(int $id): JsonResponse
    {
        return new JsonResponse();
    }
}
