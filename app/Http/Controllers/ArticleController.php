<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreArticleRequest;
use App\Http\Requests\UpdateArticleRequest;
use App\Http\Services\ImageService;
use App\Http\Traits\ApiResponse;
use App\Models\Article;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;

class ArticleController extends Controller
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
            $articles = Article::orderBy('created_at', 'desc')
                ->with(['author:id,name,image', 'category', 'interactions:id,article_id,totalReactions'])
                ->withCount('comments')
                ->paginate(20);

            if ($articles->total() === 0) {
                return $this->noContentResponse();
            }

            return $this->paginationResponse($articles, 200);
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }


    public function topTenArticlesByViews()
    {
        try {
            $articles = Article::whereNotNull('views')
                ->orderByDesc('views')
                ->with(['author:id,name,image', 'category'])
                ->limit(10)
                ->get();

            if ($articles->isEmpty()) {
                return $this->noContentResponse();
            }

            return $this->successResponse($articles, 200);
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }


    public function getArticlesByStatus($status)
    {
        try {
            $articles = Article::where('status', $status)
                ->orderByDesc('created_at')
                ->with(['author:id,name,image', 'category'])
                ->paginate(20);

            if ($articles->total() === 0) {
                return $this->noContentResponse();
            }

            return $this->paginationResponse($articles, 200);
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }



    public function getPublishedArticlesBySearch(Request $request)
    {
        try {
            // التحقق من صحة الإدخال
            $validated = $request->validate([
                'search_content' => 'required|string|min:2'
            ]);

            // استخدام القيمة الصحيحة
            $contentSearch = '%' . $validated['search_content'] . '%';

            // البحث فقط في المقالات المنشورة
            $articles = Article::where('status', 'published')
                ->where(function ($query) use ($contentSearch) {
                    $query->where('title_en', 'like', $contentSearch)
                        ->orWhere('title_ar', 'like', $contentSearch)
                        ->orWhere('content_en', 'like', $contentSearch)
                        ->orWhere('content_ar', 'like', $contentSearch);
                })
                ->orderByDesc('views')
                ->with([
                    'author:id,name,image',
                    'category:id,title_en,title_ar',
                    'interactions:id,article_id,totalReactions'
                ])
                ->withCount('comments')
                ->paginate(20);

            if ($articles->total() == 0) {
                return $this->noContentResponse();
            }

            return $this->paginationResponse($articles, 200);
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }



    public function getArticlesBySearch(Request $request)
    {
        try {
            // التحقق من صحة الإدخال
            $validated = $request->validate([
                'search_content' => 'required|string|min:2'
            ]);

            // استخدام القيمة الصحيحة
            $contentSearch = '%' . $validated['search_content'] . '%';

            // البحث فقط في المقالات المنشورة
            $articles = Article::where('title_en', 'like', $contentSearch)
                ->orWhere('title_ar', 'like', $contentSearch)
                ->orWhere('content_en', 'like', $contentSearch)
                ->orWhere('content_ar', 'like', $contentSearch)
                ->orderByDesc('views')
                ->with([
                    'author:id,name,image',
                    'category:id,title_en,title_ar'
                ])
                ->paginate(20);

            if ($articles->total() == 0) {
                return $this->noContentResponse();
            }

            return $this->paginationResponse($articles, 200);
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreArticleRequest $request)
    {
        try {
            $data = $request->validated();
            $article = new Article();
            $article = Article::create(Arr::except($data, ['image']));
            if ($request->has('image')) {
                $this->imageservice->ImageUploaderwithvariable($request, $article, 'images/articles', 'image');
            }
            $article->refresh();
            return $this->successResponse($article, 201);
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $article = Article::with('category:id,title_en')->findOrFail($id);
        return $this->successResponse($article, 200);
        try {
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }



    /**
     * Update the specified resource in storage.
     */
    public function update($id, UpdateArticleRequest $request)
    {
        try {
            $data = $request->validated();
            $article = Article::findOrFail($id);
            $article->update(Arr::except($data, ['image']));
            if ($request->has('image')) {
                $this->imageservice->ImageUploaderwithvariable($request, $article, 'images/articles', 'image');
            }
            $article->refresh();
            return $this->successResponse($article, 200);
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
            $article = Article::findOrFail($id);
            if ($article->image) {
                $this->imageservice->deleteOldImage($article, 'images/articles');
            }
            $article->delete();
            return $this->successResponse(['message' => 'تم حذف المقال بنجاح', 'title' => $article->title_en], 200);
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }
}
