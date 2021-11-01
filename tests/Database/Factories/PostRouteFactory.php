<?php

namespace Matijajanc\Postman\Tests\Database\Factories;

class PostRouteFactory
{
    public function definition(): array
    {
        return [
            'parameter1' => 'test',
            'parameter2' => 'test2',
        ];
    }

    public function postWithMethodName(): array
    {
        return [
            'parameter3' => 'test3',
            'parameter4' => 'test4',
        ];
    }
}
