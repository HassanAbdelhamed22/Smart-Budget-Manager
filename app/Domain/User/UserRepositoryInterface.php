<?php

namespace App\Domain\User;

use App\Models\User;

interface UserRepositoryInterface
{
  public function create(array $data): User;
  public function findByEmail(string $email): ?User;
  public function findModelByEmail(string $email): ?User;
}
