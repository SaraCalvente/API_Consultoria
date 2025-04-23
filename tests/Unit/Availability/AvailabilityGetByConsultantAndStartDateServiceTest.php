<?php

declare(strict_types=1);

namespace App\Tests\Unit\Availability;

use App\Consultant\Application\Availability\AvailabilityGetByConsultantAndStartDateService;
use App\Consultant\Domain\Availability\Availability;
use App\Consultant\Domain\Consultant\Consultant;
use App\Consultant\Domain\Model\AvailabilityRepositoryInterface;
use App\Consultant\Domain\Model\ConsultantRepositoryInterface;
use App\User\Domain\Model\UserRepositoryInterface;
use App\User\Domain\User;
use Codeception\Test\Unit;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\JsonResponse;

class AvailabilityGetByConsultantAndStartDateServiceTest extends Unit
{
    private AvailabilityRepositoryInterface $availabilityRepository;
    private ConsultantRepositoryInterface $consultantRepository;
    private UserRepositoryInterface $userRepository;
    private AvailabilityGetByConsultantAndStartDateService $service;

    /**
     * @throws Exception
     */
    protected function setUp(): void
    {
        parent::setUp();
        $this->availabilityRepository = $this->createMock(AvailabilityRepositoryInterface::class);
        $this->consultantRepository = $this->createMock(ConsultantRepositoryInterface::class);
        $this->userRepository = $this->createMock(UserRepositoryInterface::class);

        $this->service = new AvailabilityGetByConsultantAndStartDateService(
            $this->availabilityRepository,
            $this->consultantRepository,
            $this->userRepository
        );
    }



    /**
     * @throws \DateMalformedStringException
     * @throws Exception
     */
    public function testReturnsAvailabilitySuccessfully(): void
    {
        $email = 'test@example.com';
        $startDate = '2025-04-20 09:00:00';

        $user = $this->createMock(User::class);
        $consultant = $this->createMock(Consultant::class);
        $availability = $this->createConfiguredMock(Availability::class, [
            'getId' => 1,
            'isAvailable' => true,
            'getStartDate' => new \DateTime($startDate),
            'getEndDate' => new \DateTime('2025-04-20 17:00:00'),
            'getConsultant' => $consultant,
        ]);
        $consultant->method('getId')->willReturn(123);

        $this->userRepository
            ->method('findUserByEmail')
            ->with($email)
            ->willReturn($user);

        $this->consultantRepository
            ->method('findConsultantByUser')
            ->with($user)
            ->willReturn($consultant);

        $this->availabilityRepository
            ->method('checkIfAvailabilityExists')
            ->with($consultant, $startDate)
            ->willReturn(true);

        $this->availabilityRepository
            ->method('findAvailabilityByStartDateAndConsultant')
            ->with($consultant, $startDate)
            ->willReturn($availability);

        $response = ($this->service)($email, $startDate);

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(201, $response->getStatusCode());

        $data = json_decode($response->getContent(), true);

        $this->assertEquals('Availability successfully updated.', $data['message']);
        $this->assertEquals([
            'availability_id' => 1,
            'available' => true,
            'start_date' => '2025-04-20 09:00:00',
            'end_date' => '2025-04-20 17:00:00',
            'consultant_id' => 123
        ], $data['availability']);
    }

    /**
     * @throws Exception
     */
    public function testReturnsErrorWhenAvailabilityDoesNotExist(): void
    {
        $email = 'test@example.com';
        $startDate = '2025-04-20 09:00:00';

        $user = $this->createMock(User::class);
        $consultant = $this->createMock(Consultant::class);

        $this->userRepository
            ->method('findUserByEmail')
            ->willReturn($user);

        $this->consultantRepository
            ->method('findConsultantByUser')
            ->willReturn($consultant);

        $this->availabilityRepository
            ->method('checkIfAvailabilityExists')
            ->with($consultant, $startDate)
            ->willReturn(false);

        $response = ($this->service)($email, $startDate);

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(403, $response->getStatusCode());

        $data = json_decode($response->getContent(), true);
        $this->assertStringContainsString('does not exist', $data['error']);
    }
}
