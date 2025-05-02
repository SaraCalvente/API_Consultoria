<?php

namespace App\Tests\Unit\Project;

use App\Client\Domain\Client;
use App\Consultant\Domain\Consultant\Consultant;
use App\Consultant\Domain\Model\ConsultantRepositoryInterface;
use App\Project\Application\Project\ProjectUpdateByNameService;
use App\Project\Domain\Model\ProjectRepositoryInterface;
use App\Project\Domain\Project\Project;
use App\Project\Domain\Status;
use App\User\Domain\Model\UserRepositoryInterface;
use App\User\Domain\User;
use Codeception\Test\Unit;
use Doctrine\Common\Collections\ArrayCollection;
use PHPUnit\Framework\MockObject\Exception;
use Symfony\Component\HttpFoundation\JsonResponse;

class ProjectUpdateByNameServiceTest extends Unit
{
    private ProjectRepositoryInterface $projectRepository;
    private ConsultantRepositoryInterface $consultantRepository;
    private UserRepositoryInterface $userRepository;
    private ProjectUpdateByNameService $service;

    /**
     * @throws Exception
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->projectRepository = $this->createMock(ProjectRepositoryInterface::class);
        $this->consultantRepository = $this->createMock(ConsultantRepositoryInterface::class);
        $this->userRepository = $this->createMock(UserRepositoryInterface::class);
        $this->service = new ProjectUpdateByNameService(
            $this->projectRepository,
            $this->consultantRepository,
            $this->userRepository
        );
    }

    /**
     * @throws \DateMalformedStringException
     * @throws Exception
     */
    public function testUpdateProjectSuccessfully(): void
    {
        $projectName = 'ProjectX';
        $emailToAdd = 'new.consultant@example.com';
        $emailToRemove = 'old.consultant@example.com';

        $userToAdd = $this->createMock(User::class);
        $userToRemove = $this->createMock(User::class);

        $consultantToAdd = $this->createMock(Consultant::class);
        $consultantToRemove = $this->createMock(Consultant::class);

        $client = $this->createMock(Client::class);
        $client->method('getId')->willReturn(999);

        $consultantToRemove->method('getId')->willReturn(10);
        $consultantToAdd->method('getId')->willReturn(20);

        $project = $this->createMock(Project::class);
        $project->method('getName')->willReturn($projectName);
        $project->method('getStartDate')->willReturn(new \DateTime('2025-01-01'));
        $project->method('getEndDate')->willReturn(new \DateTime('2025-12-31'));
        $project->method('getId')->willReturn(123);
        $project->method('getDescription')->willReturn('Updated description');
        $project->method('getStatus')->willReturn(Status::EN_PROCESO);
        $project->method('getConsultant')->willReturn(new ArrayCollection([$consultantToAdd]));
        $project->method('getClient')->willReturn($client);


        $project->expects($this->once())->method('setDescription')->with('Updated description');
        $project->expects($this->once())->method('setStatus')->with(Status::EN_PROCESO);
        $project->expects($this->once())->method('setEndDate')->with(new \DateTime('2025-12-31'));

        $this->userRepository->method('findUserByEmailOrFail')->willReturnMap([
            [$emailToAdd, $userToAdd],
            [$emailToRemove, $userToRemove],
        ]);

        $this->consultantRepository->method('findConsultantByUser')->willReturnMap([
            [$userToAdd, $consultantToAdd],
            [$userToRemove, $consultantToRemove],
        ]);


        $this->projectRepository
            ->method('findProjectByName')
            ->with($projectName)
            ->willReturn($project);

        $this->projectRepository
            ->method('checkIfProjectExists')
            ->with($projectName)
            ->willReturn(true);

        $this->projectRepository
            ->method('checkDates')
            ->willReturn(true);

        $this->projectRepository
            ->expects($this->once())
            ->method('saveProject');

        $data = [
            'name' => $projectName,
            'description' => 'Updated description',
            'status' => Status::EN_PROCESO->value,
            'endDate' => '2025-12-31',
            'addConsultantsEmails' => [$emailToAdd],
            'erraseConsultantsEmails' => [$emailToRemove],
        ];

        $response = ($this->service)($data);

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(200, $response->getStatusCode());

        $content = json_decode($response->getContent(), true);
        $this->assertEquals('Project updated successfully', $content['message']);
        $this->assertArrayHasKey('project', $content);

        $this->assertEquals(123, $content['project']['project_id']);
        $this->assertEquals(999, $content['project']['client_id']);
        $this->assertEquals('Updated description', $content['project']['description']);
    }

    /**
     * @throws \DateMalformedStringException
     * @throws Exception
     */
    public function testReturnsErrorIfProjectDoesNotExist(): void
    {
        $project = $this->createMock(Project::class);
        $project->method('getName')->willReturn('FakeProject');

        $this->projectRepository
            ->method('findProjectByName')
            ->willReturn($project);

        $this->projectRepository
            ->method('checkIfProjectExists')
            ->with('FakeProject')
            ->willReturn(false);

        $data = ['name' => 'FakeProject'];

        $response = ($this->service)($data);

        $this->assertEquals(400, $response->getStatusCode());
        $content = json_decode($response->getContent(), true);
        $this->assertEquals('The project FakeProject does not exist', $content['error']);
    }

    /**
     * @throws \DateMalformedStringException
     * @throws Exception
     */
    public function testReturnsErrorIfDatesAreInvalid(): void
    {
        $project = $this->createMock(Project::class);
        $project->method('getName')->willReturn('ProjectX');
        $project->method('getStartDate')->willReturn(new \DateTime('2025-01-01'));

        $this->projectRepository
            ->method('findProjectByName')
            ->willReturn($project);

        $this->projectRepository
            ->method('checkIfProjectExists')
            ->willReturn(true);

        $this->projectRepository
            ->method('checkDates')
            ->willReturn(false);

        $data = [
            'name' => 'ProjectX',
            'description' => null,
            'status' => null,
            'endDate' => '2024-01-01',
            'addConsultantsEmails' => null,
            'erraseConsultantsEmails' => null,
        ];

        $response = ($this->service)($data);

        $this->assertEquals(405, $response->getStatusCode());
        $content = json_decode($response->getContent(), true);
        $this->assertEquals('Invalid date range or format (Y-m-d)', $content['error']);
    }
}
