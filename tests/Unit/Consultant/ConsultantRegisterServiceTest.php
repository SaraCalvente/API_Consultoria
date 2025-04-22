<?php
declare(strict_types=1);

namespace App\Tests\Unit\Consultant;

use App\Consultant\Application\Consultant\ConsultantRegisterService;
use App\Consultant\Domain\Ability\Ability;
use App\Consultant\Domain\Consultant\Consultant;
use App\Consultant\Domain\Consultant\Profile;
use App\Consultant\Domain\Model\ConsultantRepositoryInterface;
use App\Consultant\Domain\Model\AbilityRepositoryInterface;
use App\Shared\Domain\Exception\NotValidEmailException;
use App\User\Domain\Model\UserRepositoryInterface;
use App\User\Domain\User;
use App\User\Domain\ValueObject\EmailValueObject;
use Doctrine\Common\Collections\ArrayCollection;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class ConsultantRegisterServiceTest extends TestCase
{
    private ConsultantRegisterService $service;
    private $hasher;
    private $consultantRepo;
    private $userRepo;
    private $abilityRepo;

    /**
     * @throws Exception
     */
    protected function setUp(): void
    {
        $this->hasher = $this->createMock(UserPasswordHasherInterface::class);
        $this->consultantRepo = $this->createMock(ConsultantRepositoryInterface::class);
        $this->userRepo = $this->createMock(UserRepositoryInterface::class);
        $this->abilityRepo = $this->createMock(AbilityRepositoryInterface::class);

        $this->service = new ConsultantRegisterService(
            $this->hasher,
            $this->consultantRepo,
            $this->userRepo,
            $this->abilityRepo
        );
    }

    /**
     * @throws Exception
     * @throws NotValidEmailException
     */
    public function testConsultantRegistersSuccessfully(): void
    {
        $email = 'test@example.com';
        $password = 'Secure1234';
        $name = 'Sara';
        $surnames = 'Calvente';
        $profile = 'Desarrollador';

        $ability1 = ['abilityName' => 'PHP', 'level' => 'ALTO'];
        $ability2 = ['abilityName' => 'JavaScript', 'level' => 'MEDIO'];


        $this->abilityRepo->method('findAbilityByNameAndLevel')
            ->willReturnMap([
                ['PHP', 'JUNIOR', $ability1],
                ['Java', 'SENIOR', $ability2]
            ]);



        $user = $this->createMock(User::class);
        $consultant = $this->createMock(Consultant::class);

        $this->userRepo->expects($this->once())->method('checkIfUserExists')->with($email);
        $this->userRepo->expects($this->once())->method('checkPasswordLength')->with($password);
        $this->hasher->expects($this->once())->method('hashPassword')->willReturn('hashed_password');
        $this->userRepo->expects($this->once())->method('add')->with($this->isInstanceOf(User::class));
        $this->consultantRepo->expects($this->once())->method('addConsultant')->with($this->isInstanceOf(Consultant::class));

        $user->method('getId')->willReturn(10);
        $emailVO = new EmailValueObject($email);
        $user->method('getEmail')->willReturn($emailVO);
        $user->method('getRoles')->willReturn(['ROLE_CONSULTANT']);

        $consultant->method('getId')->willReturn(1);
        $consultant->method('getName')->willReturn($name);
        $consultant->method('getProfile')->willReturn(Profile::from($profile));
        $consultant->method('getAbilities')->willReturn(new ArrayCollection());
        $consultant->method('getUser')->willReturn($user);

        $response = ($this->service)([
            'email' => $email,
            'password' => $password,
            'name' => $name,
            'surnames' => $surnames,
            'profile' => $profile,
            'abilities' => [$ability1, $ability2],
        ]);

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(201, $response->getStatusCode());
        $this->assertStringContainsString('Consultant successfully registered', $response->getContent());
    }

}

