<?php

namespace App\Application\Auth;

use App\Models\User;
use App\Domain\User\UserRepositoryInterface;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class LoginUserService
{
  public function __construct(private UserRepositoryInterface $users) {}

  public function login(string $email, string $password): User
    {
        $user = $this->users->findModelByEmail($email);

        if (! $user || ! Hash::check($password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }

        return $user;
    }
}