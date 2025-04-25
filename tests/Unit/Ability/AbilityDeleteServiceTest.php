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
    private array $date;

    /**
     * @throws Exception
     */
    protected function setUp(): void
    {
        $this->abilityRepository = $this->createMock(AbilityRepositoryInterface::class);
        $this->service = new AbilityDeleteService($this->abilityRepository);
        $this->data = ['name' => 'Symfony', 'level' => 'Bajo'];

    }

    /**
     * @throws Exception
     */
    public function testDeletesExistingAbility(): void
    {
        $ability = $this->createMock(Ability::class);

        $this->abilityRepository->expects($this->once())->method('checkIfAbilityExists')->with($this->data['name'], $this->data['level'])
            ->willReturn(true);

        $this->abilityRepository->expects($this->once())->method('findAbilityByNameAndLevel')->with($this->data['name'], $this->data['level'])
            ->willReturn($ability);

        $this->abilityRepository->expects($this->once())->method('deleteAbility')->with($ability);

        $response = ($this->service)($this->data);

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(201, $response->getStatusCode());

        $data = json_decode($response->getContent(), true);
        $this->assertEquals('Ability successfully deleted.', $data['message']);
    }

    public function testFailsWhenAbilityDoesNotExist(): void
    {
        $this->abilityRepository->expects($this->once())->method('checkIfAbilityExists')->with($this->data['name'], $this->data['level'])
            ->willReturn(false);

        $this->abilityRepository->expects($this->never())->method('findAbilityByNameAndLevel');

        $this->abilityRepository->expects($this->never())->method('deleteAbility');

        $response = ($this->service)($this->data);

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(403, $response->getStatusCode());

        $data = json_decode($response->getContent(), true);
        $this->assertEquals('An ability with the name Symfony does not exists.', $data['error']);
    }
}
