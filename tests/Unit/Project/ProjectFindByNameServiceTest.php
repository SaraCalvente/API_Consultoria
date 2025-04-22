<?php

namespace App\Tests\Unit\Project;

use App\Client\Domain\Client;
use App\Project\Application\Project\ProjectFindByNameService;
use App\Project\Domain\Model\ProjectRepositoryInterface;
use App\Project\Domain\Project\Project;
use App\Project\Domain\Status;
use Codeception\Test\Unit;
use PHPUnit\Framework\MockObject\Exception;
use Symfony\Component\HttpFoundation\JsonResponse;

class ProjectFindByNameServiceTest extends Unit
{
    private ProjectRepositoryInterface $projectRepository;
    private ProjectFindByNameService $service;

    /**
     * @throws Exception
     */
    protected function setUp(): void
    {
        parent::setUp();
        $this->projectRepository = $this->createMock(ProjectRepositoryInterface::class);
        $this->service = new ProjectFindByNameService($this->projectRepository);
    }

    public function testReturnsProjectByName(): void
    {
        $client = $this->createMock(Client::class);
        $client->method('getId')->willReturn(1);

        $project = (new Project())
            ->setClient($client)
            ->setName('Example Project')
            ->setDescription('A test project')
            ->setStartDate(new \DateTime('2025-01-01'))
            ->setStatus(Status::PENDIENTE);

        $this->projectRepository
            ->method('findProjectByName')
            ->with('Example Project')
            ->willReturn($project);

        $response = ($this->service)(['name' => 'Example Project']);

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(200, $response->getStatusCode());

        $content = json_decode($response->getContent(), true);

        $this->assertEquals('Project retrieved successfully', $content['message']);
        $this->assertEquals('Example Project', $content['project']['name']);
        $this->assertEquals('A test project', $content['project']['description']);
        $this->assertEquals('2025-01-01', $content['project']['start_date']);
    }
}

