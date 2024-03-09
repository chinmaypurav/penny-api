<?php

namespace App\Http\Controllers;

use App\Http\Resources\UserResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserController extends Controller
{
    public function __invoke(Request $request): JsonResource
    {
        return UserResource::make($request->user());
    }
}
