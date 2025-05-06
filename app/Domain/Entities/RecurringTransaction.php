<?php

namespace App\Domain\Entities;

class RecurringTransaction extends TransactionEntity
{
  private string $frequency;
  private ?string $endDate;

  public function __construct(
    int $userId,
    int $accountId,
    int $categoryId,
    float $amount,
    string $type,
    string $date,
    string $frequency,
    ?string $endDate,
    ?string $payee = null,
    ?string $notes = null,
    ?int $id = null
  ) {
    parent::__construct($userId, $accountId, $categoryId, $amount, $type, $date, $payee, $notes, $id);
    $this->frequency = $frequency;
    $this->endDate = $endDate;
  }

  // Getters
  public function getFrequency(): string
  {
    return $this->frequency;
  }
  public function getEndDate(): ?string
  {
    return $this->endDate;
  }

  public function toArray(): array
  {
    $data = parent::toArray();
    $data['frequency'] = $this->frequency;
    $data['end_date'] = $this->endDate;
    return $data;
  }
}
