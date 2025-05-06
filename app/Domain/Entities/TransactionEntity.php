<?php

namespace App\Domain\Entities;

class TransactionEntity
{
  private ?int $id = null;
  private int $userId;
  private int $accountId;
  private int $categoryId;
  private float $amount;
  private string $type; // 'income' or 'expense'
  private string $date;
  private ?string $payee = null;
  private ?string $notes = null;

  public function __construct(
    int $userId,
    int $accountId,
    int $categoryId,
    float $amount,
    string $type,
    string $date,
    ?string $payee = null,
    ?string $notes = null,
    ?int $id = null
  ) {
    $this->id = $id;
    $this->userId = $userId;
    $this->accountId = $accountId;
    $this->categoryId = $categoryId;
    $this->amount = $amount;
    $this->type = $type;
    $this->date = $date;
    $this->payee = $payee;
    $this->notes = $notes;
  }

  // Getters
  public function getId(): ?int
  {
    return $this->id;
  }
  public function getUserId(): int
  {
    return $this->userId;
  }
  public function getAccountId(): int
  {
    return $this->accountId;
  }
  public function getCategoryId(): int
  {
    return $this->categoryId;
  }
  public function getAmount(): float
  {
    return $this->amount;
  }
  public function getType(): string
  {
    return $this->type;
  }
  public function getDate(): string
  {
    return $this->date;
  }
  public function getPayee(): ?string
  {
    return $this->payee;
  }
  public function getNotes(): ?string
  {
    return $this->notes;
  }

  // Setters
  public function update(
    float $amount,
    string $type,
    string $date,
    ?string $payee = null,
    ?string $notes = null
  ): void {
    $this->amount = $amount;
    $this->type = $type;
    $this->date = $date;
    $this->payee = $payee;
    $this->notes = $notes;
  }

  public function setId(int $id): void
  {
    $this->id = $id;
  }

  public function toArray(): array
  {
    return [
      'id' => $this->id,
      'user_id' => $this->userId,
      'account_id' => $this->accountId,
      'category_id' => $this->categoryId,
      'amount' => $this->amount,
      'type' => $this->type,
      'date' => $this->date,
      'payee' => $this->payee,
      'notes' => $this->notes,
    ];
  }
}