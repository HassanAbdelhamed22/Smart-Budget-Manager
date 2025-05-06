<?php

namespace App\Http\Controllers;

use App\Application\DTOs\TransactionDTO;
use App\Application\Services\TransactionService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class TransactionController extends Controller
{
  private TransactionService $transactionService;

  public function __construct(TransactionService $transactionService)
  {
    $this->transactionService = $transactionService;
  }

  public function index(): JsonResponse
  {
    $userId = auth()->id();
    if (!$userId) {
      return response()->json(['error' => 'Unauthorized'], 401);
    }
    $transactions = $this->transactionService->getUserTransactions($userId);
    return response()->json($transactions);
  }

  public function show(int $id): JsonResponse
  {
    $transaction = $this->transactionService->getTransaction($id);
    if (!$transaction || $transaction->getUserId() !== auth()->id()) {
      return response()->json(['error' => 'Transaction not found or unauthorized'], 403);
    }
    return response()->json($transaction);
  }

  public function store(Request $request): JsonResponse
  {
    $validated = $request->validate([
      'account_id' => 'required|integer|exists:accounts,id',
      'category_id' => 'required|integer|exists:categories,id',
      'amount' => 'required|numeric',
      'type' => 'required|in:income,expense',
      'date' => 'required|date',
      'payee' => 'nullable|string',
      'notes' => 'nullable|string',
      'frequency' => 'nullable|in:weekly,monthly', // For recurring
      'end_date' => 'nullable|date|after:date', // For recurring
    ]);

    $userId = auth()->id();
    if (!$userId) {
      return response()->json(['error' => 'Unauthorized'], 401);
    }

    $dto = TransactionDTO::fromArray(array_merge($validated, ['user_id' => $userId]));

    if (isset($validated['frequency'])) {
      $this->transactionService->createRecurringTransaction(
        $dto,
        $validated['frequency'],
        $validated['end_date'] ?? null
      );
    } else {
      $this->transactionService->createTransaction($dto);
    }

    return response()->json(['message' => 'Transaction created successfully'], 201);
  }

  public function update(Request $request, int $id): JsonResponse
  {
    $validated = $request->validate([
      'account_id' => 'required|integer|exists:accounts,id',
      'category_id' => 'required|integer|exists:categories,id',
      'amount' => 'required|numeric',
      'type' => 'required|in:income,expense',
      'date' => 'required|date',
      'payee' => 'nullable|string',
      'notes' => 'nullable|string',
    ]);

    $userId = auth()->id();
    if (!$userId) {
      return response()->json(['error' => 'Unauthorized'], 401);
    }

    $dto = TransactionDTO::fromArray(array_merge($validated, ['user_id' => $userId]));
    try {
      $this->transactionService->updateTransaction($id, $dto);
      return response()->json(['message' => 'Transaction updated successfully']);
    } catch (\Exception $e) {
      return response()->json(['error' => $e->getMessage()], 404);
    }
  }

  public function destroy(int $id): JsonResponse
  {
    try {
      $this->transactionService->deleteTransaction($id);
      return response()->json(['message' => 'Transaction deleted successfully']);
    } catch (\Exception $e) {
      return response()->json(['error' => $e->getMessage()], 404);
    }
  }
}
