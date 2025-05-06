<?php

namespace App\Infrastructure\Repositories;

use App\Domain\Entities\TransactionEntity as Transaction;
use App\Domain\Entities\RecurringTransaction;
use App\Domain\Repositories\TransactionRepositoryInterface;
use App\Models\Account as AccountModel;
use App\Models\Transaction as TransactionModel;
use App\Models\RecurringTransaction as RecurringTransactionModel;

class EloquentTransactionRepository implements TransactionRepositoryInterface
{
  public function findById(int $id): ?Transaction
  {
    $model = TransactionModel::find($id);
    if (!$model) return null;
    return new Transaction(
      $model->user_id,
      $model->account_id,
      $model->category_id,
      $model->amount,
      $model->type,
      $model->date,
      $model->payee,
      $model->notes,
      $model->id
    );
  }

  public function findByUserId(int $userId): array
  {
    $models = TransactionModel::where('user_id', $userId)->get();
    return $models->map(function ($model) {
      return new Transaction(
        $model->user_id,
        $model->account_id,
        $model->category_id,
        $model->amount,
        $model->type,
        $model->date,
        $model->payee,
        $model->notes,
        $model->id
      );
    })->toArray();
  }

  public function findRecurringByUserId(int $userId): array
  {
    $models = RecurringTransactionModel::where('user_id', $userId)->get();
    return $models->map(function ($model) {
      return new RecurringTransaction(
        $model->user_id,
        $model->account_id,
        $model->category_id,
        $model->amount,
        $model->type,
        $model->date,
        $model->frequency,
        $model->end_date,
        $model->payee,
        $model->notes,
        $model->id
      );
    })->toArray();
  }

  public function getUserAccounts(int $userId): array
  {
    $models = AccountModel::where('user_id', $userId)->get();
    return $models->map(function ($model) {
      // Assuming Account entity exists or create a simple one
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
    $data = $transaction->toArray();
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
}