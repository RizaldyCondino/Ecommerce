<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductImages;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Inertia\Inertia;

class ProductController extends Controller
{
    public function index()
    {
        $brands = Brand::get();
        $categories = Category::get();
        $products = Product::with(['brand', 'category', 'product_images'])
            ->latest()
            ->filtered()
            ->paginate(6)
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

        return Inertia::render('Admin/Products/Index', [
            'products' => $products,
            'brands' => $brands,
            'categories' => $categories,
        ]);

    }
    public function store(Request $request)
    {

        // dd($request)->file('product_images');
        $validated = $request->validate([
            'title'       => 'required|string|max:200',
            'quantity'    => 'required|integer|min:0',
            'description' => 'nullable|string',
            'published'   => 'boolean',
            'inStock'     => 'boolean',
            'price'       => 'required|numeric|min:0',
            'brand_id'    => 'required|exists:brands,id',
            'category_id' => 'required|exists:categories,id',
        ]);

        $product = new Product();

        $product->title = $validated['title'];
        $product->slug = Str::slug($validated['title']); // auto-generate slug
        $product->quantity = $validated['quantity'];
        $product->description = $validated['description'] ?? null;
        $product->published = $validated['published'] ?? 1;
        $product->inStock = $validated['inStock'] ?? 0;
        $product->price = $validated['price'];
        $product->brand_id = $validated['brand_id'] ?? null;
        $product->category_id = $validated['category_id'] ?? null;

        $product->save();

        if ($request->hasFile('product_images')) {

            $productImages = $request->file('product_images');
            foreach ($productImages as $image) {
                $uniqueName = time() . '-' . Str::random(10) . '-' . $image->getClientOriginalExtension();
                $image->move('product_images', $uniqueName);

                ProductImages::create([
                    'product_id' => $product->id,
                    'image' => 'product_images/' . $uniqueName,
                ]);
            }
        }

        return redirect()->route('admin.products.index')
            ->with('success', 'Product added successfully.');
    }

    public function deleteImage($id)
    {
        $image = ProductImages::findOrFail($id);

        $filePath = public_path($image->image);
        if (File::exists($filePath)) {
            File::delete($filePath);
        }

        $image->delete();

        return redirect()->route('admin.products.index')
            ->with('success', 'Image deleted successfully');
    }

    public function update(Request $request, Product $product)
    {
        $request->validate([
            'title'       => 'required|string|max:200',
            'quantity'    => 'required|integer|min:0',
            'description' => 'nullable|string',
            'published'   => 'boolean',
            'inStock'     => 'boolean',
            'price'       => 'required|numeric|min:0',
            'brand_id'    => 'required|exists:brands,id',
            'category_id' => 'required|exists:categories,id',
        ]);

        $product->update([
            'title' => $request->title,
            'slug' => Str::slug($request->title),
            'quantity' => $request->quantity,
            'description' => $request->description,
            'published' => $request->published ?? 1,
            'inStock' => $request->inStock ?? 0,
            'price' => $request->price,
            'brand_id' => $request->brand_id ?? null,
            'category_id' => $request->category_id ?? null,
        ]);

        if ($request->hasFile('product_images')) {
            foreach ($request->file('product_images') as $image) {
                $uniqueName = time() . '-' . Str::random(10) . '.' . $image->getClientOriginalExtension();
                $image->move(public_path('product_images'), $uniqueName);
                ProductImages::create([
                    'product_id' => $product->id,
                    'image' => 'product_images/' . $uniqueName,
                ]);
            }
        }

        return redirect()->route('admin.products.index')
            ->with('success', 'Product updated successfully.');
    }

    public function updatePublished(Product $product)
    {
        $product->published = !$product->published;
        $product->save();

        return back()->with('success', 'Product status updated successfully!');
    }
}
