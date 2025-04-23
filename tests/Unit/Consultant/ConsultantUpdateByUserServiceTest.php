<?php

declare(strict_types=1);

namespace App\Tests\Unit\Consultant;

use App\Consultant\Application\Consultant\ConsultantUpdateByUserService;
use App\Consultant\Domain\Ability\Ability;
use App\Consultant\Domain\Consultant\Consultant;
use App\Consultant\Domain\Consultant\Profile;
use App\Consultant\Domain\Model\AbilityRepositoryInterface;
use App\Consultant\Domain\Model\ConsultantRepositoryInterface;
use App\User\Domain\User;
use App\User\Domain\ValueObject\EmailValueObject;
use Codeception\Test\Unit;
use Doctrine\Common\Collections\ArrayCollection;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\JsonResponse;

class ConsultantUpdateByUserServiceTest extends Unit
{
    private ConsultantRepositoryInterface $consultantRepository;
    private AbilityRepositoryInterface $abilityRepository;
    private ConsultantUpdateByUserService $service;

    /**
     * @throws Exception
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->consultantRepository = $this->createMock(ConsultantRepositoryInterface::class);
        $this->abilityRepository = $this->createMock(AbilityRepositoryInterface::class);

        $this->service = new ConsultantUpdateByUserService(
            $this->consultantRepository,
            $this->abilityRepository
        );
    }

    /**
     * @throws Exception
     */
    public function testUpdateConsultantProfile(): void
    {
        $user = $this->createMock(User::class);
        $profile = 'Desarrollador';

        $consultant = $this->createMock(Consultant::class);

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

        $consultant->method('getId')->willReturn(1);
        $consultant->method('getName')->willReturn('Updated Consultant');
        $consultant->method('getProfile')->willReturn(Profile::from($profile));
        $consultant->method('getAbilities')->willReturn(new ArrayCollection());
        $consultant->method('getUser')->willReturn($user);

        $user->method('getId')->willReturn(10);
        $user->method('getEmail')->willReturn(new EmailValueObject('user@example.com'));
        $user->method('getRoles')->willReturn(['ROLE_CONSULTANT']);

        $response = ($this->service)($user, $profile, null, null);

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(200, $response->getStatusCode());
    }

    /**
     * @throws Exception
     */
    public function testAddAndRemoveAbilities(): void
    {
        $user = $this->createMock(User::class);
        $addAbilities = [
            ['abilityName' => 'PHP', 'level' => 'JUNIOR']
        ];
        $removeAbilities = [
            ['abilityName' => 'Java', 'level' => 'SENIOR']
        ];

        $consultant = $this->createMock(Consultant::class);
        $abilityToAdd = $this->createMock(Ability::class);
        $abilityToRemove = $this->createMock(Ability::class);

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
        $user->method('getEmail')->willReturn(new EmailValueObject('user@example.com'));
        $user->method('getRoles')->willReturn(['ROLE_CONSULTANT']);

        $response = ($this->service)($user, null, $addAbilities, $removeAbilities);

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(200, $response->getStatusCode());
    }
}
