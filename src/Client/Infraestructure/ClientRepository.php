<?php
declare(strict_types=1);

namespace App\Client\Infraestructure;

use App\Client\Domain\Client;
use App\Client\Domain\ClientDTO;
use App\Client\Domain\Model\ClientRepositoryInterface;
use App\Shared\Domain\Exception\ClientNotFoundException;
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


    public function findClientByUser(User $user): ?Client
    {
        $client = $this->entityManager->getRepository(Client::class)->findOneBy(['user' => $user]);
        if (!$client) {
            throw new ClientNotFoundException();
        }
        return $client;
    }

    public function checkIfClientExists(User $user): bool
    {
        $client = $this->entityManager->getRepository(Client::class)->findOneBy(['user' => $user]);
        if (!$client) {
            return false;
        }
        return true;
    }

    public function getClientByUser(User $user): Client
    {
        $client = $this->entityManager->getRepository(Client::class)->findOneBy(['user' => $user]);

        if (!$client) {
            throw new ClientNotFoundException("No client found for user with email: " . $user->getEmail());
        }

        return $client;
    }

    public function findAllClients(): array
    {
        return $this->entityManager->getRepository(Client::class)->findAll();
    }

    public function updateClient(User $user, ?string $address, ?string $phoneNumber): JsonResponse
    {
        $client = $this->findClientByUser($user);

        if ($address && !$phoneNumber) {
            $client->setAddress($address);
            $this->saveClient();

            return new JsonResponse([
                'message' => 'Client address updated successfully',
                ClientDTO::fromEntity($client)
            ]);
        }

        if ($phoneNumber && !$address) {
            $client->setPhoneNumber($phoneNumber);
            $this->saveClient();

            return new JsonResponse([
                'message' => 'Client phone number updated successfully',
                ClientDTO::fromEntity($client)
            ]);
        }

        if ($address && $phoneNumber) {
            $client->setAddress($address);
            $client->setPhoneNumber($phoneNumber);
            $this->saveClient();
        }

        return new JsonResponse([
            'message' => 'Client address and phone number updated successfully',
            ClientDTO::fromEntity($client)
        ]);
    }

    public function deleteClient(Client $client): JsonResponse
    {
        $this->removeClient($client);

        return new JsonResponse([
            'success' => 'Client and associated user deleted successfully',
        ]);
    }

    public function addClient(Client $client): void
    {
        $this->entityManager->persist($client);
        $this->entityManager->flush();
    }

    public function saveClient(): void
    {
        $this->entityManager->flush();
    }

    public function removeClient(Client $client): void
    {
        $this->entityManager->remove($client);
        $this->entityManager->flush();

    }
}