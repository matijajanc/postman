<?php

declare(strict_types=1);

namespace Matijajanc\Postman\Tests;

use Matijajanc\Postman\Providers\PostmanServiceProvider;

class TestCase extends \Orchestra\Testbench\TestCase
{
    public function setUp(): void
    {
        parent::setUp();
        config(['postman.storage_location' => __DIR__]);
    }

    /**
     * Get package providers.
     *
     * @param  \Illuminate\Foundation\Application  $app
     *
     * @return array
     */
    protected function getPackageProviders($app)
    {
        return [
            PostmanServiceProvider::class,
        ];
    }
}
