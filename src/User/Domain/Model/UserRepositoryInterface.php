<?php

namespace App\User\Domain\Model;

use App\User\Domain\User;

interface UserRepositoryInterface
{
    public function findUserByEmail(string $email): ?User;
    public function findAllUsers(): array;
    public function add(User $admin): void;
    public function getAllAdmins(): array;
    public function findUserById(int $id): ?User;
    public function remove(User $admin): void;
    public function checkIfUserExists(string $email): bool;
    public function checkIfUserExists1(string $email): void;
    public function getUserByEmail(string $email): User;
    public function checkPasswordLength(string $password): void;


}