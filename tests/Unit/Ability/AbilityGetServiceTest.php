<?php

declare(strict_types=1);

namespace App\Tests\Unit\Ability;

use App\Consultant\Application\Ability\AbilityGetService;
use App\Consultant\Domain\Ability\Ability;
use App\Consultant\Domain\Ability\AbilityDTO;
use App\Consultant\Domain\Ability\Level;
use App\Consultant\Domain\Model\AbilityRepositoryInterface;
use Codeception\Test\Unit;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\JsonResponse;

class AbilityGetServiceTest extends Unit
{
    private AbilityRepositoryInterface $abilityRepository;
    private AbilityGetService $service;

    /**
     * @throws Exception
     */
    protected function setUp(): void
    {
        $this->abilityRepository = $this->createMock(AbilityRepositoryInterface::class);
        $this->service = new AbilityGetService($this->abilityRepository);
    }

    /**
     *
     * @throws Exception
     */
    public function testReturns404IfAbilityDoesNotExist(): void
    {
        $name = 'Symfony';
        $level = Level::EXPERT->value;

        $this->abilityRepository
            ->expects($this->once())
            ->method('checkIfAbilityExists')
            ->with($name, $level)
            ->willReturn(false);

        $response = ($this->service)($name, $level);

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(403, $response->getStatusCode());

        $data = json_decode($response->getContent(), true);
        $this->assertEquals("An ability with the name $name and level $level does not exist.", $data['error']);
    }

    /**
     *
     * @throws Exception
     */
    public function testReturnsAbilityIfExists(): void
    {
        $name = 'Symfony';
        $level = Level::EXPERT->value;

        $ability = $this->createConfiguredMock(Ability::class, [
            'getId' => 1,
            'getName' => $name,
            'getLevel' => Level::EXPERT,
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

        $response = ($this->service)($name, $level);

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(201, $response->getStatusCode());

        $data = json_decode($response->getContent(), true);
        $this->assertEquals('Ability successfully updated.', $data['message']);
        $this->assertArrayHasKey('ability', $data);
    }
}
