<?php

declare(strict_types=1);

namespace App\Tests\Unit\Consultant\Admin;

use App\Consultant\Application\Consultant\Admin\ConsultantUpdateByEmailService;
use App\Consultant\Domain\Ability\Ability;
use App\Consultant\Domain\Consultant\Consultant;
use App\Consultant\Domain\Consultant\Profile;
use App\Consultant\Domain\Model\AbilityRepositoryInterface;
use App\Consultant\Domain\Model\ConsultantRepositoryInterface;
use App\User\Domain\Model\UserRepositoryInterface;
use App\User\Domain\User;
use Codeception\Test\Unit;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\JsonResponse;
use Doctrine\Common\Collections\ArrayCollection;


class ConsultantUpdateByEmailServiceTest extends Unit
{
    private ConsultantRepositoryInterface $consultantRepository;
    private UserRepositoryInterface $userRepository;
    private AbilityRepositoryInterface $abilityRepository;
    private ConsultantUpdateByEmailService $service;

    /**
     * @throws Exception
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->consultantRepository = $this->createMock(ConsultantRepositoryInterface::class);
        $this->userRepository = $this->createMock(UserRepositoryInterface::class);
        $this->abilityRepository = $this->createMock(AbilityRepositoryInterface::class);

        $this->service = new ConsultantUpdateByEmailService(
            $this->consultantRepository,
            $this->userRepository,
            $this->abilityRepository
        );
    }

    /**
     * @throws Exception
     */
    public function testUpdateConsultantProfile(): void
    {
        $email = 'test@example.com';
        $profile = 'Desarrollador';

        $data = ['email' => $email, 'profile' => $profile, 'addAbilities' => null, 'removeAbilities' => null];

        $user = $this->createMock(User::class);
        $consultant = $this->createMock(Consultant::class);

        $this->userRepository
            ->method('findUserByEmail')
            ->with($email)
            ->willReturn($user);

        $this->consultantRepository
            ->method('findConsultantByUser')
            ->with($user)
            ->willReturn($consultant);

        $consultant
            ->expects($this->once())
            ->method('setProfile')
            ->with(Profile::from($profile));

        $this->consultantRepository
            ->expects($this->once())
            ->method('saveConsultant');

        $consultant
            ->method('getId')->willReturn(1);
        $consultant
            ->method('getName')->willReturn('Updated Consultant');
        $consultant
            ->method('getProfile')->willReturn(Profile::from($profile));
        $consultant
            ->method('getAbilities')->willReturn(new ArrayCollection());

        $user
            ->method('getId')->willReturn(10);
        $user
            ->method('getEmail')->willReturn(null);
        $user
            ->method('getRoles')->willReturn(['ROLE_CONSULTANT']);

        $consultant
            ->method('getUser')->willReturn($user);

        $response = ($this->service)($data);

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(200, $response->getStatusCode());
    }

    /**
     * @throws Exception
     */
    public function testAddAndRemoveAbilities(): void
    {
        $email = 'test@example.com';
        $addAbilities = [
            ['abilityName' => 'PHP', 'level' => 'JUNIOR']
        ];
        $removeAbilities = [
            ['abilityName' => 'Java', 'level' => 'SENIOR']
        ];

        $data = ['email' => $email, 'profile' => null, 'addAbilities' => $addAbilities, 'removeAbilities' => $removeAbilities];

        $user = $this->createMock(User::class);
        $consultant = $this->createMock(Consultant::class);
        $abilityToAdd = $this->createMock(Ability::class);
        $abilityToRemove = $this->createMock(Ability::class);

        $this->userRepository->method('findUserByEmail')->with($email)->willReturn($user);
        $this->consultantRepository->method('findConsultantByUser')->with($user)->willReturn($consultant);

        $this->abilityRepository->method('findAbilityByNameAndLevel')
            ->willReturnMap([
                ['PHP', 'JUNIOR', $abilityToAdd],
                ['Java', 'SENIOR', $abilityToRemove]
            ]);

        $consultant->expects($this->once())->method('addAbility')->with($abilityToAdd);
        $consultant->expects($this->once())->method('removeAbility')->with($abilityToRemove);

        $this->consultantRepository->expects($this->once())->method('saveConsultant');

        $consultant->method('getId')->willReturn(1);
        $consultant->method('getName')->willReturn('Test Consultant');
        $consultant->method('getProfile')->willReturn(Profile::DESARROLLADOR);
        $consultant->method('getAbilities')->willReturn(new ArrayCollection());
        $consultant->method('getUser')->willReturn($user);

        $user->method('getId')->willReturn(10);
        $user->method('getEmail')->willReturn(null);
        $user->method('getRoles')->willReturn(['ROLE_CONSULTANT']);

        $response = ($this->service)($data);

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(200, $response->getStatusCode());
    }
}
