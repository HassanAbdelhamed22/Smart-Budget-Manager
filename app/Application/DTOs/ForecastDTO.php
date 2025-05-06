<?php

namespace App\Application\DTOs;

class ForecastDTO
{
    public string $date;
    public float $projectedBalance;
    public array $transactions;

    public function __construct(string $date, float $projectedBalance, array $transactions)
    {
        $this->date = $date;
        $this->projectedBalance = $projectedBalance;
        $this->transactions = $transactions;
    }

    public function toArray(): array
    {
        return [
            'date' => $this->date,
            'projected_balance' => $this->projectedBalance,
            'transactions' => $this->transactions,
        ];
    }
}