<?php

declare(strict_types=1);

namespace Matijajanc\Postman\Tests\Database\Factories;

class PutRouteFactory
{
    public function definition(): array
    {
        return [
            'update-parameter1' => 'updated-test',
            'update-parameter2' => 'updated-test2',
        ];
    }
}
