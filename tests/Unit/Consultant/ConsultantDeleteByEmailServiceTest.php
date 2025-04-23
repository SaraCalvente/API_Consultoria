<?php
declare(strict_types=1);

namespace App\Tests\Unit\Consultant\Admin;

use App\Consultant\Application\Consultant\Admin\ConsultantDeleteByEmailService;
use App\Consultant\Domain\Consultant\Consultant;
use App\Consultant\Domain\Model\ConsultantRepositoryInterface;
use App\Project\Domain\Model\ProjectRepositoryInterface;
use App\User\Domain\Model\UserRepositoryInterface;
use App\User\Domain\User;
use App\User\Domain\ValueObject\EmailValueObject;
use Codeception\Test\Unit;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\JsonResponse;

class ConsultantDeleteByEmailServiceTest extends Unit
{
    private ConsultantRepositoryInterface $consultantRepository;
    private ProjectRepositoryInterface $projectRepository;
    private UserRepositoryInterface $userRepository;
    private ConsultantDeleteByEmailService $service;

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

    public function testDeleteConsultantSuccessfully(): void
    {
        $email = 'consultant@example.com';

        // Mock de EmailValueObject
        $emailValueObject = $this->createMock(EmailValueObject::class);
        $emailValueObject->method('__toString')->willReturn($email);

        // Mock de User
        $user = $this->createMock(User::class);
        $user->method('getEmail')->willReturn($emailValueObject);

        // Mock de Consultant
        $consultant = $this->createMock(Consultant::class);
        $consultant->method('getId')->willReturn(123);

        // Configurar mocks para el repository
        $this->userRepository->method('findUserByEmail')->with($email)->willReturn($user);
        $this->consultantRepository->method('findConsultantByUser')->with($user)->willReturn($consultant);

        // Simular que no tiene proyectos (devolver null)
        $this->projectRepository->method('checkIfConsultantHasProjects')->with($consultant)->willReturn(null);

        // Simular la eliminación del consultor
        $this->consultantRepository->method('deleteConsultant')->with($consultant)->willReturn(new JsonResponse(['message' => 'Consultant deleted successfully'], 200));

        // Ejecutar el servicio
        $response = ($this->service)($email);

        // Verificar la respuesta
        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals(['message' => 'Consultant deleted successfully'], json_decode($response->getContent(), true));
    }

    public function testCannotDeleteConsultantWithProjects(): void
    {
        $email = 'consultant@example.com';

        // Mock de EmailValueObject
        $emailValueObject = $this->createMock(EmailValueObject::class);
        $emailValueObject->method('__toString')->willReturn($email);

        // Mock de User
        $user = $this->createMock(User::class);
        $user->method('getEmail')->willReturn($emailValueObject);

        // Mock de Consultant
        $consultant = $this->createMock(Consultant::class);
        $consultant->method('getId')->willReturn(123);

        // Configurar mocks para el repository
        $this->userRepository->method('findUserByEmail')->with($email)->willReturn($user);
        $this->consultantRepository->method('findConsultantByUser')->with($user)->willReturn($consultant);

        // Simular que tiene proyectos asignados (devolver JsonResponse con los detalles)
        $this->projectRepository->method('checkIfConsultantHasProjects')->with($consultant)->willReturn(
            new JsonResponse([
                'error' => 'Cannot delete consultant because there are associated projects.',
                'projects' => [['id' => 1, 'name' => 'Project 1'], ['id' => 2, 'name' => 'Project 2']],
            ], 402)
        );

        // Ejecutar el servicio
        $response = ($this->service)($email);

        // Verificar la respuesta
        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(402, $response->getStatusCode());
        $this->assertEquals([
            'error' => 'Cannot delete consultant because there are associated projects.',
            'projects' => [['id' => 1, 'name' => 'Project 1'], ['id' => 2, 'name' => 'Project 2']]
        ], json_decode($response->getContent(), true));
    }
}
