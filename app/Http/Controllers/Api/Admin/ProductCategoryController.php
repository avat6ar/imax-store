<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProductCategoryRequest;
use App\Http\Resources\Admin\ProductIndexResource;
use App\Http\Resources\CategoryResource;
use App\Models\ProductCategory;
use App\Models\User;
use App\Notifications\{CreateCategoryNotification, DeleteCategoryNotification, UpdateCategoryNotification};
use App\Services\SaveImage;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Notification;

class ProductCategoryController extends Controller
{
  private User $user;

  private Collection $admins;

  public function __construct(protected SaveImage $saveImage)
  {
    $this->middleware(function ($request, $next)
    {
      $this->user = User::find(auth()->user()->id);
      $this->admins = User::whereIn('role', ['admin', 'super_admin'])->whereNotIn('id', [$this->user->id])->get();
      return $next($request);
    });
  }

  public function index(): JsonResponse
  {
    return response()->json([
      'message' => 'Categories retrieved successfully',
      'categories' => CategoryResource::collection(ProductCategory::all()),
    ]);
  }

  public function store(ProductCategoryRequest $request): JsonResponse
  {
    $data = $request->validated();
    $data['image'] = $this->saveImage->saveImage($data['image']);
    $category = ProductCategory::create($data);
    Notification::send($this->admins, new CreateCategoryNotification($category, $this->user));

    return response()->json(['message' => 'Product category has been created successfully']);
  }

  public function show(string $id): JsonResponse
  {
    $category = ProductCategory::find($id);
    if (!$category)
    {
      return response()->json(['message' => 'Category not found'], 404);
    }
    $products = $category->products()->get();
    $productsResource = ProductIndexResource::collection($products);

    return response()->json([
      'message' => 'Category and associated products retrieved successfully',
      'category' => new CategoryResource($category),
      'products' => $productsResource
    ]);
  }

  public function update(ProductCategoryRequest $request, string $id): JsonResponse
  {
    $data = $request->validated();
    $category = ProductCategory::find($id);
    if (!$category)
    {
      return response()->json(['message' => 'Category not found'], 404);
    }

    $data['image'] = $this->saveImage->saveImage($data['image']);
    if ($category->image !== $data['image'])
    {
      File::delete(public_path($category->image));
    }

    $category->update($data);
    Notification::send($this->admins, new UpdateCategoryNotification($category, $this->user));

    return response()->json(['message' => 'Category updated successfully']);
  }

  public function destroy(string $id): JsonResponse
  {
    $category = ProductCategory::find($id);
    if (!$category)
    {
      return response()->json(['message' => 'Category not found'], 404);
    }


    Notification::send($this->admins, new DeleteCategoryNotification($category, $this->user));
    $category->delete();

    return response()->json(['message' => 'Category deleted successfully']);
  }
}
