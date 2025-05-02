<?php

declare(strict_types=1);

namespace App\Tests\Unit\Ability;

use App\Consultant\Application\Ability\AbilityCreateService;
use App\Consultant\Domain\Ability\Ability;
use App\Consultant\Domain\Model\AbilityRepositoryInterface;
use Codeception\Test\Unit;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\JsonResponse;

class AbilityCreateServiceTest extends Unit
{
    private $abilityRepository;
    private AbilityCreateService $service;
    private array $data;

    /**
     * @throws Exception
     */
    protected function setUp(): void
    {
        $this->abilityRepository = $this->createMock(AbilityRepositoryInterface::class);
        $this->service = new AbilityCreateService($this->abilityRepository);
        $this->data = ['name' => 'Symfony', 'level' => 'Experto'];
    }

    public function testCreatesAbilitySuccessfully(): void
    {
        $this->abilityRepository->expects($this->once())->method('checkIfAbilityExists')->with($this->data['name'], $this->data['level'])
            ->willReturn(false);

        $this->abilityRepository->expects($this->once())->method('addAbility')->with($this->isInstanceOf(Ability::class));

        $response = ($this->service)($this->data);

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(201, $response->getStatusCode());

        $data = json_decode($response->getContent(), true);
        $this->assertEquals('Consultant successfully registered', $data['message']);
        $this->assertArrayHasKey('ability', $data);
    }

    public function testReturnsErrorIfAbilityExists(): void
    {
        $this->abilityRepository->expects($this->once())->method('checkIfAbilityExists')->with($this->data['name'], $this->data['level'])
            ->willReturn(true);

        $this->abilityRepository->expects($this->never())->method('addAbility');

        $response = ($this->service)($this->data);

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(403, $response->getStatusCode());

        $data = json_decode($response->getContent(), true);
        $this->assertEquals('An ability with the name Symfony already exists.', $data['error']);
    }
}
