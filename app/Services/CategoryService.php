<?php

namespace App\Services;

use App\Models\Category;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class CategoryService
{
    public function index(User $user): Collection
    {
        return $user->categories()->get();
    }

    public function store(User $user, array $input): Category
    {
        return DB::transaction(
            fn () => $user->categories()->create($input)
        );
    }

    public function update(Category $category, array $input): Category
    {
        return DB::transaction(
            fn () => tap($category, fn (Category $category) => $category->update($input))
        );
    }

    public function destroy(Category $category): Category
    {
        return DB::transaction(
            fn () => tap($category, fn (Category $category) => $category->delete())
        );
    }
}
