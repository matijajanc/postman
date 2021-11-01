<?php

declare(strict_types=1);

namespace Matijajanc\Postman\Tests\Functional;

use Matijajanc\Postman\Postman;
use Matijajanc\Postman\Tests\TestCase;
use Psr\Log\LoggerInterface;

abstract class AbstractTest extends TestCase
{
    private LoggerInterface $logger;

    public function setUp(): void
    {
        parent::setUp();
        $this->logger = $this->getMockForAbstractClass(LoggerInterface::class);
    }

    protected function generatePostmanFile(): void
    {
        $postman = $this->getMockBuilder(Postman::class)
            ->setMethods(['__construct'])
            ->setConstructorArgs(['$logger'])
            ->disableOriginalConstructor()
            ->getMock();

        $postman->generatePostmanJson();
    }
    
    protected function updatePostmanId(string $fileName, string $postmanId): array
    {
        $postmanFileSample = file_get_contents(__DIR__ . '/../Postman-files/'. $fileName);
        $postmanFileSampleArray = json_decode($postmanFileSample, true);
        $postmanFileSampleArray['info']['_postman_id'] = $postmanId;

        return $postmanFileSampleArray;
    }
}
