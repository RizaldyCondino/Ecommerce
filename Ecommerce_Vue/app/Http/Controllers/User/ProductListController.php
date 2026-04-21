<?php

namespace App\Http\Controllers\User;

use App\Helper\Cart;
use App\Http\Controllers\Controller;
use App\Models\Brand;
use App\Models\CartItem;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ProductListController extends Controller
{
    public function index()
    {

        $categories = Category::get();
        $brands = Brand::get();

        $products = Product::with(['brand', 'category', 'product_images'])
            ->where('published', 1) 
            ->filtered()
            ->latest()
            ->paginate(10)
            ->withQueryString()
            ->through(fn($product) => [
                'id' => $product->id,
                'title' => $product->title,
                'brand_id' => $product->brand?->id ?? null,
                'brand' => $product->brand?->name ?? null,
                'category_id' => $product->category?->id ?? null,
                'category' => $product->category?->name ?? null,
                'price' => $product->price,
                'quantity' => $product->quantity,
                'inStock' => $product->inStock,
                'published' => $product->published,
                'description' => $product->description,
                'product_images' => $product->product_images->map(fn($img) => [
                    'id' => $img->id,      // real database ID
                    'image' => $img->image // path
                ]),

            ]);
         
        return Inertia::render('User/ProductList', [
            'products' => $products,
            'brands' => $brands,
            'categories' => $categories
        ]);
    }

    public function view($id)
    {
        $product = Product::with(['brand', 'category', 'product_images'])
            ->findOrFail($id); // fetch the single product by ID

        // Transform the product for the frontend
        $productData = [
            'id' => $product->id,
            'title' => $product->title,
            'brand_id' => $product->brand?->id ?? null,
            'brand' => $product->brand?->name ?? null,
            'category_id' => $product->category?->id ?? null,
            'category' => $product->category?->name ?? null,
            'price' => $product->price,
            'quantity' => $product->quantity,
            'inStock' => $product->inStock,
            'published' => $product->published,
            'description' => $product->description,
            'product_images' => $product->product_images->map(fn($img) => [
                'id' => $img->id,
                'image' => $img->image
            ]),
        ];

        return Inertia::render('User/ProductsView', [
            'product' => $productData
        ]);
    }

    public function storeOrUpdate(Request $request, Product $product)
    {
        $quantity = $request->integer('quantity', 1); 
        $user = $request->user();

        if ($user) {
            $cartItem = CartItem::firstOrNew([
                'user_id' => $user->id,
                'product_id' => $product->id
            ]);
            $cartItem->quantity = $quantity;
            $cartItem->save();
        } else {
            $cartItems = Cart::getCookieCartItems();
            $found = false;

            foreach ($cartItems as &$item) {
                if ($item['product_id'] === $product->id) {
                    $item['quantity'] = $quantity; 
                    $found = true;
                    break;
                }
            }
            if (!$found) {
                $cartItems[] = [
                    'product_id' => $product->id,
                    'quantity' => $quantity,
                ];
            }

            Cart::setCookieCartItems($cartItems);
        }

        return back()->with('success', 'Cart updated!');
    }
}
