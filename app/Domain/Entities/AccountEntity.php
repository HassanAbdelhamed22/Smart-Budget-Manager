<?php

namespace App\Domain\Entities;

use JsonSerializable;

class AccountEntity implements JsonSerializable
{
  private ?int $id = null;
  private int $userId;
  private string $name;
  private string $type;
  private float $balance;
  private string $currency;
  private ?string $notes;

  public function __construct(
    int $userId,
    string $name,
    string $type,
    float $balance,
    string $currency,
    ?string $notes = null,
    ?int $id = null
  ) {
    $this->id = $id;
    $this->userId = $userId;
    $this->name = $name;
    $this->type = $type;
    $this->balance = $balance;
    $this->currency = $currency;
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

  public function getName(): string
  {
    return $this->name;
  }

  public function getType(): string
  {
    return $this->type;
  }

  public function getBalance(): float
  {
    return $this->balance;
  }

  public function getCurrency(): string
  {
    return $this->currency;
  }

  public function getNotes(): ?string
  {
    return $this->notes;
  }

  // Setters
  public function update(
    string $name,
    string $type,
    float $balance,
    string $currency,
    ?string $notes = null
  ): void {
    $this->name = $name;
    $this->type = $type;
    $this->balance = $balance;
    $this->currency = $currency;
    $this->notes = $notes;
  }

  public function setId(int $id): void
  {
    $this->id = $id;
  }

  // Ensure proper serialization
  public function toArray(): array
  {
    return [
      'id' => $this->id,
      'user_id' => $this->userId,
      'name' => $this->name,
      'type' => $this->type,
      'balance' => $this->balance,
      'currency' => $this->currency,
      'notes' => $this->notes,
    ];
  }

  // Implement JsonSerializable
  public function jsonSerialize(): array
  {
    return $this->toArray();
  }
}
