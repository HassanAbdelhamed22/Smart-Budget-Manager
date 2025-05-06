<?php

namespace App\Domain\Repositories;

use App\Domain\Entities\BudgetEntity as Budget;

interface BudgetRepositoryInterface
{
  public function findById(int $id): ?Budget;
  public function findByUserId(int $userId): array;
  public function save(Budget $budget): void;
  public function delete(int $id): void;
}
