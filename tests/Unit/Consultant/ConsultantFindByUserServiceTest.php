<?php

declare(strict_types=1);

namespace App\Tests\Unit\Consultant;

use App\Consultant\Application\Consultant\ConsultantFindByUserService;
use App\Consultant\Domain\Consultant\Consultant;
use App\Consultant\Domain\Consultant\ConsultantDTO;
use App\Consultant\Domain\Consultant\Profile;
use App\Consultant\Domain\Model\ConsultantRepositoryInterface;
use App\User\Domain\User;
use App\User\Domain\ValueObject\EmailValueObject;
use Doctrine\Common\Collections\ArrayCollection;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\JsonResponse;

class ConsultantFindByUserServiceTest extends TestCase
{
    private ConsultantRepositoryInterface $consultantRepository;
    private ConsultantFindByUserService $service;

    /**
     * @throws Exception
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->consultantRepository = $this->createMock(ConsultantRepositoryInterface::class);
        $this->service = new ConsultantFindByUserService($this->consultantRepository);
    }

    /**
     * @throws Exception
     */
    public function testFindsConsultantByUser(): void
    {
        $user = $this->createMock(User::class);
        $consultant = $this->createMock(Consultant::class);

        $consultant->method('getUser')->willReturn($user);

        $emailValueObject = $this->createMock(EmailValueObject::class);

        $emailValueObject->method('__toString')->willReturn('john.doe@example.com');

        $user->method('getEmail')->willReturn($emailValueObject);

        $user->method('getId')->willReturn(1);
        $user->method('getRoles')->willReturn(['ROLE_CONSULTANT']);

        $consultant->method('getId')->willReturn(2);
        $consultant->method('getName')->willReturn('John Doe');
        $consultant->method('getProfile')->willReturn(Profile::PROJECT_MANAGER);

        $abilitiesCollection = new ArrayCollection([
        ]);
        $consultant->method('getAbilities')->willReturn($abilitiesCollection);

        $this->consultantRepository
            ->method('findConsultantByUser')
            ->with($user)
            ->willReturn($consultant);

        $response = ($this->service)($user);

        $this->assertInstanceOf(JsonResponse::class, $response);

        $data = json_decode($response->getContent(), true);

        $this->assertEquals(2, $data['consultant_id']);
        $this->assertEquals('John Doe', $data['name']);
        $this->assertEquals('Project Manager', $data['profile']);
        $this->assertEquals(1, $data['user_id']);
    }


}
