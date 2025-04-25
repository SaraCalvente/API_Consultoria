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
    private array $data;

    /**
     * @throws Exception
     */
    protected function setUp(): void
    {
        $this->abilityRepository = $this->createMock(AbilityRepositoryInterface::class);
        $this->service = new AbilityGetService($this->abilityRepository);
        $this->data = ['name' => 'Symfony', 'level' => Level::EXPERT->value];
    }

    /**
     *
     * @throws Exception
     */
    public function testReturns404IfAbilityDoesNotExist(): void
    {
        $this->abilityRepository->expects($this->once())->method('checkIfAbilityExists')->with($this->data['name'], $this->data['level'])
            ->willReturn(false);

        $response = ($this->service)($this->data);

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(403, $response->getStatusCode());

        $data = json_decode($response->getContent(), true);
        $this->assertEquals(
            "An ability with the name " . $this->data['name'] . " and level " . $this->data['level'] . " does not exist.", $data['error']
        );
    }

    /**
     *
     * @throws Exception
     */
    public function testReturnsAbilityIfExists(): void
    {
        $ability = $this->createConfiguredMock(Ability::class,
            ['getId' => 1, 'getName' => $this->data['name'], 'getLevel' => Level::EXPERT]);

        $this->abilityRepository->expects($this->once())->method('checkIfAbilityExists')->with($this->data['name'], $this->data['level'])
            ->willReturn(true);

        $this->abilityRepository->expects($this->once())->method('findAbilityByNameAndLevel')->with($this->data['name'], $this->data['level'])
            ->willReturn($ability);

        $response = ($this->service)($this->data);

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(201, $response->getStatusCode());

        $data = json_decode($response->getContent(), true);
        $this->assertEquals('Ability successfully updated.', $data['message']);
        $this->assertArrayHasKey('ability', $data);
    }
}
