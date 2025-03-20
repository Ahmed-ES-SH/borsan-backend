<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCategoryRequest;
use App\Http\Requests\UpdateCategoryRequest;
use App\Http\Services\ImageService;
use App\Http\Traits\ApiResponse;
use App\Models\ArticleCategory;
use Illuminate\Http\Request;

class ArticleCategoryController extends Controller
{

    use ApiResponse;
    protected $imageservice;

    public function __construct(ImageService $imageService)
    {
        $this->imageservice = $imageService;
    }


    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $Categories = ArticleCategory::orderBy('created_at', 'desc')->paginate(30);
            if ($Categories->total() === 0) {
                return $this->noContentResponse();
            }
            return $this->paginationResponse($Categories, 200);
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }



    public function publicCategories()
    {
        try {
            $categories = ArticleCategory::orderByDesc('created_at')->limit(8)->get();

            if ($categories->count() === 0) {
                return $this->noContentResponse();
            }

            return $this->successResponse($categories, 200);
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to fetch categories.', [
                'error' => $e->getMessage()
            ], 500);
        }
    }



    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreCategoryRequest $request)
    {
        try {
            $data = $request->validated();
            $category = new ArticleCategory();
            $category->fill($data);
            if ($request->has('image')) {
                $this->imageservice->ImageUploaderwithvariable($request, $category, 'images/articleCategories', 'image');
            }
            return $this->successResponse($category, 201);
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $category = ArticleCategory::findOrFail($id);
        return $this->successResponse($category, 200);
        try {
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }



    /**
     * Update the specified resource in storage.
     */
    public function update($id, UpdateCategoryRequest $request)
    {
        try {
            $category = ArticleCategory::findOrFail($id);
            $data = $request->validated();
            $category->update($data);
            if ($request->has('image')) {
                $this->imageservice->ImageUploaderwithvariable($request, $category, 'images/articleCategories');
            }
            return $this->successResponse($category->fresh(), 200);
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        try {
            $articleCategory = ArticleCategory::findOrFail($id);

            if ($articleCategory->image) {
                $this->imageservice->deleteOldImage($articleCategory, 'images/articleCategories');
            }

            $articleCategory->delete();

            return $this->successResponse(['name' => $articleCategory->title_en], 200);
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }
}
