<?php

declare(strict_types=1);

namespace App\Tests\Unit\Availability;

use App\Consultant\Application\Availability\AvailabilityCreateService;
use App\Consultant\Domain\Consultant\Consultant;
use App\Consultant\Domain\Model\AvailabilityRepositoryInterface;
use App\Consultant\Domain\Model\ConsultantRepositoryInterface;
use App\User\Domain\Model\UserRepositoryInterface;
use App\User\Domain\User;
use Codeception\Test\Unit;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\JsonResponse;

class AvailabilityCreateServiceTest extends TestCase
{
    private AvailabilityRepositoryInterface $availabilityRepository;
    private ConsultantRepositoryInterface $consultantRepository;
    private UserRepositoryInterface $userRepository;
    private AvailabilityCreateService $service;

    /**
     * @throws Exception
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->availabilityRepository = $this->createMock(AvailabilityRepositoryInterface::class);
        $this->consultantRepository = $this->createMock(ConsultantRepositoryInterface::class);
        $this->userRepository = $this->createMock(UserRepositoryInterface::class);

        $this->service = new AvailabilityCreateService(
            $this->availabilityRepository,
            $this->consultantRepository,
            $this->userRepository
        );
    }


    /**
     * @throws \DateMalformedStringException
     * @throws Exception
     */
    public function testReturns403IfAvailabilityAlreadyExists(): void
    {
        $email = 'test@example.com';
        $start = '2025-04-20 09:00:00';
        $end = '2025-04-21 09:00:00';

        $user = $this->createMock(User::class);
        $consultant = $this->createMock(Consultant::class);

        $this->userRepository->method('findUserByEmail')->willReturn($user);
        $this->consultantRepository->method('findConsultantByUser')->willReturn($consultant);
        $this->availabilityRepository->method('checkIfAvailabilityExists')->willReturn(true);

        $response = ($this->service)($email, $start, $end, true);

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(403, $response->getStatusCode());

        $data = json_decode($response->getContent(), true);
        $this->assertStringContainsString('already exists', $data['error']);
    }

    /**
     * @throws \DateMalformedStringException
     * @throws Exception
     */
    public function testReturns403IfDatesAreInvalid(): void
    {
        $email = 'test@example.com';
        $start = '2025-04-22 10:00:00';
        $end = '2025-04-21 09:00:00'; // start > end

        $user = $this->createMock(User::class);
        $consultant = $this->createMock(Consultant::class);

        $this->userRepository->method('findUserByEmail')->willReturn($user);
        $this->consultantRepository->method('findConsultantByUser')->willReturn($consultant);
        $this->availabilityRepository->method('checkIfAvailabilityExists')->willReturn(false);
        $this->availabilityRepository->method('checkDates')->willReturn(false);

        $response = ($this->service)($email, $start, $end, true);

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(403, $response->getStatusCode());

        $data = json_decode($response->getContent(), true);
        $this->assertStringContainsString('wrong format', $data['error']);
    }

    /**
     * @throws \DateMalformedStringException
     * @throws Exception
     */
    public function testReturns201IfAvailabilityIsCreated(): void
    {
        $email = 'test@example.com';
        $start = '2025-04-20 09:00:00';
        $end = '2025-04-21 09:00:00';

        $user = $this->createMock(User::class);

        $consultant = $this->createConfiguredMock(Consultant::class, [
            'getId' => 1,
        ]);

        $this->userRepository->method('findUserByEmail')->willReturn($user);
        $this->consultantRepository->method('findConsultantByUser')->willReturn($consultant);
        $this->availabilityRepository->method('checkIfAvailabilityExists')->willReturn(false);
        $this->availabilityRepository->method('checkDates')->willReturn(true);

        $this->availabilityRepository
            ->expects($this->once())
            ->method('addAvailability');

        $response = ($this->service)($email, $start, $end, true);

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(201, $response->getStatusCode());

        $data = json_decode($response->getContent(), true);
        $this->assertEquals('Consultant successfully registered', $data['message']);

        $this->assertArrayHasKey('ability', $data);
        $this->assertArrayHasKey('availability_id', $data['ability']);
        $this->assertArrayHasKey('available', $data['ability']);
        $this->assertArrayHasKey('start_date', $data['ability']);
        $this->assertArrayHasKey('end_date', $data['ability']);
        $this->assertArrayHasKey('consultant_id', $data['ability']);
    }
}
