<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCategoryRequest;
use App\Http\Requests\UpdateCategoryRequest;
use App\Http\Resources\CategoryCollection;
use App\Http\Resources\CategoryResource;
use App\Models\Category;
use App\Models\User;
use App\Services\CategoryService;

class CategoryController extends Controller
{
    protected User $user;

    public function __construct(protected CategoryService $categoryService)
    {
        $this->authorizeResource(Category::class);
        $this->middleware(function ($request, $next) {
            $this->user = auth()->user();

            return $next($request);
        });
    }

    public function index()
    {
        return CategoryCollection::make($this->categoryService->index($this->user));
    }

    public function store(StoreCategoryRequest $request)
    {
        return CategoryResource::make($this->categoryService->store($this->user, $request->validated()));
    }

    public function show(Category $category)
    {
        return CategoryResource::make($category);
    }

    public function update(UpdateCategoryRequest $request, Category $category)
    {
        return CategoryResource::make($this->categoryService->update($category, $request->validated()));
    }

    public function destroy(Category $category)
    {
        $this->categoryService->destroy($category);

        return response()->noContent();
    }
}
