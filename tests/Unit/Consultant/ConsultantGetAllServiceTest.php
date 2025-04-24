<?php

declare(strict_types=1);

namespace App\Tests\Unit\Consultant\Admin;

use App\Consultant\Application\Consultant\Admin\ConsultantGetAllService;
use App\Consultant\Domain\Consultant\Consultant;
use App\Consultant\Domain\Consultant\Profile;
use App\Consultant\Domain\Model\ConsultantRepositoryInterface;
use Codeception\Test\Unit;
use Doctrine\Common\Collections\ArrayCollection;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\JsonResponse;

class ConsultantGetAllServiceTest extends Unit
{
    private ConsultantRepositoryInterface $consultantRepository;
    private ConsultantGetAllService $service;

    /**
     * @throws Exception
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->consultantRepository = $this->createMock(ConsultantRepositoryInterface::class);
        $this->service = new ConsultantGetAllService($this->consultantRepository);
    }

    /**
     * @throws Exception
     */
    public function testReturnsAllConsultants(): void
    {
        $email1 = $this->createMock(\App\User\Domain\ValueObject\EmailValueObject::class);
        $email1->method('__toString')->willReturn('john@example.com');

        $email2 = $this->createMock(\App\User\Domain\ValueObject\EmailValueObject::class);
        $email2->method('__toString')->willReturn('jane@example.com');

// Mock del User
        $user1 = $this->createMock(\App\User\Domain\User::class);
        $user1->method('getId')->willReturn(1);
        $user1->method('getEmail')->willReturn($email1);
        $user1->method('getRoles')->willReturn(['ROLE_CONSULTANT']);

        $user2 = $this->createMock(\App\User\Domain\User::class);
        $user2->method('getId')->willReturn(2);
        $user2->method('getEmail')->willReturn($email2);
        $user2->method('getRoles')->willReturn(['ROLE_CONSULTANT']);

        // Mock de Consultant
        $consultant1 = $this->createMock(Consultant::class);
        $consultant1->method('getId')->willReturn(101);
        $consultant1->method('getName')->willReturn('John Doe');
        $consultant1->method('getProfile')->willReturn(Profile::DESARROLLADOR);
        $consultant1->method('getUser')->willReturn($user1);
        $consultant1->method('getAbilities')->willReturn(new ArrayCollection([]));

        $consultant2 = $this->createMock(Consultant::class);
        $consultant2->method('getId')->willReturn(102);
        $consultant2->method('getName')->willReturn('Jane Doe');
        $consultant2->method('getProfile')->willReturn(Profile::PROJECT_MANAGER);
        $consultant2->method('getUser')->willReturn($user2);
        $consultant2->method('getAbilities')->willReturn(new ArrayCollection([]));

        $this->consultantRepository
            ->method('findAllConsultants')
            ->willReturn([$consultant1, $consultant2]);

        $response = ($this->service)();

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(200, $response->getStatusCode());

        $data = json_decode($response->getContent(), true);

        $this->assertCount(2, $data);

        $this->assertEquals(1, $data[0]['user_id']);
        $this->assertEquals(101, $data[0]['consultant_id']);
        $this->assertEquals('John Doe', $data[0]['name']);
        $this->assertEquals('Desarrollador', $data[0]['profile']);
        $this->assertEquals(['ROLE_CONSULTANT'], $data[0]['roles']);

        $this->assertEquals(2, $data[1]['user_id']);
        $this->assertEquals(102, $data[1]['consultant_id']);
        $this->assertEquals('Jane Doe', $data[1]['name']);
        $this->assertEquals('Project Manager', $data[1]['profile']);
        $this->assertEquals(['ROLE_CONSULTANT'], $data[1]['roles']);
    }

    public function testReturnsEmptyListWhenNoConsultants(): void
    {
        $this->consultantRepository
            ->method('findAllConsultants')
            ->willReturn([]);

        $response = ($this->service)();

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals([], json_decode($response->getContent(), true));
    }
}
