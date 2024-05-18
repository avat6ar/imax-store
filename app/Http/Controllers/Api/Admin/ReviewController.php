<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\ReviewResource;
use App\Models\ProductReview;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ReviewController extends Controller
{
  /**
   * Display a listing of the resource.
   */
  public function index()
  {
    $reviews = ProductReview::where('publish', true)->get();
    $reviewsResource = ReviewResource::collection($reviews);
    $averageRating = 0;
    $totalRating = $reviews->sum('rate');
    $numberOfReviews = $reviews->count();

    if ($numberOfReviews > 0) {
      $averageRating = round($totalRating / $numberOfReviews, 1);
    }
    return response()->json(['message' => 'Reviews successfully', 'reviews' => $reviewsResource, 'averageRating' => $averageRating]);
  }

  /**
   * Store a newly created resource in storage.
   */
  public function togglePublish(string $id)
  {
    $review = ProductReview::find($id);

    if (!$review) {
      return response()->json(['message' => 'Product review not found'], 404);
    }

    $newPublishState = !$review->publish;

    $review->update(['publish' => $newPublishState]);

    if ($newPublishState) {
      return response()->json(['message' => 'Product review published successfully'], 200);
    } else {
      return response()->json(['message' => 'Product review unpublished successfully'], 200);
    }
  }

  /**
   * Remove the specified resource from storage.
   */
  public function destroy(string $id)
  {
    $review = ProductReview::find($id);

    if (!$review) {
      return response()->json(['message' => 'Product review not found'], 404);
    }
    $review->delete();

    return response()->json(['message' => 'Product review deleted']);
  }
}
