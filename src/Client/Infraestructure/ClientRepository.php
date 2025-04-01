<?php

namespace App\Client\Infraestructure;

use App\Client\Domain\Client;
use App\Client\Domain\ClientDTO;
use App\Client\Domain\Model\ClientRepositoryInterface;
use App\Project\Domain\Project;
use App\Shared\Domain\Exception\ClientNotFoundException;
use App\Shared\Domain\Exception\UserNotFoundException;
use App\User\Domain\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\JsonResponse;


/**
 * @extends ServiceEntityRepository<Client>
 *
 * @method Client|null find($id, $lockMode = null, $lockVersion = null)
 * @method Client|null findOneBy(array $criteria, array $orderBy = null)
 * @method Client[]    findAll()
 * @method Client[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ClientRepository extends ServiceEntityRepository implements ClientRepositoryInterface
{
    private EntityManagerInterface $entityManager;
    public function __construct( EntityManagerInterface $entityManager, ManagerRegistry $registry)
    {
        parent::__construct($registry, Client::class);
        $this->entityManager = $entityManager;
    }

    public function add(Client $client): void
    {
        $this->entityManager->persist($client);
        $this->entityManager->flush();
    }

    public function findUserAndClient(array $criteria): array
    {
        $user = $this->entityManager->getRepository(User::class)->findOneBy($criteria);
        if (!$user) {
            throw new UserNotFoundException();
        }

        $client = $this->entityManager->getRepository(Client::class)->findOneBy(['user' => $user]);
        if (!$client) {
            throw new ClientNotFoundException();
        }

        return [$user, $client];
    }

    public function checkIfUserExists(string $email): bool{
        $user = $this->entityManager->getRepository(User::class)->findOneBy(['email' => $email]);
        if ($user) {
            return false;
        }
        $client = $this->entityManager->getRepository(Client::class)->findOneBy(['user' => $user]);
        if ($client) {
            return false;
        }
        return true;
    }

    public function findClientById(int $user): ?Client{
        $client = $this->entityManager->getRepository(Client::class)->findOneBy(['user' => $user]);
        if (!$client) {
            throw new ClientNotFoundException();
        }
        return $client;
    }

    public function findAllClients(): array{
        return $this->entityManager->getRepository(Client::class)->findAll();
    }

    public function modifyClient(array $criteria, ?string $address, ?string $phoneNumber): JsonResponse
    {
        [, $client] = $this->findUserAndClient($criteria);

        if ($address !== null) {
            $client->setAddress($address);
        }
        if ($phoneNumber !== null) {
            $client->setPhoneNumber($phoneNumber);
        }

        $this->entityManager->flush();

        return new JsonResponse(ClientDTO::fromEntity($client));
    }

    public function removeClient(array $criteria): JsonResponse
    {
        [$user, $client] = $this->findUserAndClient($criteria);

        $projects = $this->entityManager->getRepository(Project::class)->findBy(['client' => $client->getId()]);

        if (count($projects) > 0) {
            return new JsonResponse(['error' => 'Cannot delete client because there are associated projects.'], 400);
        }

        $this->entityManager->remove($client);
        $this->entityManager->remove($user);
        $this->entityManager->flush();

        return new JsonResponse(['message' => 'Client and associated user deleted successfully'], 200);
    }

}