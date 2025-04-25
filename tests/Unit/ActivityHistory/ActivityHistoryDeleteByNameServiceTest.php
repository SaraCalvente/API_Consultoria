<?php

namespace App\Tests\Unit\ActivityHistory;

use App\ActivityHistory\Application\ActivityHistoryCreateService;
use App\ActivityHistory\Application\ActivityHistoryDeleteByNameService;
use App\ActivityHistory\Domain\ActivityHistory;
use App\ActivityHistory\Domain\Model\ActivityHistoryRepositoryInterface;
use App\Consultant\Domain\Model\ConsultantRepositoryInterface;
use App\Project\Domain\Model\ProjectRepositoryInterface;
use App\Project\Domain\Project\Project;
use App\User\Domain\Model\UserRepositoryInterface;
use App\User\Domain\User;
use Codeception\Test\Unit;
use phpDocumentor\Reflection\Types\This;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\JsonResponse;

class ActivityHistoryDeleteByNameServiceTest extends Unit
{

    private ActivityHistoryRepositoryInterface $activityHistoryRepository;
    private ProjectRepositoryInterface $projectRepository;

    private ActivityHistoryDeleteByNameService $service;
    private array $data;

    /**
     * @throws Exception
     */
    protected function setUp(): void
    {
        $this->activityHistoryRepository = $this->createMock(ActivityHistoryRepositoryInterface::class);
        $this->projectRepository = $this->createMock(ProjectRepositoryInterface::class);

        $this->service = new ActivityHistoryDeleteByNameService($this->activityHistoryRepository, $this->projectRepository);
        $this->data = [
            'projectName' => 'Project A',
            'name' => 'Activity 1'
        ];
    }
    /**
     * @throws Exception
     */
    public function testDeletesActivitySuccessfully(): void
    {
        $project = $this->createMock(Project::class);
        $activity = $this->createMock(ActivityHistory::class);

        $this->projectRepository->method('checkIfProjectExists')->willReturn(true);
        $this->projectRepository->method('findProjectByName')->willReturn($project);

        $this->activityHistoryRepository->method('checkIfActivityHistoryFromProjectExists')->willReturn(true);
        $this->activityHistoryRepository->method('findActivityHistoryFromProject')->willReturn($activity);
        $this->activityHistoryRepository->expects($this->once())->method('removeActivityHistory')->with($activity);

        $response = ($this->service)($this->data);

        $this->assertEquals(201, $response->getStatusCode());

        $expected = ['message' => 'Activity deleted successfully'];
        $this->assertEquals($expected, json_decode($response->getContent(), true));
    }

    public function testReturns404IfProjectNotFound(): void
    {

        $this->projectRepository->method('checkIfProjectExists')->willReturn(false);

        $response = ($this->service)($this->data);

        $this->assertEquals(404, $response->getStatusCode());
        $this->assertStringContainsString('Project with name', $response->getContent());
    }

    /**
     * @throws \DateMalformedStringException
     * @throws Exception
     */
    public function testReturns404IfActivityNotFound(): void
    {
        $project = $this->createMock(Project::class);

        $this->projectRepository->method('checkIfProjectExists')->willReturn(true);
        $this->projectRepository->method('findProjectByName')->willReturn($project);
        $this->activityHistoryRepository->method('checkIfActivityHistoryFromProjectExists')->willReturn(false);

        $response = ($this->service)($this->data);

        $this->assertEquals(404, $response->getStatusCode());
        $this->assertStringContainsString('not found', $response->getContent());
    }
}
