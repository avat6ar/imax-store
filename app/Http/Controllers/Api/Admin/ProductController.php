<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProductRequest;
use App\Http\Resources\Admin\ProductIndexResource;
use App\Http\Resources\Admin\ProductViewResource;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\User;
use App\Notifications\{CreateProductNotification, DeleteProductNotification, UpdateProductNotification};
use App\Services\SaveImage;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Notification;

class ProductController extends Controller
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

  /**
   * Display a listing of the resource.
   */
  public function index(Request $request)
  {
    $query = Product::query();
    $searchQuery = $request->input('search');
    $language = $request->headers->get('Language');

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

    $products = $query->paginate(10);
    $productsResource = ProductIndexResource::collection($products);

    return response()->json([
      'message' => 'Products Geted Successfully',
      'products' => $productsResource,
      'current_page' => $products->currentPage(),
      'last_page' => $products->lastPage(),
    ]);
  }

  /**
   * Display the specified resource.
   */
  public function show(string $id)
  {
    $product = Product::find($id);
    if (!$product)
    {
      return response()->json(['message' => 'Product not found'], 404);
    }

    $data = new ProductViewResource($product);

    return response()->json(['message' => 'Product geted successfully', 'data' => $data]);
  }

  public function showDetails(string $slug)
  {
    $product = Product::where('slug', $slug)->first();

    if (!$product)
    {
      return response()->json(['message' => 'Product not found'], 404);
    }

    $data = new ProductViewResource($product);

    return response()->json(['message' => 'Product geted successfully', 'product' => $data]);
  }

  /**
   * Store a newly created resource in storage.
   */
  public function store(ProductRequest $request)
  {
    $data = $request->validated();

    $typeTranslations = [
      'automatic' => [
        'en' => 'automatic',
        'fr' => 'automatique',
        'ar' => 'تلقائي',
      ],
      'manual' => [
        'en' => 'manual',
        'fr' => 'manuel',
        'ar' => 'يدوي',
      ],
    ];

    $data['type_en'] = $data['type'];
    $data['type_fr'] = $typeTranslations[$data['type']]['fr'];
    $data['type_ar'] = $typeTranslations[$data['type']]['ar'];

    $product = Product::create($data);

    foreach (['seo', 'codes', 'inputs', 'questions', 'prices'] as $relation)
    {
      $this->updateOrCreateRelationships($product, $relation, $data);
    }

    $this->updateOrCreateImages($product, $data['images']);

    Notification::send($this->admins, new CreateProductNotification($product, $this->user));
    $product->load(['seo', 'codes', 'inputs', 'questions', 'images', 'prices']);


    return response()->json(['message' => 'Product created successfully']);
  }

  /**
   * Update the specified resource in storage.
   */
  public function update(ProductRequest $request, string $id)
  {
    $product = Product::find($id);

    if (!$product)
    {
      return response()->json(['message' => 'Product not found' . $id, $product], 404);
    }
    $data = $request->validated();

    $typeTranslations = [
      'automatic' => [
        'en' => 'automatic',
        'fr' => 'automatique',
        'ar' => 'تلقائي',
      ],
      'manual' => [
        'en' => 'manual',
        'fr' => 'manuel',
        'ar' => 'يدوي',
      ],
    ];

    $data['type_en'] = $data['type'];
    $data['type_fr'] = $typeTranslations[$data['type']]['fr'];
    $data['type_ar'] = $typeTranslations[$data['type']]['ar'];

    unset($data['type']);

    $product->update($data);

    foreach (['seo', 'codes', 'inputs', 'questions', 'prices'] as $relation)
    {
      $this->updateOrCreateRelationships($product, $relation, $data);
    }

    $this->updateOrCreateImages($product, $data['images']);

    $product->load(['seo', 'codes', 'inputs', 'questions', 'images', 'prices']);

    Notification::send($this->admins, new UpdateProductNotification($product, $this->user));

    return response()->json(['message' => 'Product updated successfully']);
  }

  private function updateOrCreateRelationships(Product $product, string $relation, array $validatedData)
  {
    $existingIds = $product->$relation()->pluck('id')->toArray();

    if ($relation == "prices" || $relation == "seo")
    {
      $model = $product->$relation()->find($validatedData[$relation]['id'] ?? null);
      if ($model)
      {
        $model->update($validatedData[$relation]);
        // Remove the id from existingIds array to avoid deletion
        $key = array_search($model->id, $existingIds);
        if ($key !== false)
        {
          unset($existingIds[$key]);
        }
      }
      else
      {
        $product->$relation()->create($validatedData[$relation]);
      }
    }

    foreach ($validatedData[$relation] as $data)
    {
      $model = $product->$relation()->find($data['id'] ?? null);
      if (isset($data['expire_date']))
      {
        $expireDate = date('Y-m-d H:i:s', strtotime($data['expire_date']));
        $data['expire_date'] = $expireDate;
      }
      if (isset($data['data']))
      {
        $dataString = json_encode($data['data']);
        $data['data'] = $dataString;
      }
      if (is_array($data))
      {
        if ($model)
        {
          $model->update($data);
          // Remove the id from existingIds array to avoid deletion
          $key = array_search($model->id, $existingIds);
          if ($key !== false)
          {
            unset($existingIds[$key]);
          }
        }
        else
        {
          $product->$relation()->create($data);
        }
      }
    }

    // Delete remaining items that weren't found in the validated data
    $product->$relation()->whereIn('id', $existingIds)->delete();
  }

  private function updateOrCreateImages(Product $product, array $validatedData)
  {
    $existingImages = $product->images()->get();

    foreach ($existingImages as $existingImage)
    {
      $imageDataExists = false;

      foreach ($validatedData as $imageData)
      {
        if (isset($imageData['id']) && $imageData['id'] == $existingImage->id)
        {
          $imageDataExists = true;
          break;
        }
      }

      if (!$imageDataExists)
      {
        // Delete the image from storage and database
        $this->deleteImage($existingImage);
      }
    }

    // Now handle the validated data
    foreach ($validatedData as $imageData)
    {
      if (isset($imageData['id']))
      {
        // Update existing image
        $image = ProductImage::findOrFail($imageData['id']);
        $image->update(['image' => $imageData['image']]);
      }
      else
      {
        // Save new image and create a new record
        $imagePath = $this->saveImage->saveImage($imageData['image']);
        $product->images()->create(['image' => $imagePath]);
      }
    }
  }

  private function deleteImage($image)
  {
    $imagePath = public_path($image->getOriginal('image'));

    if (File::exists($imagePath))
    {
      File::delete($imagePath);
    }

    $image->delete();
  }

  /**
   * Remove the specified resource from storage.
   */
  public function destroy(string $id)
  {
    $product = Product::find($id);

    if (!$product)
    {
      return response()->json(['message' => 'Product not found'], 404);
    }

    Notification::send($this->admins, new DeleteProductNotification($product, $this->user));

    $product->delete();

    return response()->json(['message' => 'Product deleted successfully'], 200);
  }
}
