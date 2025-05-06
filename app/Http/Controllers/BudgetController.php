<?php

namespace App\Http\Controllers;

use App\Application\DTOs\BudgetDTO;
use App\Application\Services\BudgetService;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class BudgetController extends Controller
{
  private BudgetService $budgetService;

  public function __construct(BudgetService $budgetService)
  {
    $this->budgetService = $budgetService;
  }

  public function index(): JsonResponse
  {
    $userId = auth()->id();
    if (!$userId) {
      return response()->json(['error' => 'Unauthorized'], 401);
    }
    $budgets = $this->budgetService->getUserBudgets($userId);
    // Convert Budget entities to arrays
    $budgetsArray = array_map(fn($budget) => $budget->toArray(), $budgets);
    return response()->json($budgetsArray);
  }

  public function show(int $id): JsonResponse
  {
    $budget = $this->budgetService->getBudget($id);
    if (!$budget || $budget->getUserId() !== auth()->id()) {
      return response()->json(['error' => 'Budget not found or unauthorized'], 403);
    }
    // Convert Budget entity to array
    return response()->json($budget->toArray());
  }

  public function store(Request $request): JsonResponse
  {
    $validated = $request->validate([
      'category_name' => 'required_without:category_id|string|max:255',
      'category_id' => 'required_without:category_name|integer|exists:categories,id',
      'amount' => 'required|numeric',
      'start_date' => 'required|date',
      'end_date' => 'required|date|after_or_equal:start_date',
      'type' => 'nullable|in:income,expense',
      'color' => 'nullable|string|regex:/^#[0-9a-fA-F]{6}$/',
    ]);

    $userId = auth()->id();
    if (!$userId) {
      return response()->json(['error' => 'Unauthorized'], 401);
    }

    $categoryId = null;
    if (isset($validated['category_id'])) {
      $category = Category::where('id', $validated['category_id'])
        ->where(function ($query) use ($userId) {
          $query->where('user_id', $userId)->orWhereNull('user_id');
        })->first();
      if (!$category) {
        return response()->json(['error' => 'Category not found or unauthorized'], 404);
      }
      $categoryId = $category->id;
    } else {
      $category = Category::create([
        'user_id' => $userId,
        'name' => $validated['category_name'],
        'type' => $validated['type'] ?? 'expense',
        'color' => $validated['color'] ?? '#6b7280',
      ]);
      $categoryId = $category->id;
      logger('New category created: ' . $validated['category_name'] . ' for user_id: ' . $userId);
    }

    $dto = new BudgetDTO(
      $userId,
      $categoryId,
      $validated['amount'],
      $validated['start_date'],
      $validated['end_date']
    );
    $this->budgetService->createBudget($dto);
    return response()->json(['message' => 'Budget created successfully'], 201);
  }

  public function update(Request $request, int $id): JsonResponse
  {
    $validated = $request->validate([
      'category_name' => 'required_without:category_id|string|max:255',
      'category_id' => 'required_without:category_name|integer|exists:categories,id',
      'amount' => 'required|numeric',
      'start_date' => 'required|date',
      'end_date' => 'required|date|after_or_equal:start_date',
      'type' => 'nullable|in:income,expense',
      'color' => 'nullable|string|regex:/^#[0-9a-fA-F]{6}$/',
    ]);

    $userId = auth()->id();
    if (!$userId) {
      return response()->json(['error' => 'Unauthorized'], 401);
    }

    $categoryId = null;
    if (isset($validated['category_id'])) {
      $category = Category::where('id', $validated['category_id'])
        ->where(function ($query) use ($userId) {
          $query->where('user_id', $userId)->orWhereNull('user_id');
        })->first();
      if (!$category) {
        return response()->json(['error' => 'Category not found or unauthorized'], 404);
      }
      $categoryId = $category->id;
    } else {
      $category = Category::create([
        'user_id' => $userId,
        'name' => $validated['category_name'],
        'type' => $validated['type'] ?? 'expense',
        'color' => $validated['color'] ?? '#6b7280',
      ]);
      $categoryId = $category->id;
      logger('New category created: ' . $validated['category_name'] . ' for user_id: ' . $userId);
    }

    $dto = new BudgetDTO(
      $userId,
      $categoryId,
      $validated['amount'],
      $validated['start_date'],
      $validated['end_date']
    );
    try {
      $this->budgetService->updateBudget($id, $dto);
      return response()->json(['message' => 'Budget updated successfully']);
    } catch (\Exception $e) {
      return response()->json(['error' => $e->getMessage()], 404);
    }
  }

  public function destroy(int $id): JsonResponse
  {
    try {
      $this->budgetService->deleteBudget($id);
      return response()->json(['message' => 'Budget deleted successfully']);
    } catch (\Exception $e) {
      return response()->json(['error' => $e->getMessage()], 404);
    }
  }

  public function forecast(Request $request): JsonResponse
  {
    $validated = $request->validate([
      'start_date' => 'required|date',
      'end_date' => 'required|date|after_or_equal:start_date',
    ]);

    $userId = auth()->id();
    if (!$userId) {
      return response()->json(['error' => 'Unauthorized'], 401);
    }
    $forecast = $this->budgetService->generateForecast($userId, $validated['start_date'], $validated['end_date']);
    return response()->json($forecast);
  }

  public function getCategories(): JsonResponse
  {
    $userId = auth()->id();
    if (!$userId) {
      return response()->json(['error' => 'Unauthorized'], 401);
    }
    $categories = Category::where(function ($query) use ($userId) {
      $query->where('user_id', $userId)->orWhereNull('user_id');
    })->get()
      ->map(fn($c) => [
        'id' => $c->id,
        'name' => $c->name,
        'type' => $c->type,
        'color' => $c->color,
      ]);
    return response()->json($categories);
  }
}