<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\Admin\DashboardResource;
use App\Http\Resources\Admin\NotificationsResource;
use App\Http\Resources\Admin\ProductIndexResource;
use App\Models\Code;
use App\Models\Order;
use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\User;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
  public function index()
  {
    $products = Product::get();
    $orders = Order::get();
    $users = User::count();
    $codes = Code::count();
    $code_expired = Code::where('expired_at', '<', now())->count();
    $categories = ProductCategory::count();
    $admins = User::whereIn('role', ['admin', 'super_admin'])->count();
    $users_ban = User::where('status', false)->count();
    $topProducts = ProductIndexResource::collection(Product::orderBy('sales_count', 'desc')->take(6)->get());
    $dashboard = new DashboardResource(['products' => $products, 'orders' => $orders]);


    return response()->json(['message' => 'Index Dashboard', 'dashboard' => $dashboard, 'users' => $users, 'topProducts' => $topProducts, 'codes' => $codes, 'users_ban' => $users_ban, 'categories' => $categories, 'admins' => $admins, 'code_expired' => $code_expired]);
  }

  public function admin(Request $request)
  {
    $user = $request->user();
    $notifications = NotificationsResource::collection($user->notifications);

    if ($user->status == false)
    {
      return response()->json(['errors' => "Your account is not active. Please contact the administrator."], 404);
    }

    return response()->json(['message' => 'User profile successfully', 'user' => $user, 'notifications' => $notifications]);
  }
}
