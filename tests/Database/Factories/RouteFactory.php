<?php

declare(strict_types=1);

namespace Matijajanc\Postman\Tests\Database\Factories;

class RouteFactory
{
    public function getRouteWithParameters(): array
    {
        return [
            'parameter1' => 'test',
            'parameter2' => 'test2',
        ];
    }
}
