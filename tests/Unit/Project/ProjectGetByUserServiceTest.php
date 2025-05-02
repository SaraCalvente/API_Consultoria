<?php

namespace App\Tests\Unit\Project;

use App\Client\Domain\Client;
use App\Client\Domain\Model\ClientRepositoryInterface;
use App\Consultant\Domain\Consultant\Consultant;
use App\Consultant\Domain\Model\ConsultantRepositoryInterface;
use App\Project\Application\Project\ProjectGetByUserService;
use App\Project\Domain\Model\ProjectRepositoryInterface;
use App\Project\Domain\Project\Project;
use App\Project\Domain\Status;
use App\User\Domain\User;
use Codeception\Test\Unit;
use Doctrine\Common\Collections\ArrayCollection;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\JsonResponse;

class ProjectGetByUserServiceTest extends Unit
{
    private ProjectRepositoryInterface $projectRepository;
    private ClientRepositoryInterface $clientRepository;
    private ConsultantRepositoryInterface $consultantRepository;
    private ProjectGetByUserService $service;

    /**
     * @throws Exception
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->projectRepository = $this->createMock(ProjectRepositoryInterface::class);
        $this->clientRepository = $this->createMock(ClientRepositoryInterface::class);
        $this->consultantRepository = $this->createMock(ConsultantRepositoryInterface::class);

        $this->service = new ProjectGetByUserService(
            $this->projectRepository,
            $this->clientRepository,
            $this->consultantRepository
        );
    }

    /**
     * @throws Exception
     */
    public function testReturnsProjectsForClientUser(): void
    {
        $user = $this->createMock(User::class);
        $client = $this->createMock(Client::class);
        $client->method('getId')->willReturn(1);

        $project = (new Project())
            ->setClient($client)
            ->setName('Client Project')
            ->setDescription('A project')
            ->setStartDate(new \DateTime('2024-03-01'))
            ->setStatus(Status::PENDIENTE);

        $this->clientRepository->method('checkIfClientExists')->with($user)->willReturn(true);
        $this->clientRepository->method('findClientByUser')->with($user)->willReturn($client);

        $this->consultantRepository->method('checkIfConsultantExists')->with($user)->willReturn(false);

        $this->projectRepository
            ->method('findProjectByClient')
            ->with($client)
            ->willReturn([$project]);

        $response = ($this->service)($user);

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(200, $response->getStatusCode());

        $content = json_decode($response->getContent(), true);
        $this->assertEquals('Projects retrieved successfully', $content['message']);
        $this->assertEquals(1, $content['current_client_id']);
        $this->assertCount(1, $content['projects']);
        $this->assertEquals('Client Project', $content['projects'][0]['name']);
    }

    /**
     * @throws Exception
     */
    public function testReturnsProjectsForConsultantUser(): void
    {
        $user = $this->createMock(User::class);
        $consultant = $this->createMock(Consultant::class);
        $consultant->method('getId')->willReturn(1);

        $client = $this->createMock(Client::class);
        $client->method('getId')->willReturn(1);

        $project = (new Project())
        ->setClient($client)
            ->setName('Consultant Project')
            ->setDescription('A project')
            ->setStartDate(new \DateTime('2024-03-01'))
            ->setStatus(Status::PENDIENTE);

        $this->clientRepository->method('checkIfClientExists')->with($user)->willReturn(false);
        $this->consultantRepository->method('checkIfConsultantExists')->with($user)->willReturn(true);
        $this->consultantRepository->method('findConsultantByUser')->with($user)->willReturn($consultant);

        $consultant->method('getProject')->willReturn(new ArrayCollection([$project]));

        $response = ($this->service)($user);

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(200, $response->getStatusCode());

        $content = json_decode($response->getContent(), true);
        $this->assertEquals('Projects retrieved successfully', $content['message']);
        $this->assertEquals(1, $content['current_consultant_id']);
        $this->assertCount(1, $content['projects']);
        $this->assertEquals('Consultant Project', $content['projects'][0]['name']);
    }



    /**
     * @throws Exception
     */
    public function testReturns404IfUserHasNoProjects(): void
    {
        $user = $this->createMock(User::class);

        $this->clientRepository->method('checkIfClientExists')->with($user)->willReturn(false);
        $this->consultantRepository->method('checkIfConsultantExists')->with($user)->willReturn(false);

        $response = ($this->service)($user);

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(404, $response->getStatusCode());

        $content = json_decode($response->getContent(), true);
        $this->assertEquals('User has no associated projects', $content['error']);
    }
}
