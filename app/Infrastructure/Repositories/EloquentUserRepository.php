<?php

namespace App\Infrastructure\Repositories;

use App\Models\User;
use App\Domain\User\UserRepositoryInterface;

class EloquentUserRepository implements UserRepositoryInterface
{
  public function create(array $data): User
  {
    return User::create($data);
  }

  public function findByEmail(string $email): ?User
  {
    return User::where('email', $email)->first();
  }

  public function findModelByEmail(string $email): ?User
  {
    return User::where('email', $email)->first();
  }
}
