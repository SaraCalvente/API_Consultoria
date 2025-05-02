<?php

declare(strict_types=1);

namespace App\Tests\Unit\Ability;

use App\Consultant\Application\Ability\AbilityGetByConsultantService;
use App\Consultant\Domain\Ability\Ability;
use App\Consultant\Domain\Ability\Level;
use App\Consultant\Domain\Consultant\Consultant;
use App\Consultant\Domain\Model\AbilityRepositoryInterface;
use App\Consultant\Domain\Model\ConsultantRepositoryInterface;
use App\User\Domain\User;
use App\User\Domain\ValueObject\EmailValueObject;
use Codeception\Test\Unit;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\JsonResponse;

class AbilityGetByConsultantServiceTest extends Unit
{
    private AbilityRepositoryInterface $abilityRepository;
    private ConsultantRepositoryInterface $consultantRepository;
    private AbilityGetByConsultantService $service;

    /**
     * @throws Exception
     */
    protected function setUp(): void
    {
        $this->abilityRepository = $this->createMock(AbilityRepositoryInterface::class);
        $this->consultantRepository = $this->createMock(ConsultantRepositoryInterface::class);

        $this->service = new AbilityGetByConsultantService(
            $this->abilityRepository,
            $this->consultantRepository
        );
    }

    /**
     * @throws Exception
     */
    public function testReturnsAbilitiesForConsultant(): void
    {
        $user = $this->createConfiguredMock(User::class, ['getEmail' => new EmailValueObject('consultant@example.com')]);

        $consultant = $this->createConfiguredMock(Consultant::class, ['getName' => 'Jane Doe', 'getId' => 42]);

        $ability = $this->createConfiguredMock(Ability::class, ['getName' => 'PHP', 'getLevel' => Level::HIGH]);

        $this->consultantRepository->expects($this->once())->method('checkIfConsultantExists')->with($user)
            ->willReturn(true);

        $this->consultantRepository->expects($this->once())->method('findConsultantByUser')->with($user)
            ->willReturn($consultant);

        $this->abilityRepository->expects($this->once())->method('findAbilitiesByConsultant')->with($consultant)
            ->willReturn([$ability]);

        $response = ($this->service)($user);

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(201, $response->getStatusCode());

        $data = json_decode($response->getContent(), true);

        $this->assertEquals('Ability successfully retrieved.', $data['message']);
        $this->assertEquals('Jane Doe', $data['consultant_name']);
        $this->assertEquals(42, $data['consultant_id']);
        $this->assertEquals([['name' => 'PHP', 'level' => 'Alto']], $data['abilities']);
    }

    /**
     * @throws Exception
     */
    public function testReturns404IfNotAConsultant(): void
    {

        $user = $this->createConfiguredMock(User::class, ['getEmail' => new EmailValueObject('noconsultant@example.com')]);

        $this->consultantRepository->expects($this->once())->method('checkIfConsultantExists')->with($user)
            ->willReturn(false);

        $response = ($this->service)($user);

        $this->assertEquals(404, $response->getStatusCode());

        $data = json_decode($response->getContent(), true);
        $this->assertEquals('User noconsultant@example.com is not a Consultant', $data['error']);
    }
}
