<?php

namespace App\Http\Controllers;

use App\Application\DTOs\AccountDTO;
use App\Application\Services\AccountService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Models\Account as AccountModel;


class AccountController extends Controller
{
  private AccountService $accountService;

  public function __construct(AccountService $accountService)
  {
    $this->accountService = $accountService;
  }

  public function index(): JsonResponse
  {
    $userId = auth()->id();
    if (!$userId) {
      return response()->json(['error' => 'Unauthorized'], 401);
    }
    $accounts = $this->accountService->getUserAccounts($userId);
    return response()->json($accounts);
  }

  public function show(int $id): JsonResponse
  {
    $account = $this->accountService->getAccount($id);
    $userId = auth()->id();
    if (!$account) {
      return response()->json(['error' => 'Account not found'], 404);
    }
    if ($account->getUserId() !== $userId) {
      return response()->json(['error' => 'Unauthorized'], 403);
    }
    return response()->json($account);
  }

  public function store(Request $request): JsonResponse
  {
    $validated = $request->validate([
      'name' => 'required|string|max:255',
      'type' => 'required|string|in:bank,cash,card',
      'balance' => 'required|numeric',
      'currency' => 'required|string|max:3',
      'notes' => 'nullable|string',
    ]);

    $dto = AccountDTO::fromArray(array_merge($validated, ['user_id' => auth()->id()]));
    $this->accountService->createAccount($dto);

    return response()->json(['message' => 'Account created successfully'], 201);
  }

  public function update(Request $request, int $id): JsonResponse
  {
    $validated = $request->validate([
      'name' => 'required|string|max:255',
      'type' => 'required|string|in:bank,cash,card',
      'balance' => 'required|numeric',
      'currency' => 'required|string|max:3',
      'notes' => 'nullable|string',
    ]);

    $dto = AccountDTO::fromArray(array_merge($validated, ['user_id' => auth()->id()]));
    try {
      $this->accountService->updateAccount($id, $dto);
      return response()->json(['message' => 'Account updated successfully']);
    } catch (\Exception $e) {
      return response()->json(['error' => $e->getMessage()], 404);
    }
  }

  public function destroy(int $id): JsonResponse
  {
    try {
      $this->accountService->deleteAccount($id);
      return response()->json(['message' => 'Account deleted successfully']);
    } catch (\Exception $e) {
      return response()->json(['error' => $e->getMessage()], 404);
    }
  }
}