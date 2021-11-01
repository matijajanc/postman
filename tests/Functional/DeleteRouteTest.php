<?php

declare(strict_types=1);

namespace Matijajanc\Postman\Tests\Functional;

class DeleteRouteTest extends AbstractTest
{
    public function testDeleteRoute(): void
    {
        $this->generatePostmanFile();
        
        $readFile = file_get_contents(__DIR__ . '/../postman.json');
        $readFileArray = json_decode($readFile, true);

        $this->assertEquals(
            $readFileArray,
            $this->updatePostmanId('delete-route.json', $readFileArray['info']['_postman_id'])
        );
    }

    protected function defineRoutes($router)
    {
        $router->delete(
            'api/delete-route/{id}',
            [
                'uses' => '\Matijajanc\Postman\Tests\Http\Controllers\TestController@deleteRoute'
            ]
        );
    }
}
