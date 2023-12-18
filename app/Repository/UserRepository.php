<?php

namespace App\Repositories;

use OPIS\ORM\EntityManager;
use App\Models\User;

class UserRepository
{
    /**
     * @var \OPIS\ORM\EntityManager
     */
    private $entityManager;

    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function all(): array
    {
        return $this->entityManager->getRepository(User::class)->findAll();
    }

    public function find(int $id): User
    {
        return $this->entityManager->getRepository(User::class)->find($id);
    }

    public function create(User $user): void
    {
        $this->entityManager->persist($user);
        $this->entityManager->flush();
    }

    public function update(User $user): void
    {
        $this->entityManager->merge($user);
        $this->entityManager->flush();
    }

    public function delete(User $user): void
    {
        $this->entityManager->remove($user);
        $this->entityManager->flush();
    }
}
