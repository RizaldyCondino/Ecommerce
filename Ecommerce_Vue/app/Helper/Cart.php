<?php

namespace App\Helper;

use App\Models\CartItem;
use App\Models\Product;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Cookie;

class Cart
{
    public static function getCount()
    {
        if ($user = auth()->user()) {
            return CartItem::whereUserId($user->id)->count();
        } 
        else
        {
            return array_reduce(self::getCookieCartItems(), fn($carry) => $carry + 1, 0);
        }

        
    }

    public static function getCartItems()
    {
        if ($user = auth()->user()) {
            return CartItem::whereUserId($user->id)->get()->map(fn(CartItem $item) => ['product_id' => $item->product_id, 'quantity' => $item->quantity]);
        }
        else{
            return self::getCookieCartItems();
        }
    }

    public static function getCookieCartItems()
    {
        return json_decode(request()->cookie('cart_items', '[]'), true);
    }




    public static function setCookieCartItems(array $cartItems)
    {
        $value = json_encode($cartItems);
        Cookie::queue('cart_items', $value, 60 * 24 * 30); // 30 days

    }


    public static function saveCookieCartItems()
    {
        $user = auth()->user();
        $userCartItems = CartItem::where(['user_id' => $user->id])->get()->keyBy('product_id');
        $saveCartItems = [];
        foreach (self::getCookieCartItems() as $cartItem) {
            if (isset($userCartItems[$cartItem['product_id']])) {
                $userCartItems[$cartItem['product_id']]->update(['quantity' => $cartItem['quantity']]);
                continue;
            }

            $saveCartItems[] = [
                'user_id' => $user->id,
                'product_id' => $cartItem['product_id'],
                'quantity' => $cartItem['quantity'],
            ];
        }
        if (!empty($saveCartItems)) {
            CartItem::insert($saveCartItems);
        }
    }

    public static function moveCartItemsIntoDb()
    {

        // check if the record already exist in the database
        $request = request();
        $cartItems = self::getCookieCartItems();
        $newCartItems = [];
        foreach ($cartItems as $cartItem) {
            $existingCartItem = CartItem::where([
                'user_id' => $request->user()->id,
                'product_id' => $cartItem['product_id'],

            ])->first();

            if (!$existingCartItem) {

                // Only insert if it doesn't already exist
                $newCartItems[] = [
                    'user_id' => $request->user()->id,
                    'product_id' => $cartItem['product_id'],
                    'quantity' => $cartItem['quantity'],
                ];
            }
        }

        if (!empty($newCartItems)) {
            CartItem::insert($newCartItems);
        }
    }

    public static function getProductsAndCartItems()
    {
        $cartItems = auth()->check()
            ? self::getCartItems()
            : self::getCookieCartItems();

        if (empty($cartItems)) {
            return [
                'products' => collect(),
                'cartItems' => []
            ];
        }

        $ids = Arr::pluck($cartItems, 'product_id');

        $products = Product::whereIn('id', $ids)
            ->with('product_images')
            ->get();

        $cartItems = Arr::keyBy($cartItems, 'product_id');

        return [
            'products' => $products,
            'cartItems' => $cartItems
        ];
    }

    
}
