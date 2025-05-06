<?php

namespace App\Application\DTOs;

class AccountDTO
{
    public int $userId;
    public string $name;
    public string $type;
    public float $balance;
    public string $currency;
    public ?string $notes;

    public function __construct(
        int $userId,
        string $name,
        string $type,
        float $balance,
        string $currency,
        ?string $notes = null
    ) {
        $this->userId = $userId;
        $this->name = $name;
        $this->type = $type;
        $this->balance = $balance;
        $this->currency = $currency;
        $this->notes = $notes;
    }

    public static function fromArray(array $data): self
    {
        return new self(
            $data['user_id'],
            $data['name'],
            $data['type'],
            $data['balance'],
            $data['currency'],
            $data['notes'] ?? null
        );
    }

    public function toArray(): array
    {
        return [
            'user_id' => $this->userId,
            'name' => $this->name,
            'type' => $this->type,
            'balance' => $this->balance,
            'currency' => $this->currency,
            'notes' => $this->notes,
        ];
    }
}