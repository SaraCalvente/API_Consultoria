<?php

declare(strict_types=1);

namespace App\Tests\Unit\Ability;

use App\Consultant\Application\Ability\AbilityUpdateService;
use App\Consultant\Domain\Ability\Ability;
use App\Consultant\Domain\Ability\Level;
use App\Consultant\Domain\Model\AbilityRepositoryInterface;
use Codeception\Test\Unit;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\JsonResponse;

class AbilityUpdateServiceTest extends Unit
{
    private AbilityRepositoryInterface $abilityRepository;
    private AbilityUpdateService $service;

    /**
     * @throws Exception
     */
    protected function setUp(): void
    {
        $this->abilityRepository = $this->createMock(AbilityRepositoryInterface::class);
        $this->service = new AbilityUpdateService($this->abilityRepository);
    }

    /**
     * @throws Exception
     */
    public function testReturns403IfAbilityDoesNotExist(): void
    {
        $name = 'Symfony';
        $level = Level::EXPERT->value;
        $newName = 'Laravel';
        $newLevel = Level::MEDIUM->value;

        $this->abilityRepository
            ->expects($this->once())
            ->method('checkIfAbilityExists')
            ->with($name, $level)
            ->willReturn(false);

        $response = ($this->service)($name, $level, $newName, $newLevel);

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(403, $response->getStatusCode());

        $data = json_decode($response->getContent(), true);
        $this->assertEquals("An ability with the name $name and level $level does not exists.", $data['error']);
    }

    /**
     * @throws Exception
     */
    public function testReturnsUpdatedAbilityIfExists(): void
    {
        $name = 'Symfony';
        $level = Level::EXPERT->value;
        $newName = 'Laravel';
        $newLevel = Level::MEDIUM->value;

        $ability = $this->createConfiguredMock(Ability::class, [
            'getId' => 1,
            'getName' => $newName,
            'getLevel' => Level::MEDIUM,
        ]);

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
            ->method('updateAbility')
            ->with($ability, $newName, $newLevel);

        $response = ($this->service)($name, $level, $newName, $newLevel);

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(201, $response->getStatusCode());

        $data = json_decode($response->getContent(), true);
        $this->assertEquals('Ability successfully updated.', $data['message']);

        $this->assertEquals([
            'ability_id' => 1,
            'name' => $newName,
            'level' => Level::MEDIUM->value,
        ], $data['ability']);
    }
}
