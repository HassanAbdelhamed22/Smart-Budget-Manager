<?php

namespace App\Application\DTOs;

class TransactionDTO
{
    public int $userId;
    public int $accountId;
    public int $categoryId;
    public float $amount;
    public string $type; // 'income' or 'expense'
    public string $date;
    public ?string $payee = null;
    public ?string $notes = null;

    public function __construct(
        int $userId,
        int $accountId,
        int $categoryId,
        float $amount,
        string $type,
        string $date,
        ?string $payee = null,
        ?string $notes = null
    ) {
        $this->userId = $userId;
        $this->accountId = $accountId;
        $this->categoryId = $categoryId;
        $this->amount = $amount;
        $this->type = $type;
        $this->date = $date;
        $this->payee = $payee;
        $this->notes = $notes;
    }

    public static function fromArray(array $data): self
    {
        return new self(
            $data['user_id'],
            $data['account_id'],
            $data['category_id'],
            $data['amount'],
            $data['type'],
            $data['date'],
            $data['payee'] ?? null,
            $data['notes'] ?? null
        );
    }

    public function toArray(): array
    {
        return [
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