<?php

namespace App\Application\Services;

use App\Application\DTOs\AccountDTO;
use App\Domain\Entities\AccountEntity as Account;
use App\Domain\Repositories\AccountRepositoryInterface;

class AccountService
{
  private AccountRepositoryInterface $accountRepository;

  public function __construct(AccountRepositoryInterface $accountRepository)
  {
    $this->accountRepository = $accountRepository;
  }

  public function getAccount(int $id): ?Account
  {
    return $this->accountRepository->findById($id);
  }

  public function getUserAccounts(int $userId): array
  {
    logger('AccountService: Fetching accounts for user_id: ' . $userId);
    $accounts = $this->accountRepository->findByUserId($userId);
    logger('AccountService: Retrieved accounts count: ' . count($accounts));
    logger('AccountService: Retrieved accounts data: ' . json_encode($accounts));
    return $accounts;
  }

  public function createAccount(AccountDTO $accountDTO): void
  {
    $account = new Account(
      $accountDTO->userId,
      $accountDTO->name,
      $accountDTO->type,
      $accountDTO->balance,
      $accountDTO->currency,
      $accountDTO->notes
    );
    $this->accountRepository->save($account);
  }

  public function updateAccount(int $id, AccountDTO $accountDTO): void
  {
    $account = $this->accountRepository->findById($id);
    if (!$account) {
      throw new \Exception('Account not found');
    }
    $account->update(
      $accountDTO->name,
      $accountDTO->type,
      $accountDTO->balance,
      $accountDTO->currency,
      $accountDTO->notes
    );
    $this->accountRepository->save($account);
  }

  public function deleteAccount(int $id): void
  {
    $account = $this->accountRepository->findById($id);
    if (!$account) {
      throw new \Exception('Account not found');
    }
    $this->accountRepository->delete($id);
  }
}