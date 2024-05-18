<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\WishlistRequest;
use App\Http\Resources\WishlistResource;
use App\Models\Wishlist;
use Illuminate\Http\Request;

class WishlistController extends Controller
{
  public function index()
  {
    $wishlists = auth()->user()->wishlists()->get();

    if (!$wishlists->count()) {
      return response()->json(['message' => 'No wishlists found for the current user']);
    }

    $wishlistsResource = WishlistResource::collection($wishlists);

    return response()->json([
      'message' => 'Wishlists geted successfully',
      'wishlists' => $wishlistsResource
    ]);
  }

  /**
   * Store a newly created resource in storage.
   */
  public function store(WishlistRequest $request)
  {
    $data = $request->validated();

    $existingItem = Wishlist::where('product_id', $data['product_id'])
      ->where('user_id', $data['user_id'])
      ->first();

    if ($existingItem) {
      return response()->json(['message' => 'Item has been already to the wishlist.'], 200);
    } else {
      Wishlist::create($data);
      return response()->json(['message' => 'Item has been added to the wishlist.'], 201);
    }
  }

  public function destroy(string $id)
  {
    $user = auth()->user();

    $wishlist = $user->wishlists()->where('product_id', $id)->first();

    if (!$wishlist) {
      return response()->json(['message' => 'Product not found for the current user'], 404);
    }

    $wishlist->delete();
    return response()->json(['message' => 'Product removed from wishlist successfully'], 200);
  }
}
