<?php

namespace Matijajanc\Postman\Tests\Functional;

class PostRouteWithoutMethodNameTest extends AbstractTest
{
    public function testPostRouteWithoutMethodName(): void
    {
        $this->generatePostmanFile();

        $readFile = file_get_contents(__DIR__ . '/../postman.json');
        $readFileArray = json_decode($readFile, true);

        $this->assertEquals(
            $readFileArray,
            $this->updatePostmanId('post-route-without-method-name.json', $readFileArray['info']['_postman_id'])
        );
    }

    protected function defineRoutes($router)
    {
        $router->post(
            'api/post-route-without-method-name',
            [
                'uses' => '\Matijajanc\Postman\Tests\Http\Controllers\TestController@postRouteWithoutMethodName'
            ]
        );
    }
}
