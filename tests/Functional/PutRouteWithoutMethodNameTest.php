<?php

namespace Matijajanc\Postman\Tests\Functional;

class PutRouteWithoutMethodNameTest extends AbstractTest
{
    public function testPutRouteWithoutMethodName(): void
    {
        $this->generatePostmanFile();

        $readFile = file_get_contents(__DIR__ . '/../postman.json');
        $readFileArray = json_decode($readFile, true);

        $this->assertEquals(
            $readFileArray,
            $this->updatePostmanId('put-route-without-method-name.json', $readFileArray['info']['_postman_id'])
        );
    }

    protected function defineRoutes($router)
    {
        $router->put(
            'api/put-route-without-method-name',
            [
                'uses' => '\Matijajanc\Postman\Tests\Http\Controllers\TestController@putRouteWithoutMethodName'
            ]
        );
    }
}
