<?php

namespace App\User\Application\User;

use App\User\Domain\Model\UserRepositoryInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Core\Exception\BadCredentialsException;

final readonly class UserLoginService
{
    public function __construct(
        private UserPasswordHasherInterface $passwordHasher,
        private JWTTokenManagerInterface $jwtManager,
        private UserRepositoryInterface $repository
    )
    {}

    public function __invoke(array $data): JsonResponse{
        $user = $this->repository->findUserByEmailOrFail($data['email']);

        if (!$this->passwordHasher->isPasswordValid($user, $data['password'])){
            throw new BadCredentialsException('Invalid email or password');
        }

        try {
            $token = $this->jwtManager->create($user);
        } catch (\Exception $e) {
            return new JsonResponse(['error' => 'Token generation failed: ' . $e->getMessage()], 500);
        }

        return new JsonResponse([
            'message' => 'User logged in successfully',
            'user_id' => $user->getId(),
            'token' => $token
        ], 200);
    }

}