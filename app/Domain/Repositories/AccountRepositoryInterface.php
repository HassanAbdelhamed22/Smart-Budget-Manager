<?php

namespace App\Domain\Repositories;

use App\Domain\Entities\AccountEntity as Account;

interface AccountRepositoryInterface
{
  public function findById(int $id): ?Account;
  public function findByUserId(int $userId): array;
  public function save(Account $account): void;
  public function delete(int $id): void;
}
