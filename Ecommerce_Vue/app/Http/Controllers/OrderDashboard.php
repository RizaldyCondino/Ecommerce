<?php

namespace App\Http\Controllers;

use App\Models\CartItem;
use App\Models\Order;
use App\Models\UserAddress;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class OrderDashboard extends Controller
{
    public function index()
    {
        
        $orders = Order::with('order_items.product.brand', 'order_items.product.category')
            ->where('created_by', Auth::id())
            ->latest()
            ->paginate(6); 

        return Inertia::render('User/OrderDashboard', [
            'orders' => $orders
        ]);
    }
}
