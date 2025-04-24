<?php

declare(strict_types=1);

namespace App\Tests\Unit\Availability;

use App\Consultant\Application\Availability\AvailabilityGetByConsultantEmailService;
use App\Consultant\Domain\Availability\Availability;
use App\Consultant\Domain\Availability\AvailabilityDTO;
use App\Consultant\Domain\Consultant\Consultant;
use App\Consultant\Domain\Model\AvailabilityRepositoryInterface;
use App\Consultant\Domain\Model\ConsultantRepositoryInterface;
use App\User\Domain\Model\UserRepositoryInterface;
use App\User\Domain\User;
use App\User\Domain\ValueObject\EmailValueObject;
use Codeception\Test\Unit;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\JsonResponse;

class AvailabilityGetByConsultantEmailServiceTest extends Unit
{
    private AvailabilityRepositoryInterface $availabilityRepository;
    private ConsultantRepositoryInterface $consultantRepository;
    private UserRepositoryInterface $userRepository;
    private AvailabilityGetByConsultantEmailService $service;

    /**
     * @throws Exception
     */
    protected function setUp(): void
    {
        parent::setUp();
        $this->availabilityRepository = $this->createMock(AvailabilityRepositoryInterface::class);
        $this->consultantRepository = $this->createMock(ConsultantRepositoryInterface::class);
        $this->userRepository = $this->createMock(UserRepositoryInterface::class);

        $this->service = new AvailabilityGetByConsultantEmailService(
            $this->availabilityRepository,
            $this->consultantRepository,
            $this->userRepository
        );
    }

    /**
     * @throws Exception
     */
    public function testReturnsAvailabilitiesSuccessfully(): void
    {
        $email = 'test@example.com';

        $emailValueObject = $this->createMock(EmailValueObject::class);
        $emailValueObject->method('__toString')->willReturn($email);

        $user = $this->createMock(User::class);
        $consultant = $this->createMock(Consultant::class);

        $availability1 = $this->createMock(Availability::class);
        $availability2 = $this->createMock(Availability::class);

        $availability1->method('getConsultant')->willReturn($consultant);
        $availability2->method('getConsultant')->willReturn($consultant);

        $availability1->method('getStartDate')->willReturn(new \DateTime('2025-04-20 09:00:00'));
        $availability1->method('getEndDate')->willReturn(new \DateTime('2025-04-20 17:00:00'));

        $availability2->method('getStartDate')->willReturn(new \DateTime('2025-04-21 10:00:00'));
        $availability2->method('getEndDate')->willReturn(new \DateTime('2025-04-21 18:00:00'));

        $consultant->method('getId')->willReturn(123);
        $consultant->method('getName')->willReturn('John Doe');

        $user->method('getEmail')->willReturn($emailValueObject);

        $this->userRepository
            ->method('findUserByEmail')
            ->with($email)
            ->willReturn($user);

        $this->consultantRepository
            ->method('checkIfConsultantExists')
            ->with($user)
            ->willReturn(true);

        $this->consultantRepository
            ->method('findConsultantByUser')
            ->with($user)
            ->willReturn($consultant);

        $this->availabilityRepository
            ->method('findAvailabilityByConsultant')
            ->with($consultant)
            ->willReturn([$availability1, $availability2]);

        $availabilityDTO1 = $this->createMock(AvailabilityDTO::class);
        $availabilityDTO2 = $this->createMock(AvailabilityDTO::class);
        $this->availabilityRepository
            ->method('findAvailabilityByConsultant')
            ->willReturn([$availabilityDTO1, $availabilityDTO2]);

        $data = ['email' => $email];
        $response = ($this->service)($data);

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(201, $response->getStatusCode());

        $data = json_decode($response->getContent(), true);
        $this->assertEquals('Ability successfully retrieved.', $data['message']);
        $this->assertEquals('John Doe', $data['consultant_name']);
        $this->assertEquals(123, $data['consultant_id']);
        $this->assertCount(2, $data['availabilities']);
    }

    /**
     * @throws Exception
     */
    public function testReturnsErrorWhenConsultantDoesNotExist(): void
    {
        $email = 'test@example.com';
        $data = ['email' => $email];

        $user = $this->createMock(User::class);

        $this->userRepository
            ->method('findUserByEmail')
            ->willReturn($user);

        $this->consultantRepository
            ->method('checkIfConsultantExists')
            ->with($user)
            ->willReturn(false);

        $response = ($this->service)($data);

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(404, $response->getStatusCode());

        $data = json_decode($response->getContent(), true);
        $this->assertStringContainsString('is not a Consultant', $data['error']);
    }
}
