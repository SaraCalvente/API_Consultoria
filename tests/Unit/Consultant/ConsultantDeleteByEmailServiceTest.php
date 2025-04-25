<?php
declare(strict_types=1);

namespace App\Tests\Unit\Consultant;

use App\Consultant\Application\Consultant\ConsultantDeleteByEmailService;
use App\Consultant\Domain\Consultant\Consultant;
use App\Consultant\Domain\Model\ConsultantRepositoryInterface;
use App\Project\Domain\Model\ProjectRepositoryInterface;
use App\User\Domain\Model\UserRepositoryInterface;
use App\User\Domain\User;
use App\User\Domain\ValueObject\EmailValueObject;
use Codeception\Test\Unit;
use PHPUnit\Framework\MockObject\Exception;
use Symfony\Component\HttpFoundation\JsonResponse;

class ConsultantDeleteByEmailServiceTest extends Unit
{
    private ConsultantRepositoryInterface $consultantRepository;
    private ProjectRepositoryInterface $projectRepository;
    private UserRepositoryInterface $userRepository;
    private ConsultantDeleteByEmailService $service;

    /**
     * @throws Exception
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->consultantRepository = $this->createMock(ConsultantRepositoryInterface::class);
        $this->projectRepository = $this->createMock(ProjectRepositoryInterface::class);
        $this->userRepository = $this->createMock(UserRepositoryInterface::class);

        $this->service = new ConsultantDeleteByEmailService(
            $this->consultantRepository,
            $this->projectRepository,
            $this->userRepository
        );
    }

    /**
     * @throws Exception
     */
    public function testDeleteConsultantSuccessfully(): void
    {
        $email = 'consultant@example.com';

        $data = ['email' => $email];

        $emailValueObject = $this->createMock(EmailValueObject::class);
        $emailValueObject->method('__toString')->willReturn($email);

        $user = $this->createMock(User::class);
        $user->method('getEmail')->willReturn($emailValueObject);

        $consultant = $this->createMock(Consultant::class);
        $consultant->method('getId')->willReturn(123);

        $this->userRepository->method('findUserByEmail')->with($email)->willReturn($user);
        $this->consultantRepository->method('findConsultantByUser')->with($user)->willReturn($consultant);

        $this->projectRepository->method('checkIfConsultantHasProjects')->with($consultant)->willReturn(null);

        $this->consultantRepository->method('deleteConsultant')->with($consultant)->willReturn(new JsonResponse(['message' => 'Consultant deleted successfully'], 200));

        $response = ($this->service)($data);

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals(['message' => 'Consultant deleted successfully'], json_decode($response->getContent(), true));
    }

    /**
     * @throws Exception
     */
    public function testCannotDeleteConsultantWithProjects(): void
    {
        $email = 'consultant@example.com';
        $data = ['email' => $email];

        $emailValueObject = $this->createMock(EmailValueObject::class);
        $emailValueObject->method('__toString')->willReturn($email);

        $user = $this->createMock(User::class);
        $user->method('getEmail')->willReturn($emailValueObject);

        $consultant = $this->createMock(Consultant::class);
        $consultant->method('getId')->willReturn(123);

        $this->userRepository->method('findUserByEmail')->with($email)->willReturn($user);
        $this->consultantRepository->method('findConsultantByUser')->with($user)->willReturn($consultant);

        $this->projectRepository->method('checkIfConsultantHasProjects')->with($consultant)->willReturn(
            new JsonResponse([
                'error' => 'Cannot delete consultant because there are associated projects.',
                'projects' => [['id' => 1, 'name' => 'Project 1'], ['id' => 2, 'name' => 'Project 2']],
            ], 402)
        );

        $response = ($this->service)($data);

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(402, $response->getStatusCode());
        $this->assertEquals([
            'error' => 'Cannot delete consultant because there are associated projects.',
            'projects' => [['id' => 1, 'name' => 'Project 1'], ['id' => 2, 'name' => 'Project 2']]
        ], json_decode($response->getContent(), true));
    }
}
