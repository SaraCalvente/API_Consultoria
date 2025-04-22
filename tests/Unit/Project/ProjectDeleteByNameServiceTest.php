<?php

namespace App\Tests\Unit\Project;

use App\Consultant\Domain\Consultant\Consultant;
use App\Project\Application\Project\ProjectDeleteByNameService;
use App\Project\Domain\Model\ProjectRepositoryInterface;
use App\Project\Domain\Project\Project;
use Codeception\Test\Unit;
use Symfony\Component\HttpFoundation\JsonResponse;
use PHPUnit\Framework\MockObject\Exception;
use Doctrine\Common\Collections\ArrayCollection;

class ProjectDeleteByNameServiceTest extends Unit
{
    private ProjectRepositoryInterface $projectRepository;
    private ProjectDeleteByNameService $service;

    /**
     * @throws Exception
     */
    protected function setUp(): void
    {
        parent::setUp();
        $this->projectRepository = $this->createMock(ProjectRepositoryInterface::class);
        $this->service = new ProjectDeleteByNameService($this->projectRepository);
    }

    public function testReturns400IfProjectDoesNotExist(): void
    {
        $this->projectRepository
            ->method('checkIfProjectExists')
            ->willReturn(false);

        $response = ($this->service)(['name' => 'NonexistentProject']);

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(400, $response->getStatusCode());

        $data = json_decode($response->getContent(), true);
        $this->assertStringContainsString('does not exist', $data['error']);
    }

    /**
     * @throws Exception
     */
    public function testReturns201IfProjectIsDeleted(): void
    {
        $projectName = 'ImportantProject';

        $mockConsultant = $this->createMock(Consultant::class);
        $consultants = new ArrayCollection([$mockConsultant]);

        $project = $this->createMock(Project::class);
        $project->method('getConsultant')->willReturn($consultants);

        // Se espera que se llame a removeConsultant por cada consultor
        $project->expects($this->once())
            ->method('removeConsultant')
            ->with($mockConsultant);

        $this->projectRepository
            ->method('checkIfProjectExists')
            ->willReturn(true);

        $this->projectRepository
            ->method('findProjectByName')
            ->willReturn($project);

        $this->projectRepository
            ->expects($this->once())
            ->method('removeProject')
            ->with($project);

        $response = ($this->service)(['name' => $projectName]);

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(201, $response->getStatusCode());

        $data = json_decode($response->getContent(), true);
        $this->assertEquals('Project deleted successfully', $data['message']);
    }
}
