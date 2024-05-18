<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\UserUpdateRequest;
use App\Http\Resources\Admin\UserOrderResource;
use App\Http\Resources\Admin\UserResource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
  /**
   * Display a listing of the resource.
   */
  public function index(Request $request)
  {
    $query = User::query();
    $searchQuery = $request->input('search');

    if (!empty($searchQuery))
    {
      $query->where(function ($query) use ($searchQuery)
      {
        $query->where('name', 'like', '%' . $searchQuery . '%')
          ->orWhere('email', 'like', '%' . $searchQuery . '%');
      });
    }

    $users = $query->paginate(10);
    $usersResource = UserResource::collection($users);

    return response()->json([
      'message' => 'Users Geted Successfully',
      'users' => $usersResource,
      'current_page' => $users->currentPage(),
      'last_page' => $users->lastPage(),
    ]);
  }

  /**
   * Display the specified resource.
   */
  public function show(string $id)
  {
    $user = User::find($id);

    if (!$user)
    {
      return response()->json(['message' => 'User not found'], 404);
    }

    $user = new UserResource($user);
    $orders = UserOrderResource::collection($user->orders()->get());

    return response()->json(['message' => 'User geted successfully', 'orders' => $orders, 'user' => $user]);
  }

  /**
   * Update the specified resource in storage.
   */
  public function update(UserUpdateRequest $request, string $id)
  {
    $data = $request->validated();

    $user = User::find($id);

    if (!$user)
    {
      return response()->json(['message' => 'User not found'], 404);
    }

    if (isset($data['amount']))
    {
      $data['balance'] = $user->balance + $data['amount'];
      unset($data['amount']);
    }

    $user->update($data);

    return response()->json(['message' => 'User Updated successfully']);
  }

  /**
   * Remove the specified resource from storage.
   */
  public function destroy(string $id)
  {
    //
  }
}
