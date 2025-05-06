<?php

namespace App\Domain\User;

class UserEntity
{
  public function __construct(
    public readonly int $id,
    public string $name,
    public string $email,
    public ?string $password = null,
  ) {}
}