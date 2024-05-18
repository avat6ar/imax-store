<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\Admin\CodeResource;
use App\Models\Code;
use App\Models\User;
use Illuminate\Http\Request;

class CodeController extends Controller
{
  public function index(Request $request)
  {
    $query = Code::query();
    $searchQuery = $request->input('search');
    if ($searchQuery)
    {
      $query->where('code', 'LIKE', "%{$searchQuery}%");
    }
    $codes = $query->paginate(10);
    $codesResorce = CodeResource::collection($codes);

    return response()->json([
      'message' => 'Codes retrieved successfully', 'codes' => $codesResorce, 'current_page' => $codes->currentPage(),
      'last_page' => $codes->lastPage(),
    ]);
  }

  public function store(Request $request)
  {
    $count = $request->count;
    $amount = $request->amount;
    Code::createCodes($count, $amount);
    return response()->json(['message' => 'Codes created successfully']);
  }

  public function redeem(Request $request)
  {
    $code = Code::checkCode($request->code);
    if (!$code)
    {
      return response()->json(['message' => 'Invalid or used code'], 404);
    }
    $user = User::where('id', $request->user()->id)->first();
    $code->markAsUsed($user->email);
    $user->balance += $code->amount;
    $user->save();

    return response()->json(['message' => 'Code redeemed successfully', 'user' => $user]);
  }

  public function destroy($id)
  {
    $code = Code::find($id);
    if ($code)
    {
      $code->delete();
      return response()->json(['message' => 'Code deleted successfully']);
    }
    else
    {
      return response()->json(['message' => 'Code not found'], 404);
    }
  }
}
