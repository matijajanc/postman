<?php

declare(strict_types=1);

namespace Matijajanc\Postman\Tests\Functional;

class GetRouteTest extends AbstractTest
{
    public function testSimpleGetRoute(): void
    {
        $this->generatePostmanFile();

        $readFile = file_get_contents(__DIR__ . '/../postman.json');
        $readFileArray = json_decode($readFile, true);

        $this->assertEquals(
            $readFileArray,
            $this->updatePostmanId('get-simple-route.json', $readFileArray['info']['_postman_id'])
        );
    }

    protected function defineRoutes($router)
    {
        $router->get(
            'api/get-simple-route',
            [
                'uses' => '\Matijajanc\Postman\Tests\Http\Controllers\TestController@getSimpleRoute'
            ]
        );
    }
}
