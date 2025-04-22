<?php

declare(strict_types=1);

namespace App\Tests\Unit\Ability;

use App\Consultant\Application\Ability\AbilityGetAllService;
use App\Consultant\Domain\Ability\Ability;
use App\Consultant\Domain\Ability\Level;
use App\Consultant\Domain\Model\AbilityRepositoryInterface;
use Codeception\Test\Unit;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\JsonResponse;

class AbilityGetAllServiceTest extends TestCase
{
    private $abilityRepository;
    private AbilityGetAllService $service;

    /**
     * @throws Exception
     */
    protected function setUp(): void
    {
        $this->abilityRepository = $this->createMock(AbilityRepositoryInterface::class);
        $this->service = new AbilityGetAllService($this->abilityRepository);
    }

    /**
     * @throws Exception
     */
    public function testReturnsAllAbilities(): void
    {
        $ability1 = $this->createConfiguredMock(Ability::class, [
            'getId' => 1,
            'getName' => 'Symfony',
            'getLevel' => Level::MEDIUM
        ]);

        $ability2 = $this->createConfiguredMock(Ability::class, [
            'getId' => 2,
            'getName' => 'PHP',
            'getLevel' => Level::LOW
        ]);

        $this->abilityRepository
            ->expects($this->once())
            ->method('findAllAbilities')
            ->willReturn([$ability1, $ability2]);

        $response = ($this->service)();

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(200, $response->getStatusCode());

        $data = json_decode($response->getContent(), true);

        $this->assertCount(2, $data);
        $this->assertEquals([
            'ability_id' => 1,
            'name' => 'Symfony',
            'level' => 'Medio',
        ], $data[0]);

        $this->assertEquals([
            'ability_id' => 2,
            'name' => 'PHP',
            'level' => 'Bajo',
        ], $data[1]);
    }
}
