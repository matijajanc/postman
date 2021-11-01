<?php

namespace Matijajanc\Postman\Tests\Functional;

class PostRouteWithMiddlewareTest extends AbstractTest
{
    public function testPostRouteWithMidddlewareName(): void
    {
        $this->generatePostmanFile();

        $readFile = file_get_contents(__DIR__ . '/../postman.json');
        $readFileArray = json_decode($readFile, true);

        $this->assertEquals(
            $readFileArray,
            $this->updatePostmanId('post-route-with-middleware.json', $readFileArray['info']['_postman_id'])
        );
    }

    protected function defineRoutes($router)
    {
        $router->post(
            'api/post-route-with-middleware',
            [
                'uses' => '\Matijajanc\Postman\Tests\Http\Controllers\TestController@postRouteWithMiddleware',
                'middleware' => ['api'],
            ]
        );
    }
}
