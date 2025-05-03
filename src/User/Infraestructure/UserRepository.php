<?php

namespace App\User\Infraestructure;

use App\Shared\Domain\Exception\NotValidPasswordLengthException;
use App\Shared\Domain\Exception\UserAlreadyExistsException;
use App\Shared\Domain\Exception\UserNotFoundException;
use App\User\Domain\Model\UserRepositoryInterface;
use App\User\Domain\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use PHPUnit\Logging\Exception;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;


/**
 * @extends ServiceEntityRepository<User>
 *
 * @implements PasswordUpgraderInterface<User>
 *
 * @method User|null find($id, $lockMode = null, $lockVersion = null)
 * @method User|null findOneBy(array $criteria, array $orderBy = null)
 * @method User[]    findAll()
 * @method User[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */

final class UserRepository extends ServiceEntityRepository implements UserRepositoryInterface
{
    private const MIN_PASSWORD_LENGTH = 5;

    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        ManagerRegistry                         $registry
    )
    {
        parent::__construct($registry, User::class);
    }


    public function findUserByEmailOrFail(string $email): User
    {
        $user = $this->entityManager->getRepository(User::class)->findOneBy(['email' => $email]);

        if (!$user){
            throw new UserNotFoundException();
        }

        return $user;
    }

    public function findUserByIdOrFail(int $id): User
    {
        $user = $this->entityManager->getRepository(User::class)->find($id);

        if (!$user){
            throw new UserNotFoundException();
        }

        return $user;
    }

    public function findAllUsers(): array{
       return $this->entityManager->getRepository(User::class)->findAll();
    }

    public function add(User $admin): void
    {
        $this->entityManager->persist($admin);
        $this->entityManager->flush();
    }

    public function remove(User $admin): void{
        $this->entityManager->remove($admin);
        $this->entityManager->flush();
    }

    public function getAllAdmins(): array
    {
        return $this->entityManager->createQueryBuilder()
            ->select('u')
            ->from(User::class, 'u')
            ->where('u.roles LIKE :role')
            ->setParameter('role', '%ROLE_ADMIN%')
            ->getQuery()
            ->getResult();
    }

    public function checkIfUserExists(string $email): bool
    {

        $user = $this->entityManager->getRepository(User::class)->findOneBy(['email' => $email]);

        return $user !== null;
    }

    public function getUserByEmail(string $email): User
    {
        $user = $this->entityManager->getRepository(User::class)->findOneBy(['email' => $email]);

        if (!$user) {
            throw new UserNotFoundException();
        }

    return $user;
    }

    /**
     * @throws NotValidPasswordLengthException
     */
    public function checkPasswordLength(string $password): void
    {
    if (strlen($password) < self::MIN_PASSWORD_LENGTH) {
            throw new NotValidPasswordLengthException();
        }
    }

}