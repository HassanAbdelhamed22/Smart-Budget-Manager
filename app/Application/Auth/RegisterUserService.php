<?php

namespace App\Application\Auth;

use App\Domain\User\UserRepositoryInterface;

class RegisterUserService
{
  public
  function __construct(private UserRepositoryInterface $users) {}
  public function register(array $data)
  {
    return $this->users->create($data);
  }
}
