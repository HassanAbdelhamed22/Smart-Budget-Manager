<?php

namespace App\Domain\Entities;

class RecurringTransaction extends TransactionEntity
{
  private string $frequency; // e.g., 'weekly', 'monthly'
  private ?string $endDate = null;

  public function __construct(
    int $userId,
    int $accountId,
    int $categoryId,
    float $amount,
    string $type,
    string $date,
    string $frequency,
    ?string $endDate = null,
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

  // Setters
  public function updateFrequency(string $frequency): void
  {
    $this->frequency = $frequency;
  }
  public function setEndDate(?string $endDate): void
  {
    $this->endDate = $endDate;
  }

  public function toArray(): array
  {
    return array_merge(parent::toArray(), [
      'frequency' => $this->frequency,
      'end_date' => $this->endDate,
    ]);
  }
}