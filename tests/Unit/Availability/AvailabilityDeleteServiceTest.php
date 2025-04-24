<?php

declare(strict_types=1);

namespace App\Tests\Unit\Availability;

use App\Consultant\Application\Availability\AvailabilityDeleteService;
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

class AvailabilityDeleteServiceTest extends Unit
{
    private AvailabilityRepositoryInterface $availabilityRepository;
    private UserRepositoryInterface $userRepository;
    private ConsultantRepositoryInterface $consultantRepository;
    private AvailabilityDeleteService $service;

    /**
     * @throws Exception
     */

    protected function setUp(): void
    {
        parent::setUp();

        $this->availabilityRepository = $this->createMock(AvailabilityRepositoryInterface::class);
        $this->userRepository = $this->createMock(UserRepositoryInterface::class);
        $this->consultantRepository = $this->createMock(ConsultantRepositoryInterface::class);

        $this->service = new AvailabilityDeleteService(
            $this->availabilityRepository,
            $this->userRepository,
            $this->consultantRepository
        );
    }


    /**
     * @throws Exception
     */
    public function testReturns403IfAvailabilityDoesNotExist(): void
    {
        $email = 'test@example.com';
        $startDate = '2025-04-22 10:00:00';

        $data = ['email' => $email, 'startDate' => $startDate];

        $user = $this->createMock(User::class);
        $consultant = $this->createMock(Consultant::class);

        $this->userRepository->method('findUserByEmail')->willReturn($user);
        $this->consultantRepository->method('findConsultantByUser')->willReturn($consultant);
        $this->availabilityRepository->method('checkIfAvailabilityExists')->willReturn(false);

        $response = ($this->service)($data);

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(403, $response->getStatusCode());

        $data = json_decode($response->getContent(), true);
        $this->assertStringContainsString('does not exists', $data['error']);
    }

    /**
     * @throws Exception
     */
    public function testReturns201IfAvailabilityDeletedSuccessfully(): void
    {
        $email = 'test@example.com';
        $startDate = '2025-04-22 10:00:00';
        $data = ['email' => $email, 'startDate' => $startDate];

        $user = $this->createMock(User::class);
        $consultant = $this->createMock(Consultant::class);
        $availability = $this->createMock(Availability::class);

        $this->userRepository->method('findUserByEmail')->willReturn($user);
        $this->consultantRepository->method('findConsultantByUser')->willReturn($consultant);
        $this->availabilityRepository->method('checkIfAvailabilityExists')->willReturn(true);
        $this->availabilityRepository->method('findAvailabilityByStartDateAndConsultant')->willReturn($availability);

        $this->availabilityRepository->expects($this->once())
            ->method('deleteAvailability')
            ->with($availability);

        $response = ($this->service)($data);

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(201, $response->getStatusCode());

        $data = json_decode($response->getContent(), true);
        $this->assertEquals('Availability successfully deleted.', $data['message']);
    }
}
