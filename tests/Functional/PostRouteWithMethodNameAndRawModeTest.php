<?php

namespace Matijajanc\Postman\Tests\Functional;

class PostRouteWithMethodNameAndRawModeTest extends AbstractTest
{
    public function testPostRouteWithMethodNameAndRawMode(): void
    {
        $this->generatePostmanFile();

        $readFile = file_get_contents(__DIR__ . '/../postman.json');
        $readFileArray = json_decode($readFile, true);

        $this->assertEquals(
            $readFileArray,
            $this->updatePostmanId('post-route-with-method-name-and-raw-mode.json', $readFileArray['info']['_postman_id'])
        );
    }

    protected function defineRoutes($router)
    {
        $router->post(
            'api/post-route-with-method-name-and-raw-mode',
            [
                'uses' => '\Matijajanc\Postman\Tests\Http\Controllers\TestController@postRouteWithMethodNameAndRawMode'
            ]
        );
    }
}
