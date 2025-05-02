<?php

declare(strict_types=1);

namespace App\Tests\Unit\Consultant;

use App\Consultant\Application\Consultant\ConsultantDeleteByIdService;
use App\Consultant\Domain\Consultant\Consultant;
use App\Consultant\Domain\Model\ConsultantRepositoryInterface;
use App\Project\Domain\Model\ProjectRepositoryInterface;
use App\User\Domain\User;
use Codeception\Test\Unit;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\JsonResponse;

class ConsultantDeleteByIdServiceTest extends Unit
{
    private ConsultantRepositoryInterface $consultantRepository;
    private ProjectRepositoryInterface $projectRepository;
    private ConsultantDeleteByIdService $service;

    /**
     * @throws Exception
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->consultantRepository = $this->createMock(ConsultantRepositoryInterface::class);
        $this->projectRepository = $this->createMock(ProjectRepositoryInterface::class);

        $this->service = new ConsultantDeleteByIdService(
            $this->consultantRepository,
            $this->projectRepository
        );
    }

    /**
     * @throws Exception
     */
    public function testDeletesConsultantWhenNoProjectsAssigned(): void
    {
        $user = $this->createMock(User::class);
        $consultant = $this->createMock(Consultant::class);
        $expectedResponse = new JsonResponse(null, 200);

        $this->consultantRepository
            ->method('findConsultantByUser')
            ->with($user)
            ->willReturn($consultant);

        $this->projectRepository
            ->method('checkIfConsultantHasProjects')
            ->with($consultant)
            ->willReturn(new JsonResponse(null, 200));

        $this->consultantRepository
            ->method('deleteConsultant')
            ->with($consultant)
            ->willReturn($expectedResponse);

        $response = ($this->service)($user);

        $this->assertEquals($expectedResponse->getContent(), $response->getContent());
        $this->assertEquals($expectedResponse->getStatusCode(), $response->getStatusCode());
    }

    /**
     * @throws Exception
     */
    public function testReturnsProjectsIfConsultantHasProjects(): void
    {
        $user = $this->createMock(User::class);
        $consultant = $this->createMock(Consultant::class);
        $projects = ['project1', 'project2'];
        $projectsResponse = new JsonResponse($projects, 200);

        $this->consultantRepository
            ->method('findConsultantByUser')
            ->with($user)
            ->willReturn($consultant);

        $this->projectRepository
            ->method('checkIfConsultantHasProjects')
            ->with($consultant)
            ->willReturn($projectsResponse);

        $response = ($this->service)($user);

        $this->assertEquals($projectsResponse->getContent(), $response->getContent());
        $this->assertEquals($projectsResponse->getStatusCode(), $response->getStatusCode());
    }
}
