<?php

namespace App\Domain\Repositories;

use App\Domain\Entities\TransactionEntity as Transaction;
use App\Domain\Entities\RecurringTransaction;

interface TransactionRepositoryInterface
{
    public function findById(int $id): ?Transaction;
    public function findByUserId(int $userId): array;
    public function findRecurringByUserId(int $userId): array;
    public function getUserAccounts(int $userId): array; // Returns Account entities
    public function save(Transaction $transaction): void;
    public function delete(int $id): void;
}