<?php

declare(strict_types=1);

namespace Matijajanc\Postman\Tests\Functional;

class GetRouteWithQueryParametersTest extends AbstractTest
{
    public function testGetRouteWithQueryParameters(): void
    {
        $this->generatePostmanFile();

        $readFile = file_get_contents(__DIR__ . '/../postman.json');
        $readFileArray = json_decode($readFile, true);

        $this->assertEquals(
            $readFileArray,
            $this->updatePostmanId('get-route-with-query-parameters.json', $readFileArray['info']['_postman_id'])
        );
    }

    protected function defineRoutes($router)
    {
        $router->get(
            'api/get-route-with-query-parameters',
            [
                'uses' => '\Matijajanc\Postman\Tests\Http\Controllers\TestController@getRouteWithParameters'
            ]
        );
    }
}
