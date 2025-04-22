<?php

namespace App\Tests\Unit\Project;

use App\Client\Domain\Client;
use App\Project\Application\Project\ProjectFindAllService;
use App\Project\Domain\Model\ProjectRepositoryInterface;
use App\Project\Domain\Project\Project;
use App\Project\Domain\Status;
use Codeception\Test\Unit;
use PHPUnit\Framework\MockObject\Exception;
use Symfony\Component\HttpFoundation\JsonResponse;

class ProjectFindAllServiceTest extends Unit
{
    private ProjectRepositoryInterface $projectRepository;
    private ProjectFindAllService $service;

    /**
     * @throws Exception
     */
    protected function setUp(): void
    {
        parent::setUp();
        $this->projectRepository = $this->createMock(ProjectRepositoryInterface::class);
        $this->service = new ProjectFindAllService($this->projectRepository);
    }

    /**
     * @throws Exception
     */
    public function testReturnsAllProjectsAsJson(): void
    {
        $client = $this->createMock(Client::class);
        $client->method('getId')->willReturn(1);

        $project1 = (new Project())
            ->setClient($client)
            ->setName('Project One')
            ->setDescription('Description One')
            ->setStartDate(new \DateTime('2024-01-01'))
            ->setStatus(Status::PENDIENTE);

        $project2 = (new Project())
            ->setClient($client)
            ->setName('Project Two')
            ->setDescription('Description Two')
            ->setStartDate(new \DateTime('2024-02-01'))
            ->setStatus(Status::EN_PROCESO);

        $this->projectRepository
            ->method('findAllProjects')
            ->willReturn([$project1, $project2]);

        $response = ($this->service)();

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(200, $response->getStatusCode());

        $content = json_decode($response->getContent(), true);
        $this->assertIsArray($content);
        $this->assertCount(2, $content);
        $this->assertEquals('Project One', $content[0]['name']);
        $this->assertEquals('Project Two', $content[1]['name']);
    }

}
