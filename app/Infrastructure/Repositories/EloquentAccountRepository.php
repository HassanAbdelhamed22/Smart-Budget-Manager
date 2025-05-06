<?php

namespace App\Infrastructure\Repositories;

use App\Domain\Entities\AccountEntity as Account; 
use App\Domain\Repositories\AccountRepositoryInterface;
use App\Models\Account as AccountModel;

class EloquentAccountRepository implements AccountRepositoryInterface
{
  public function findById(int $id): ?Account
  {
    $model = AccountModel::find($id);
    if (!$model) {
      return null;
    }

    return new Account(
      $model->user_id,
      $model->name,
      $model->type,
      $model->balance,
      $model->currency,
      $model->notes,
      $model->id
    );
  }

  public function findByUserId(int $userId): array
  {
    $models = AccountModel::where('user_id', $userId)->get();
    return $models->map(function ($model) {
      return new Account(
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

  public function save(Account $account): void
  {
    $data = [
      'user_id' => $account->getUserId(),
      'name' => $account->getName(),
      'type' => $account->getType(),
      'balance' => $account->getBalance(),
      'currency' => $account->getCurrency(),
      'notes' => $account->getNotes(),
    ];

    if ($account->getId()) {
      $model = AccountModel::find($account->getId());
      if ($model) {
        $model->update($data);
      } else {
        throw new \Exception('Account not found for update');
      }
    } else {
      $model = AccountModel::create($data);
      $account->setId($model->id);
    }
  }

  public function delete(int $id): void
  {
    AccountModel::destroy($id);
  }
}