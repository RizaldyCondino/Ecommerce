<?php

namespace App\Http\Controllers\User;

use App\Helper\Cart;
use App\Http\Controllers\Controller;
use App\Models\CartItem;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Payment;
use App\Models\UserAddress;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class CheckoutController extends Controller
{
    function store(Request $request)
    {

        // dd($request);
        $user = $request->user();
        $carts = $request->carts;
        $products = $request->products;
        $grandTotal = $request->input('total');
        $mergedData = [];

        foreach ($carts as $cartItem) {
            foreach ($products as $product) {
                if ($cartItem["product_id"] == $product["id"]) {
                    $mergedData[] = array_merge($cartItem, ['title' => $product['title'], 'price' => $product['price']]);
                }
            }
        }

        $stripe = new \Stripe\StripeClient(config('services.stripe.secret'));
        $lineItems = [];
        foreach ($mergedData as $item) {

            $lineItems[] =
                [
                    'price_data' => [
                        'currency' => 'usd',
                        'product_data' => [
                            'name' => $item['title'],
                        ],
                        'unit_amount' => (int)($item['price'] * 100),

                    ],
                    'quantity' => $item['quantity'],
                ];
        }
        if ($grandTotal > array_sum(array_map(fn($i) => $i['price'] * $i['quantity'], $mergedData))) {
            $extraAmount = $grandTotal - array_sum(array_map(fn($i) => $i['price'] * $i['quantity'], $mergedData));
            $lineItems[] = [
                'price_data' => [
                    'currency' => 'usd',
                    'product_data' => [
                        'name' => 'Shipping & Tax',
                    ],
                    'unit_amount' => (int)($extraAmount * 100), // in cents
                ],
                'quantity' => 1,
            ];
        }

        $checkout_session = $stripe->checkout->sessions->create([
            'line_items' => $lineItems,
            'mode' => 'payment',
            'success_url' => route('checkout.success') . '?session_id={CHECKOUT_SESSION_ID}',
            'cancel_url' => route('user.home'),
        ]);

        $user = Auth::user();

        $mainAddress = $user->addresses()->where('isMain', true)->first();
        if ($mainAddress) {
            $order = new Order();
            $order->status = 'unpaid';
            $order->total_price = $request->total;
            $order->session_id = $checkout_session->id;
            $order->created_by = $user->id;
            $order->user_address_id = $mainAddress->id;
            $order->save();
            $cartItems = CartItem::where('user_id', $user->id)->get();

            foreach ($cartItems as $cartItem) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $cartItem->product_id,
                    'quantity' => $cartItem->quantity,
                    'unit_price' => $cartItem->product->price,
                ]);
            }

            // delete all DB cart items at once
            CartItem::where('user_id', $user->id)->delete();

            // clear cookie cart once
            $cookieCartItems = Cart::getCookieCartItems();
            $cookieCartItems = []; // reset fully
            Cart::setCookieCartItems($cookieCartItems);

            // $cartItems = CartItem::where(['user_id' => $user->id])->get();
            // foreach ($cartItems as $cartItem) {
            //     OrderItem::create([
            //         'order_id' => $order->id,
            //         'product_id' => $cartItem->product_id,
            //         'quantity' => $cartItem->quantity,
            //         'unit_price' => $cartItem->product->price,
            //     ]);
            //     $cartItem->delete();
            //     $cartItems = Cart::getCookieCartItems();
            //     foreach ($cartItems as $item) {
            //         unset($item);
            //     }
            //     array_splice($cartItems, 0, count($cartItems));
            //     Cart::setCookieCartItems($cartItems);
            // }
            $paymentData = [
                'order_id' => $order->id,
                'amount' => $request->total,
                'status' => 'pending',
                'type' => 'stripe',
                'created_by' => $user->id,
                'updated_by' => $user->id,
            ];

            Payment::create($paymentData);

            return Inertia::location($checkout_session->url);
        }
    }

    public function success(Request $request)
    {
        \Stripe\Stripe::setApiKey(env('STRIPE_SECRET'));
        $sessionId = $request->get('session_id');
        try {

            $session = \Stripe\Checkout\Session::retrieve($sessionId);
            if (!$session) {
                throw new NotFoundHttpException;
            }
            $order = Order::where('session_id', $session->id)->first();
            if (!$order) {
                throw new NotFoundHttpException();
            }

            if ($order->status === 'unpaid') {
                $order->status = 'paid';
                $order->save();
            }

            return redirect()->route('orders.dashboard.index');
        } catch (\Exception $e) {
            throw new NotFoundHttpException();
        }
    }

    function storeAddress(Request $request)
    {
        $user = Auth::user();

        if ($user->addresses()->where('isMain', true)->exists()) {
            return redirect()->route('cart.view')->with('error', 'You already have a address.');
        }

        $validated = $request->validate([
            'address1'     => 'required|string|max:255',
            'address2'     => 'nullable|string|max:255',
            'city'         => 'required|string|max:100',
            'state'        => 'required|string|max:100',
            'zipcode'      => 'required|string|max:20',
            'country_code' => 'required|string|max:10',
            'type'         => 'required|string|max:50',
        ]);

        $user->addresses()->create($validated);
        return redirect()->route('cart.view')
            ->with('success', 'Address added successfully.');
    }

    public function updateIsmain($id)
    {
        $address = UserAddress::findOrFail($id);

        $address->isMain = 0;
        $address->save();

        return redirect()->route('cart.view')
            ->with('success', 'Address updated successfully.');
    }

    /// Update pay
    public function pay(Request $request, $orderId)
    {
        $user = $request->user();

        $order = Order::with('order_items.product')
            ->where('id', $orderId)
            ->where('status', 'unpaid')
            ->where('created_by', $user->id)
            ->firstOrFail();

        $lineItems = $order->order_items->map(function ($item) {
            $product = $item->product;
            if (!$product) return null;

            return [
                'price_data' => [
                    'currency' => 'usd',
                    'product_data' => [
                        'name' => $product->title,
                    ],
                    'unit_amount' => (int) round($product->price * 100),
                ],
                'quantity' => $item->quantity,
            ];
        })->filter()->values()->toArray();

        $stripe = new \Stripe\StripeClient(config('services.stripe.secret'));


        $session = $stripe->checkout->sessions->create([
            'line_items' => $lineItems,
            'mode' => 'payment',
            'success_url' => route('checkout.success') . '?session_id={CHECKOUT_SESSION_ID}',
            'cancel_url' => route('user.home'),
            'metadata' => [
                'order_id' => $order->id,
            ],
        ]);

        $order->session_id = $session->id;
        $order->save();

        return Inertia::location($session->url);
    }
}
