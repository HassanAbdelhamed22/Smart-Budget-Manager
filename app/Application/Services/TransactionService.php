<?php

namespace App\Application\Services;

use App\Application\DTOs\BudgetDTO;
use App\Application\DTOs\TransactionDTO;
use App\Domain\Entities\TransactionEntity as Transaction;
use App\Domain\Entities\RecurringTransaction;
use App\Domain\Repositories\TransactionRepositoryInterface;
use App\Application\Services\BudgetService;

class TransactionService
{
    private TransactionRepositoryInterface $transactionRepository;
    private BudgetService $budgetService;

    public function __construct(TransactionRepositoryInterface $transactionRepository, BudgetService $budgetService)
    {
        $this->transactionRepository = $transactionRepository;
        $this->budgetService = $budgetService;
    }

    public function getTransaction(int $id): ?Transaction
    {
        return $this->transactionRepository->findById($id);
    }

    public function getUserTransactions(int $userId): array
    {
        return $this->transactionRepository->findByUserId($userId);
    }

    public function createTransaction(TransactionDTO $transactionDTO): void
    {
        $transaction = new Transaction(
            $transactionDTO->userId,
            $transactionDTO->accountId,
            $transactionDTO->categoryId,
            $transactionDTO->amount,
            $transactionDTO->type,
            $transactionDTO->date,
            $transactionDTO->payee,
            $transactionDTO->notes
        );
        $this->transactionRepository->save($transaction);

        // Decrease budget if transaction is an expense
        if ($transactionDTO->type === 'expense') {
            $this->adjustBudgetForTransaction($transactionDTO->userId, $transactionDTO->categoryId, $transactionDTO->amount, $transactionDTO->date);
        }
    }

    public function createRecurringTransaction(TransactionDTO $transactionDTO, string $frequency, ?string $endDate): void
    {
        $transaction = new RecurringTransaction(
            $transactionDTO->userId,
            $transactionDTO->accountId,
            $transactionDTO->categoryId,
            $transactionDTO->amount,
            $transactionDTO->type,
            $transactionDTO->date,
            $frequency,
            $endDate,
            $transactionDTO->payee,
            $transactionDTO->notes
        );
        $this->transactionRepository->save($transaction);

        // Decrease budget for the initial transaction if it’s an expense
        if ($transactionDTO->type === 'expense') {
            $this->adjustBudgetForTransaction($transactionDTO->userId, $transactionDTO->categoryId, $transactionDTO->amount, $transactionDTO->date);
        }
    }

    public function updateTransaction(int $id, TransactionDTO $transactionDTO): void
    {
        $transaction = $this->transactionRepository->findById($id);
        if (!$transaction) {
            throw new \Exception('Transaction not found');
        }
        $transaction->update(
            $transactionDTO->amount,
            $transactionDTO->type,
            $transactionDTO->date,
            $transactionDTO->payee,
            $transactionDTO->notes
        );
        $this->transactionRepository->save($transaction);
    }

    public function deleteTransaction(int $id): void
    {
        $this->transactionRepository->delete($id);
    }

    private function adjustBudgetForTransaction(int $userId, int $categoryId, float $amount, string $transactionDate): void
    {
        $budgets = $this->budgetService->getUserBudgets($userId);
        $transactionDateObj = new \DateTime($transactionDate);

        foreach ($budgets as $budget) {
            $startDate = new \DateTime($budget->getStartDate());
            $endDate = new \DateTime($budget->getEndDate());
            if ($budget->getCategoryId() === $categoryId && $transactionDateObj >= $startDate && $transactionDateObj <= $endDate) {
                $newAmount = $budget->getAmount() - $amount;
                $dto = new BudgetDTO(
                    $userId,
                    $categoryId,
                    $newAmount,
                    $budget->getStartDate(),
                    $budget->getEndDate()
                );
                $this->budgetService->updateBudget($budget->getId(), $dto);
                break; // Assume one budget per category and date range
            }
        }
    }
}