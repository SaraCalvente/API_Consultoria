<?php

declare(strict_types=1);

namespace App\Tests\Unit\Ability;

use App\Consultant\Application\Ability\AbilityGetByConsultantEmailService;
use App\Consultant\Domain\Ability\Ability;
use App\Consultant\Domain\Ability\Level;
use App\Consultant\Domain\Consultant\Consultant;
use App\Consultant\Domain\Model\AbilityRepositoryInterface;
use App\Consultant\Domain\Model\ConsultantRepositoryInterface;
use App\User\Domain\Model\UserRepositoryInterface;
use App\User\Domain\User;
use App\User\Domain\ValueObject\EmailValueObject;
use Codeception\Test\Unit;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\JsonResponse;

class AbilityGetByConsultantEmailServiceTest extends Unit
{
    private AbilityRepositoryInterface $abilityRepository;
    private ConsultantRepositoryInterface $consultantRepository;
    private UserRepositoryInterface $userRepository;
    private AbilityGetByConsultantEmailService $service;

    /**
     * @throws Exception
     */
    protected function setUp(): void
    {
        $this->abilityRepository = $this->createMock(AbilityRepositoryInterface::class);
        $this->consultantRepository = $this->createMock(ConsultantRepositoryInterface::class);
        $this->userRepository = $this->createMock(UserRepositoryInterface::class);

        $this->service = new AbilityGetByConsultantEmailService(
            $this->abilityRepository,
            $this->consultantRepository,
            $this->userRepository
        );
    }

    /**
     * @throws Exception
     */
    public function testReturnsAbilitiesByConsultantEmail(): void
    {
        $email = 'consultant@example.com';
        $data = ['email' => $email];

        $emailVO = $this->createConfiguredMock(EmailValueObject::class, [
            '__toString' => $email,
        ]);

        $user = $this->createConfiguredMock(User::class, [
            'getEmail' => $emailVO,
        ]);
        $consultant = $this->createConfiguredMock(Consultant::class, [
            'getName' => 'Jane Doe',
            'getId' => 123,
        ]);

        $ability = $this->createConfiguredMock(Ability::class, [
            'getName' => 'PHP',
            'getLevel' => Level::EXPERT,
        ]);

        $this->userRepository
            ->expects($this->once())
            ->method('findUserByEmail')
            ->with($email)
            ->willReturn($user);

        $this->consultantRepository
            ->expects($this->once())
            ->method('checkIfConsultantExists')
            ->with($user)
            ->willReturn(true);

        $this->consultantRepository
            ->expects($this->once())
            ->method('findConsultantByUser')
            ->with($user)
            ->willReturn($consultant);

        $this->abilityRepository
            ->expects($this->once())
            ->method('findAbilitiesByConsultant')
            ->with($consultant)
            ->willReturn([$ability]);

        $response = ($this->service)($data);

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(201, $response->getStatusCode());

        $data = json_decode($response->getContent(), true);

        $this->assertEquals('Ability successfully retrieved.', $data['message']);
        $this->assertEquals('Jane Doe', $data['consultant_name']);
        $this->assertEquals(123, $data['consultant_id']);
        $this->assertCount(1, $data['abilities']);
        $this->assertEquals(['name' => 'PHP', 'level' => 'Experto'], $data['abilities'][0]);
    }

    /**
     * @throws Exception
     */
    public function testReturns404IfNotConsultant(): void
    {
        $email = 'notconsultant@example.com';
        $data = ['email' => $email];
        $emailVO = $this->createConfiguredMock(EmailValueObject::class, [
            '__toString' => $email,
        ]);

        $user = $this->createConfiguredMock(User::class, [
            'getEmail' => $emailVO,
        ]);

        $this->userRepository
            ->expects($this->once())
            ->method('findUserByEmail')
            ->with($email)
            ->willReturn($user);

        $this->consultantRepository
            ->expects($this->once())
            ->method('checkIfConsultantExists')
            ->with($user)
            ->willReturn(false);

        $response = ($this->service)($data);

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(404, $response->getStatusCode());

        $data = json_decode($response->getContent(), true);
        $this->assertEquals("User $email is not a Consultant", $data['error']);
    }
}
