<?php
declare(strict_types=1);

namespace App\Tests\Unit\Availability;

use App\Consultant\Application\Availability\AvailabilityGetByConsultantService;
use App\Consultant\Domain\Availability\Availability;
use App\Consultant\Domain\Availability\AvailabilityDTO;
use App\Consultant\Domain\Consultant\Consultant;
use App\Consultant\Domain\Model\AvailabilityRepositoryInterface;
use App\Consultant\Domain\Model\ConsultantRepositoryInterface;
use App\User\Domain\User;
use App\User\Domain\ValueObject\EmailValueObject;
use Codeception\Test\Unit;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\JsonResponse;

class AvailabilityGetByConsultantServiceTest extends Unit
{
    private AvailabilityRepositoryInterface $availabilityRepository;
    private ConsultantRepositoryInterface $consultantRepository;
    private AvailabilityGetByConsultantService $service;

    /**
     * @throws Exception
     */
    protected function setUp(): void
    {
        parent::setUp();
        $this->availabilityRepository = $this->createMock(AvailabilityRepositoryInterface::class);
        $this->consultantRepository = $this->createMock(ConsultantRepositoryInterface::class);

        $this->service = new AvailabilityGetByConsultantService(
            $this->availabilityRepository,
            $this->consultantRepository
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
        $user->method('getEmail')->willReturn($emailValueObject);

        $consultant = $this->createMock(Consultant::class);
        $consultant->method('getId')->willReturn(123);
        $consultant->method('getName')->willReturn('John Doe');

        $availability1 = $this->createMock(Availability::class);
        $availability2 = $this->createMock(Availability::class);

        $availability1->method('getStartDate')->willReturn(new \DateTime('2025-04-20 09:00:00'));
        $availability1->method('getEndDate')->willReturn(new \DateTime('2025-04-20 17:00:00'));
        $availability1->method('getConsultant')->willReturn($consultant);

        $availability2->method('getStartDate')->willReturn(new \DateTime('2025-04-21 10:00:00'));
        $availability2->method('getEndDate')->willReturn(new \DateTime('2025-04-21 18:00:00'));
        $availability2->method('getConsultant')->willReturn($consultant);

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

        $response = ($this->service)($user);

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(201, $response->getStatusCode());

        $data = json_decode($response->getContent(), true);
        $this->assertEquals('Ability successfully retrieved.', $data['message']);
        $this->assertEquals('John Doe', $data['consultant_name']);
        $this->assertEquals(123, $data['consultant_id']);
        $this->assertCount(2, $data['availabilities']);
    }
}
