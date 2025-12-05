<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;

class CategoriesApiController extends Controller
{
    public function show($id = null): JsonResponse
    {
        try {
            if ($id) {
                $category = Category::with('children')->find($id);

                return response()->json([
                    'success' => true,
                    'data' => $category
                ], 200);
            }

            $categories = Category::with('children')
                ->whereNull('parent_id')
                ->orderBy('sort_order', 'asc')
                ->orderBy('name', 'asc')
                ->get();
            
            return response()->json([
                'success' => true,
                'data' => $categories
            ], 200);
        } catch (\Exception $e) {
            \Log::error('Erro ao buscar categorias: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Erro ao buscar categorias',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function store(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                'description' => 'nullable|string',
                'parent_id' => 'nullable|integer|exists:categories,id',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Dados inválidos',
                ], 400);
            }

            $maxSortOrder = Category::where('parent_id', $request->parent_id)
                ->max('sort_order') ?? -1;
            
            $category = Category::create([
                'name' => $request->name,
                'description' => $request->description,
                'parent_id' => $request->parent_id,
                'sort_order' => $maxSortOrder + 1,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Categoria criada com sucesso',
                'data' => $category
            ], 200);
        } catch (\Exception $e) {
            \Log::error('Erro ao criar categoria: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Erro ao criar categoria',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function update(Request $request, $id): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                'description' => 'nullable|string',
            ]);

            $category = Category::find($id);

            if (!$category) {
                return response()->json([
                    'success' => false,
                    'message' => 'Categoria não encontrada'
                ], 404);
            }

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Dados inválidos',
                ], 400);
            }

            $category->update([
                'name' => $request->name,
                'description' => $request->description,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Categoria atualizada com sucesso',
                'data' => $category
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao atualizar categoria',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function destroy($id): JsonResponse
    {
        try {
            $category = Category::find($id);

            if (!$category) {
                return response()->json([
                    'success' => false,
                    'message' => 'Categoria não encontrada'
                ], 404);
            }

            $category->delete();

            return response()->json([
                'success' => true,
                'message' => 'Categoria deletada com sucesso'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao deletar categoria',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function trashed(): JsonResponse
    {
        try {
            $categories = Category::onlyTrashed()
                ->with(['children' => function ($query) {
                    $query->onlyTrashed();
                }])
                ->whereNull('parent_id')
                ->orderBy('deleted_at', 'desc')
                ->get();

            return response()->json([
                'success' => true,
                'data' => $categories
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao buscar categorias excluídas',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function restore($id): JsonResponse
    {
        try {
            $category = Category::onlyTrashed()->find($id);

            if (!$category) {
                return response()->json([
                    'success' => false,
                    'message' => 'Categoria não encontrada na lixeira'
                ], 404);
            }

            $category->restore();

            return response()->json([
                'success' => true,
                'message' => 'Categoria restaurada com sucesso'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao restaurar categoria',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function deletePermanent($id): JsonResponse
    {
        try {
            $category = Category::onlyTrashed()->find($id);

            if (!$category) {
                return response()->json([
                    'success' => false,
                    'message' => 'Categoria não encontrada na lixeira'
                ], 404);
            }

            $category->forceDelete();

            return response()->json([
                'success' => true,
                'message' => 'Categoria excluída permanentemente'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao excluir categoria permanentemente',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function reorder(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'categories' => 'required|array',
                'categories.*.id' => 'required|integer|exists:categories,id',
                'categories.*.sort_order' => 'required|integer',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Dados inválidos',
                    'errors' => $validator->errors()
                ], status: 400);
            }

            if (empty($request->categories)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Nenhuma categoria para reordenar'
                ], 400);
            }

            $firstCategory = Category::find($request->categories[0]['id']);
            if (!$firstCategory) {
                return response()->json([
                    'success' => false,
                    'message' => 'Categoria não encontrada'
                ], 404);
            }

            $parentId = $firstCategory->parent_id;

            foreach ($request->categories as $categoryData) {
                $category = Category::find($categoryData['id']);
                if ($category && $category->parent_id === $parentId) {
                    $category->update(['sort_order' => $categoryData['sort_order']]);
                }
            }

            return response()->json([
                'success' => true,
                'message' => 'Ordem das categorias atualizada com sucesso'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao reordenar categorias',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function statistics(): JsonResponse
    {
        try {
            $total = Category::count();
            $mainCategories = Category::whereNull('parent_id')->count();
            $subcategories = Category::whereNotNull('parent_id')->count();
            $active = Category::where('is_active', true)->count();

            return response()->json([
                'success' => true,
                'data' => [
                    'total' => $total,
                    'main_categories' => $mainCategories,
                    'subcategories' => $subcategories,
                    'active' => $active
                ]
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao buscar estatísticas',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}

