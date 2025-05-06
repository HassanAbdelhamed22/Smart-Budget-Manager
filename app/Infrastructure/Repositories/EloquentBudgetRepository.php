<?php

namespace App\Infrastructure\Repositories;

use App\Domain\Entities\BudgetEntity as Budget;
use App\Domain\Repositories\BudgetRepositoryInterface;
use App\Models\Budget as BudgetModel;

class EloquentBudgetRepository implements BudgetRepositoryInterface
{
  public function findById(int $id): ?Budget
  {
    $model = BudgetModel::find($id);
    if (!$model) return null;
    return new Budget(
      $model->user_id,
      $model->category_id,
      $model->amount,
      $model->start_date,
      $model->end_date,
      $model->id
    );
  }

  public function findByUserId(int $userId): array
  {
    $models = BudgetModel::where('user_id', $userId)->get();
    return $models->map(function ($model) {
      return new Budget(
        $model->user_id,
        $model->category_id,
        $model->amount,
        $model->start_date,
        $model->end_date,
        $model->id
      );
    })->toArray();
  }

  public function save(Budget $budget): void
  {
    $data = $budget->toArray();
    if ($budget->getId()) {
      $model = BudgetModel::find($budget->getId());
      if ($model) $model->update($data);
    } else {
      $model = BudgetModel::create($data);
      $budget->setId($model->id);
    }
  }

  public function delete(int $id): void
  {
    BudgetModel::destroy($id);
  }
}