<?php

namespace App\Infrastructure\Repositories;

use App\Domain\Entities\TransactionEntity as Transaction;
use App\Domain\Entities\RecurringTransaction;
use App\Domain\Repositories\TransactionRepositoryInterface;
use App\Models\Account as AccountModel;
use App\Models\Transaction as TransactionModel;

class EloquentTransactionRepository implements TransactionRepositoryInterface
{
  public function findById(int $id): ?Transaction
  {
    $model = TransactionModel::find($id);
    if (!$model) return null;
    return $this->mapToEntity($model);
  }

  public function findByUserId(int $userId): array
  {
    $models = TransactionModel::where('user_id', $userId)
      ->whereNull('frequency')
      ->get();
    return $models->map(function (TransactionModel $model) {
      return $this->mapToEntity($model);
    })->toArray();
  }

  public function findRecurringByUserId(int $userId): array
  {
    $models = TransactionModel::where('user_id', $userId)
      ->whereNotNull('frequency')
      ->get();
    return $models->map(function (TransactionModel $model) {
      return $this->mapToEntity($model);
    })->toArray();
  }

  public function getUserAccounts(int $userId): array
  {
    $models = AccountModel::where('user_id', $userId)->get();
    return $models->map(function ($model) {
      return new \App\Domain\Entities\AccountEntity(
        $model->user_id,
        $model->name,
        $model->type,
        $model->balance,
        $model->currency,
        $model->notes,
        $model->id
      );
    })->toArray();
  }

  public function save(Transaction $transaction): void
  {
    $data = [
      'user_id' => $transaction->getUserId(),
      'account_id' => $transaction->getAccountId(),
      'category_id' => $transaction->getCategoryId(),
      'amount' => $transaction->getAmount(),
      'type' => $transaction->getType(),
      'date' => $transaction->getDate(),
      'payee' => $transaction->getPayee(),
      'notes' => $transaction->getNotes(),
    ];

    if ($transaction instanceof RecurringTransaction) {
      $data['frequency'] = $transaction->getFrequency();
      $data['end_date'] = $transaction->getEndDate();
    }

    if ($transaction->getId()) {
      $model = TransactionModel::find($transaction->getId());
      if ($model) $model->update($data);
    } else {
      $model = TransactionModel::create($data);
      $transaction->setId($model->id);
    }
  }

  public function delete(int $id): void
  {
    TransactionModel::destroy($id);
  }

  private function mapToEntity(TransactionModel $model): Transaction
  {
    if ($model->frequency) {
      return new RecurringTransaction(
        $model->user_id,
        $model->account_id,
        $model->category_id,
        (float) $model->amount,
        $model->type,
        $model->date->toDateString(),
        $model->frequency,
        $model->end_date ? $model->end_date->toDateString() : null,
        $model->payee,
        $model->notes,
        $model->id
      );
    }
    return new Transaction(
      $model->user_id,
      $model->account_id,
      $model->category_id,
      (float) $model->amount,
      $model->type,
      $model->date->toDateString(),
      $model->payee,
      $model->notes,
      $model->id
    );
  }
}
