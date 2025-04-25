<?php
declare(strict_types=1);

namespace App\Tests\Unit\Availability;

use App\Consultant\Application\Availability\AvailabilityUpdateService;
use App\Consultant\Domain\Consultant\Consultant;
use App\Consultant\Domain\Model\AvailabilityRepositoryInterface;
use App\Consultant\Domain\Model\ConsultantRepositoryInterface;
use App\User\Domain\Model\UserRepositoryInterface;
use App\User\Domain\User;
use App\User\Domain\ValueObject\EmailValueObject;
use App\Consultant\Domain\Availability\Availability;
use Codeception\Test\Unit;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\JsonResponse;

class AvailabilityUpdateServiceTest extends Unit
{
    private AvailabilityRepositoryInterface $availabilityRepository;
    private ConsultantRepositoryInterface $consultantRepository;
    private UserRepositoryInterface $userRepository;
    private AvailabilityUpdateService $service;

    /**
     * @throws Exception
     */
    protected function setUp(): void
    {
        parent::setUp();
        $this->availabilityRepository = $this->createMock(AvailabilityRepositoryInterface::class);
        $this->consultantRepository = $this->createMock(ConsultantRepositoryInterface::class);
        $this->userRepository = $this->createMock(UserRepositoryInterface::class);

        $this->service = new AvailabilityUpdateService(
            $this->availabilityRepository,
            $this->userRepository,
            $this->consultantRepository
        );
    }

    /**
     * @throws \DateMalformedStringException
     * @throws Exception
     */
    public function testUpdateAvailabilitySuccessfully(): void
    {
        $email = 'test@example.com';
        $startDate = '2025-04-20 09:00:00';
        $endDate = '2025-04-20 17:00:00';
        $available = true;

        $data = ['email' => $email, 'startDate' => $startDate, 'endDate' => $endDate, 'available' => $available];

        $emailValueObject = $this->createMock(EmailValueObject::class);
        $emailValueObject->method('__toString')->willReturn($email);

        $user = $this->createMock(User::class);
        $user->method('getEmail')->willReturn($emailValueObject);

        $consultant = $this->createMock(Consultant::class);
        $consultant->method('getId')->willReturn(123);

        $availability = $this->createMock(Availability::class);
        $availability->method('getStartDate')->willReturn(new \DateTime($startDate));

        $this->userRepository->method('findUserByEmail')->with($email)->willReturn($user);
        $this->consultantRepository->method('findConsultantByUser')->with($user)->willReturn($consultant);

        $this->availabilityRepository->method('checkIfAvailabilityExists')->with($consultant, $startDate)->willReturn(true);

        $this->availabilityRepository->method('findAvailabilityByStartDateAndConsultant')->with($consultant, $startDate)->willReturn($availability);

        $this->availabilityRepository->method('updateAvailability')->willReturn(new JsonResponse(['message' => 'Availability updated successfully'], 200));

        $response = ($this->service)($data);

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals(['message' => 'Availability updated successfully'], json_decode($response->getContent(), true));
    }

    /**
     * @throws \DateMalformedStringException
     * @throws Exception
     */
    public function testUpdateAvailabilityEndDateSuccessfully(): void
    {
        $email = 'test@example.com';
        $startDate = '2025-04-20 09:00:00';
        $endDate = '2025-04-20 17:00:00';

        $data = ['email' => $email, 'startDate' => $startDate, 'endDate' => $endDate, 'available' => null];

        $emailValueObject = $this->createMock(EmailValueObject::class);
        $emailValueObject->method('__toString')->willReturn($email);

        $user = $this->createMock(User::class);
        $user->method('getEmail')->willReturn($emailValueObject);

        $consultant = $this->createMock(Consultant::class);
        $consultant->method('getId')->willReturn(123);

        $availability = $this->createMock(Availability::class);
        $availability->method('getStartDate')->willReturn(new \DateTime($startDate));

        $this->userRepository->method('findUserByEmail')->with($email)->willReturn($user);
        $this->consultantRepository->method('findConsultantByUser')->with($user)->willReturn($consultant);

        $this->availabilityRepository->method('checkIfAvailabilityExists')->with($consultant, $startDate)->willReturn(true);

        $this->availabilityRepository->method('findAvailabilityByStartDateAndConsultant')->with($consultant, $startDate)->willReturn($availability);

        $this->availabilityRepository->method('updateAvailability')->willReturn(new JsonResponse(['message' => 'Availability updated successfully'], 200));

        $response = ($this->service)($data);

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals(['message' => 'Availability updated successfully'], json_decode($response->getContent(), true));
    }

    /**
     * @throws \DateMalformedStringException
     * @throws Exception
     */
    public function testUpdateAvailabilityAvailableSuccessfully(): void
    {
        $email = 'test@example.com';
        $startDate = '2025-04-20 09:00:00';
        $available = true;

        $data = ['email' => $email, 'startDate' => $startDate, 'endDate' => null, 'available' => $available];

        $emailValueObject = $this->createMock(EmailValueObject::class);
        $emailValueObject->method('__toString')->willReturn($email);

        $user = $this->createMock(User::class);
        $user->method('getEmail')->willReturn($emailValueObject);

        $consultant = $this->createMock(Consultant::class);
        $consultant->method('getId')->willReturn(123);

        $availability = $this->createMock(Availability::class);
        $availability->method('getStartDate')->willReturn(new \DateTime($startDate));

        $this->userRepository->method('findUserByEmail')->with($email)->willReturn($user);
        $this->consultantRepository->method('findConsultantByUser')->with($user)->willReturn($consultant);

        $this->availabilityRepository->method('checkIfAvailabilityExists')->with($consultant, $startDate)->willReturn(true);

        $this->availabilityRepository->method('findAvailabilityByStartDateAndConsultant')->with($consultant, $startDate)->willReturn($availability);

        $this->availabilityRepository->method('updateAvailability')->willReturn(new JsonResponse(['message' => 'Availability updated successfully'], 200));

        $response = ($this->service)($data);

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals(['message' => 'Availability updated successfully'], json_decode($response->getContent(), true));
    }

    /**
     * @throws Exception
     */
    public function testReturnsErrorWhenAvailabilityDoesNotExist(): void
    {
        $email = 'test@example.com';
        $startDate = '2025-04-20 09:00:00';
        $endDate = '2025-04-20 17:00:00';
        $available = true;

        $data = ['email' => $email, 'startDate' => $startDate, 'endDate' => $endDate, 'available' => $available];

        $emailValueObject = $this->createMock(EmailValueObject::class);
        $emailValueObject->method('__toString')->willReturn($email);

        $user = $this->createMock(User::class);
        $user->method('getEmail')->willReturn($emailValueObject);

        $consultant = $this->createMock(Consultant::class);
        $consultant->method('getId')->willReturn(123);

        $this->userRepository->method('findUserByEmail')->with($email)->willReturn($user);
        $this->consultantRepository->method('findConsultantByUser')->with($user)->willReturn($consultant);

        $this->availabilityRepository->method('checkIfAvailabilityExists')->with($consultant, $startDate)->willReturn(false);

        $response = ($this->service)($data);

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(403, $response->getStatusCode());
        $this->assertEquals(['error' => 'An availability for test@example.com with start date 2025-04-20 09:00:00 does not exists.'], json_decode($response->getContent(), true));
    }
}
