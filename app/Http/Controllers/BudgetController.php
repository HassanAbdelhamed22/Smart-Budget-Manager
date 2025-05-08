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

    $budgetsArray = array_map(function ($budget) {
      $budgetData = $budget->toArray();
      $category = Category::find($budget->getCategoryId());
      $budgetData['category_id'] = $budget->getCategoryId(); // Include category_id
      $budgetData['category_name'] = $category->name ?? null;
      $budgetData['category_type'] = $category->type ?? null;
      $budgetData['category_color'] = $category->color ?? null;
      return $budgetData;
    }, $budgets);

    return response()->json($budgetsArray);
  }

  public function show(int $id): JsonResponse
  {
    $budget = $this->budgetService->getBudget($id);
    if (!$budget || $budget->getUserId() !== auth()->id()) {
      return response()->json(['error' => 'Budget not found or unauthorized'], 403);
    }

    $budgetData = $budget->toArray();
    $category = Category::find($budget->getCategoryId());
    $budgetData['category_id'] = $budget->getCategoryId(); // Include category_id
    $budgetData['category_name'] = $category->name ?? null;
    $budgetData['category_type'] = $category->type ?? null;
    $budgetData['category_color'] = $category->color ?? null;

    return response()->json($budgetData);
  }

  public function store(Request $request): JsonResponse
  {
    $validated = $request->validate([
      'category_name' => 'required_without:category_id|string|max:255',
      'category_id' => 'required_without:category_name|integer|exists:categories,id',
      'amount' => 'required|numeric',
      'start_date' => 'required|date',
      'end_date' => 'required|date|after_or_equal:start_date',
      'category_type' => 'nullable|in:income,expense', // Changed to category_type
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
        'type' => $validated['category_type'] ?? 'expense',
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
      'category_id' => 'nullable|integer|exists:categories,id',
      'category_name' => 'nullable|string|max:255',
      'amount' => 'required|numeric',
      'start_date' => 'required|date',
      'end_date' => 'required|date|after_or_equal:start_date',
      'category_type' => 'nullable|in:income,expense',
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

      // Update category attributes if category_name, category_type, or color is provided
      if (isset($validated['category_name'])) {
        $category->name = $validated['category_name'];
      }
      if (isset($validated['category_type'])) {
        $category->type = $validated['category_type'];
      }
      if (isset($validated['color'])) {
        $category->color = $validated['color'];
      }
      $category->save();
    } elseif (isset($validated['category_name'])) {
      // Create new category if no category_id is provided
      $category = Category::create([
        'user_id' => $userId,
        'name' => $validated['category_name'],
        'type' => $validated['category_type'] ?? 'expense',
        'color' => $validated['color'] ?? '#6b7280',
      ]);
      $categoryId = $category->id;
      logger('New category created: ' . $validated['category_name'] . ' for user_id: ' . $userId);
    } else {
      return response()->json(['error' => 'Either category_id or category_name is required'], 422);
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
      $budget = $this->budgetService->getBudget($id); // Fetch updated budget
      $budgetData = $budget->toArray();
      $category = Category::find($budget->getCategoryId());
      $budgetData['category_id'] = $budget->getCategoryId();
      $budgetData['category_name'] = $category->name ?? null;
      $budgetData['category_type'] = $category->type ?? null;
      $budgetData['category_color'] = $category->color ?? null;
      return response()->json($budgetData);
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
