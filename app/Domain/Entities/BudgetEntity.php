<?php

namespace App\Domain\Entities;

use JsonSerializable;

class BudgetEntity implements JsonSerializable
{
    private ?int $id = null;
    private int $userId;
    private int $categoryId;
    private float $amount;
    private string $startDate;
    private string $endDate;

    public function __construct(
        int $userId,
        int $categoryId,
        float $amount,
        string $startDate,
        string $endDate,
        ?int $id = null
    ) {
        $this->id = $id;
        $this->userId = $userId;
        $this->categoryId = $categoryId;
        $this->amount = $amount;
        $this->startDate = $startDate;
        $this->endDate = $endDate;
    }

    // Getters
    public function getId(): ?int { return $this->id; }
    public function getUserId(): int { return $this->userId; }
    public function getCategoryId(): int { return $this->categoryId; }
    public function getAmount(): float { return $this->amount; }
    public function getStartDate(): string { return $this->startDate; }
    public function getEndDate(): string { return $this->endDate; }

    // Setters
    public function update(float $amount, string $startDate, string $endDate): void
    {
        $this->amount = $amount;
        $this->startDate = $startDate;
        $this->endDate = $endDate;
    }

    public function setId(int $id): void { $this->id = $id; }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'user_id' => $this->userId,
            'category_id' => $this->categoryId,
            'amount' => $this->amount,
            'start_date' => $this->startDate,
            'end_date' => $this->endDate,
        ];
    }

    public function jsonSerialize(): array
    {
        return $this->toArray();
    }
}