<?php

namespace App\Application\DTOs;

class BudgetDTO
{
  public int $userId;
  public int $categoryId;
  public float $amount;
  public string $startDate;
  public string $endDate;

  public function __construct(
    int $userId,
    int $categoryId,
    float $amount,
    string $startDate,
    string $endDate
  ) {
    $this->userId = $userId;
    $this->categoryId = $categoryId;
    $this->amount = $amount;
    $this->startDate = $startDate;
    $this->endDate = $endDate;
  }

  public static function fromArray(array $data): self
  {
    return new self(
      $data['user_id'],
      $data['category_id'],
      $data['amount'],
      $data['start_date'],
      $data['end_date']
    );
  }

  public function toArray(): array
  {
    return [
      'user_id' => $this->userId,
      'category_id' => $this->categoryId,
      'amount' => $this->amount,
      'start_date' => $this->startDate,
      'end_date' => $this->endDate,
    ];
  }
}
