<?php

namespace App\Application\Services;

use App\Application\DTOs\BudgetDTO;
use App\Application\DTOs\ForecastDTO;
use App\Domain\Entities\BudgetEntity;
use App\Domain\Repositories\BudgetRepositoryInterface;
use App\Domain\Repositories\TransactionRepositoryInterface;
use DateTime;

class BudgetService
{
  private BudgetRepositoryInterface $budgetRepository;
  private TransactionRepositoryInterface $transactionRepository;

  public function __construct(
    BudgetRepositoryInterface $budgetRepository,
    TransactionRepositoryInterface $transactionRepository
  ) {
    $this->budgetRepository = $budgetRepository;
    $this->transactionRepository = $transactionRepository;
  }

  public function getBudget(int $id): ?BudgetEntity
  {
    return $this->budgetRepository->findById($id);
  }

  public function getUserBudgets(int $userId): array
  {
    return $this->budgetRepository->findByUserId($userId);
  }

  public function createBudget(BudgetDTO $budgetDTO): void
  {
    $budget = new BudgetEntity(
      $budgetDTO->userId,
      $budgetDTO->categoryId,
      $budgetDTO->amount,
      $budgetDTO->startDate,
      $budgetDTO->endDate
    );
    $this->budgetRepository->save($budget);
  }

  public function updateBudget(int $id, BudgetDTO $budgetDTO): void
  {
    $budget = $this->budgetRepository->findById($id);
    if (!$budget) {
      throw new \Exception('Budget not found');
    }
    $budget->update($budgetDTO->amount, $budgetDTO->startDate, $budgetDTO->endDate);
    $this->budgetRepository->save($budget);
  }

  public function deleteBudget(int $id): void
  {
    $this->budgetRepository->delete($id);
  }

  public function generateForecast(int $userId, string $startDate, string $endDate): array
  {
    $budgets = $this->getUserBudgets($userId);
    $transactions = $this->transactionRepository->findByUserId($userId);
    $recurringTransactions = $this->transactionRepository->findRecurringByUserId($userId);
    $forecast = [];

    $currentDate = new DateTime($startDate);
    $endDateObj = new DateTime($endDate);
    $currentBalance = $this->getCurrentBalance($userId, $startDate);

    while ($currentDate <= $endDateObj) {
      $projectedBalance = $currentBalance;
      $dailyTransactions = [];

      // Apply transactions
      foreach ($transactions as $transaction) {
        $transactionDate = new DateTime($transaction->getDate());
        if ($transactionDate->format('Y-m-d') === $currentDate->format('Y-m-d')) {
          $projectedBalance += ('income' === $transaction->getType() ? 1 : -1) * $transaction->getAmount();
          $dailyTransactions[] = $transaction->toArray();
        }
      }

      // Apply recurring transactions
      foreach ($recurringTransactions as $recurring) {
        if ($this->isActiveRecurring($recurring, $currentDate)) {
          $projectedBalance += ('income' === $recurring->getType() ? 1 : -1) * $recurring->getAmount();
          $dailyTransactions[] = $recurring->toArray();
        }
      }

      // Apply budgets (simple check if over budget)
      foreach ($budgets as $budget) {
        $budgetStart = new DateTime($budget->getStartDate());
        $budgetEnd = new DateTime($budget->getEndDate());
        if ($currentDate >= $budgetStart && $currentDate <= $budgetEnd) {
          $spent = $this->calculateCategorySpending($transactions, $recurringTransactions, $budget->getCategoryId(), $currentDate);
          if ($spent > $budget->getAmount()) {
            $projectedBalance -= ($spent - $budget->getAmount()); // Penalize for overspending
          }
        }
      }

      $forecast[] = new ForecastDTO(
        $currentDate->format('Y-m-d'),
        $projectedBalance,
        $dailyTransactions
      );
      $currentDate->modify('+1 day');
      $currentBalance = $projectedBalance;
    }

    return array_map(fn($f) => $f->toArray(), $forecast);
  }

  private function getCurrentBalance(int $userId, string $date): float
  {
    $accounts = $this->transactionRepository->getUserAccounts($userId);
    $balance = 0;
    foreach ($accounts as $account) {
      $balance += $account->getBalance();
    }
    $pastTransactions = array_filter(
      $this->transactionRepository->findByUserId($userId),
      fn($t) => new DateTime($t->getDate()) < new DateTime($date)
    );
    foreach ($pastTransactions as $t) {
      $balance += ('income' === $t->getType() ? 1 : -1) * $t->getAmount();
    }
    return $balance;
  }

  private function isActiveRecurring($recurring, DateTime $date): bool
  {
    $start = new DateTime($recurring->getDate());
    $end = $recurring->getEndDate() ? new DateTime($recurring->getEndDate()) : null;
    if ($date < $start) return false;
    if ($end && $date > $end) return false;
    $frequency = $recurring->getFrequency();
    // Simplified: Implement actual frequency logic (e.g., weekly, monthly)
    return true; // Placeholder
  }

  private function calculateCategorySpending($transactions, $recurring, int $categoryId, DateTime $date): float
  {
    $spent = 0;
    foreach ($transactions as $t) {
      if ($t->getCategoryId() === $categoryId && new DateTime($t->getDate()) <= $date) {
        $spent += ('expense' === $t->getType() ? $t->getAmount() : 0);
      }
    }
    foreach ($recurring as $r) {
      if ($r->getCategoryId() === $categoryId && $this->isActiveRecurring($r, $date)) {
        $spent += ('expense' === $r->getType() ? $r->getAmount() : 0);
      }
    }
    return $spent;
  }
}