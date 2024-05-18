<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ReviewRequest;
use App\Http\Resources\CategoryResource;
use App\Http\Resources\ProductIndexResource;
use App\Http\Resources\ProductResource;
use App\Http\Resources\ReviewResource;
use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\ProductReview;
use App\Models\User;
use App\Notifications\NewReviewNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Notification;

class PublicController extends Controller
{
  public function categories()
  {
    $categories = ProductCategory::all();
    $categoriesResource = CategoryResource::collection($categories);

    return response()->json(['message' => 'Categories retrieved successfully', 'categories' => $categoriesResource]);
  }

  public function feedback()
  {
    $feedback = ProductReview::where('publish', true)->get();
    $feedbackResource = ReviewResource::collection($feedback);

    return response()->json(['message' => 'Feedback retrieved successfully', 'feedback' => $feedbackResource]);
  }

  public function newProducts()
  {
    $products = Product::orderBy('created_at', 'desc')->get();
    $filteredProducts = $products->filter(function ($product)
    {
      if ($product->type == 'automatic')
      {
        return $product->codes()->where('status', 'available')->exists();
      }
      return true;
    })->take(6);

    $productsResource = ProductIndexResource::collection($filteredProducts);

    return response()->json(['message' => 'New Products retrieved successfully', 'products' => $productsResource]);
  }

  public function featuredProducts()
  {
    $products = Product::orderBy('seen', 'desc')->get();
    $filteredProducts = $products->filter(function ($product)
    {
      if ($product->type == 'automatic')
      {
        return $product->codes()->where('status', 'available')->exists();
      }
      return true;
    })->take(6);

    $productsResource = ProductIndexResource::collection($filteredProducts);

    return response()->json(['message' => 'Featured Products retrieved successfully', 'products' => $productsResource]);
  }

  public function bestProducts()
  {
    $products = Product::orderBy('sales_count', 'desc')->get();
    $filteredProducts = $products->filter(function ($product)
    {
      if ($product->type == 'automatic')
      {
        return $product->codes()->where('status', 'available')->exists();
      }
      return true;
    })->take(6);

    $productsResource = ProductIndexResource::collection($filteredProducts);

    return response()->json(['message' => 'Best Products retrieved successfully', 'products' => $productsResource]);
  }

  public function otherProducts()
  {
    $products = Product::inRandomOrder()->get();
    $filteredProducts = $products->filter(function ($product)
    {
      if ($product->type == 'automatic')
      {
        return $product->codes()->where('status', 'available')->exists();
      }
      return true;
    })->take(6);

    $productsResource = ProductIndexResource::collection($filteredProducts);

    return response()->json(['message' => 'Other Products retrieved successfully', 'products' => $productsResource]);
  }

  public function products(Request $request)
  {
    $selectedCategories = (array) $request->input('categories', []);
    $selectedTypes = (array) $request->input('types', []);
    $minPrice = $request->input('price_min', 0);
    $maxPrice = $request->input('price_max', 500);
    $searchQuery = $request->input('search');
    $language = $request->headers->get('Language');
    $currency = $request->headers->get('Currency');

    $query = Product::query();

    if (!empty($selectedCategories) && is_array($selectedCategories))
    {
      $query->whereIn('category_id', $selectedCategories);
    }

    if (!empty($selectedTypes) && is_array($selectedTypes))
    {
      $query->whereIn('type_en', $selectedTypes);
    }

    if (!empty($searchQuery))
    {
      $query->where(function ($query) use ($searchQuery, $language)
      {
        if ($language == "ar")
        {
          $query->where('title_ar', 'like', '%' . $searchQuery . '%')
            ->orWhere('description_ar', 'like', '%' . $searchQuery . '%');
        }
        elseif ($language == "fr")
        {
          $query->where('title_fr', 'like', '%' . $searchQuery . '%')
            ->orWhere('description_fr', 'like', '%' . $searchQuery . '%');
        }
        else
        {
          $query->where('title_en', 'like', '%' . $searchQuery . '%')
            ->orWhere('description_en', 'like', '%' . $searchQuery . '%');
        }
      });
    }

    if ($minPrice != 0 || $maxPrice != 500)
    {
      $query->whereHas('prices', function ($query) use ($minPrice, $maxPrice, $currency)
      {
        $query->whereBetween($currency, [$minPrice, $maxPrice]);
      });
    }

    $products = $query->paginate(9);

    $productsResource = ProductIndexResource::collection($products);

    return response()->json([
      'message' => 'Products retrieved successfully',
      'products' => $productsResource,
      'current_page' => $products->currentPage(),
      'last_page' => $products->lastPage(),
    ]);
  }

  public function productDetails(string $slug)
  {
    $product = Product::where('slug', $slug)->first();

    if (!$product)
    {
      return response()->json(['message' => 'Product not found'], 404);
    }
    $product->update(['seen' => $product->seen + 1]);
    $productResource = new ProductResource($product);

    $availability = $product->type == "automatic" ? ($product->codes()->where('status', 'available')->exists() ? 'available' : 'out of stock') : "available";

    return response()->json(['message' => 'Product details retrieved successfully', 'product' => $productResource, 'availability' => $availability]);
  }

  public function storeReview(ReviewRequest $request)
  {
    $data = $request->validated();
    $user = User::find($data['user_id']);
    $admins = User::whereIn('role', ['admin', 'super_admin'])->get();
    $product = Product::find($data['product_id']);

    ProductReview::create($data);
    Notification::send($admins, new NewReviewNotification($product, $data['review'], $user));
    return response()->json(['message' => 'Review added successfully']);
  }
}
