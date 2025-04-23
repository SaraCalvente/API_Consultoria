<?php

declare(strict_types=1);

namespace App\Tests\Unit\Ability;

use App\Consultant\Application\Ability\AbilityDeleteService;
use App\Consultant\Domain\Ability\Ability;
use App\Consultant\Domain\Model\AbilityRepositoryInterface;
use Codeception\Test\Unit;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\JsonResponse;

class AbilityDeleteServiceTest extends Unit
{
    private $abilityRepository;
    private AbilityDeleteService $service;

    /**
     * @throws Exception
     */
    protected function setUp(): void
    {
        $this->abilityRepository = $this->createMock(AbilityRepositoryInterface::class);
        $this->service = new AbilityDeleteService($this->abilityRepository);
    }

    /**
     * @throws Exception
     */
    public function testDeletesExistingAbility(): void
    {
        $name = 'Symfony';
        $level = 'Bajo';
        $ability = $this->createMock(Ability::class);

        $this->abilityRepository
            ->expects($this->once())
            ->method('checkIfAbilityExists')
            ->with($name, $level)
            ->willReturn(true);

        $this->abilityRepository
            ->expects($this->once())
            ->method('findAbilityByNameAndLevel')
            ->with($name, $level)
            ->willReturn($ability);

        $this->abilityRepository
            ->expects($this->once())
            ->method('deleteAbility')
            ->with($ability);

        $response = ($this->service)($name, $level);

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(201, $response->getStatusCode());

        $data = json_decode($response->getContent(), true);
        $this->assertEquals('Ability successfully deleted.', $data['message']);
    }

    public function testFailsWhenAbilityDoesNotExist(): void
    {
        $name = 'Nonexistent';
        $level = 'Alto';

        $this->abilityRepository
            ->expects($this->once())
            ->method('checkIfAbilityExists')
            ->with($name, $level)
            ->willReturn(false);

        $this->abilityRepository
            ->expects($this->never())
            ->method('findAbilityByNameAndLevel');

        $this->abilityRepository
            ->expects($this->never())
            ->method('deleteAbility');

        $response = ($this->service)($name, $level);

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(403, $response->getStatusCode());

        $data = json_decode($response->getContent(), true);
        $this->assertEquals('An ability with the name Nonexistent does not exists.', $data['error']);
    }
}
