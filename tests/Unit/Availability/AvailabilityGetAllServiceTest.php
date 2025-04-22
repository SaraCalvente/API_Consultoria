<?php

declare(strict_types=1);

namespace App\Tests\Unit\Availability;

use App\Consultant\Application\Availability\AvailabilityGetAllService;
use App\Consultant\Domain\Availability\Availability;
use App\Consultant\Domain\Consultant\Consultant;
use App\Consultant\Domain\Model\AvailabilityRepositoryInterface;
use Codeception\Test\Unit;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\JsonResponse;

class AvailabilityGetAllServiceTest extends TestCase
{
    private AvailabilityRepositoryInterface $availabilityRepository;
    private AvailabilityGetAllService $service;

    /**
     * @throws Exception
     */
    protected function setUp(): void
    {
        parent::setUp();
        $this->availabilityRepository = $this->createMock(AvailabilityRepositoryInterface::class);
        $this->service = new AvailabilityGetAllService($this->availabilityRepository);
    }


    /**
     * @throws Exception
     */
    public function testReturnsAllAvailabilities(): void
    {
        // Consultant mocks
        $consultant1 = $this->createConfiguredMock(Consultant::class, [
            'getId' => 123
        ]);
        $consultant2 = $this->createConfiguredMock(Consultant::class, [
            'getId' => 456
        ]);

        // Availability mocks
        $availability1 = $this->createConfiguredMock(Availability::class, [
            'getId' => 1,
            'isAvailable' => true,
            'getStartDate' => new \DateTime('2025-04-20 09:00:00'),
            'getEndDate' => new \DateTime('2025-04-20 17:00:00'),
            'getConsultant' => $consultant1
        ]);

        $availability2 = $this->createConfiguredMock(Availability::class, [
            'getId' => 2,
            'isAvailable' => false,
            'getStartDate' => new \DateTime('2025-04-21 10:00:00'),
            'getEndDate' => new \DateTime('2025-04-21 18:00:00'),
            'getConsultant' => $consultant2
        ]);

        // Set up repository mock
        $this->availabilityRepository
            ->method('findAllAvailabilities')
            ->willReturn([$availability1, $availability2]);

        // Call the service
        $response = ($this->service)();

        // Assert response
        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(200, $response->getStatusCode());

        $data = json_decode($response->getContent(), true);

        $this->assertCount(2, $data);

        $this->assertEquals([
            'availability_id' => 1,
            'available' => true,
            'start_date' => '2025-04-20 09:00:00',
            'end_date' => '2025-04-20 17:00:00',
            'consultant_id' => 123
        ], $data[0]);

        $this->assertEquals([
            'availability_id' => 2,
            'available' => false,
            'start_date' => '2025-04-21 10:00:00',
            'end_date' => '2025-04-21 18:00:00',
            'consultant_id' => 456
        ], $data[1]);
    }
}
