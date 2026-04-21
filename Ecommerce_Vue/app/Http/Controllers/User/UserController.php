<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

class UserController extends Controller
{
   public function index()
   {


      $products = Product::with(['brand', 'category', 'product_images'])
      ->where('published', 1)       
      ->latest()                 
      ->paginate(8)               
      ->withQueryString();       
         
      return Inertia::render('User/Index', [ 
         'canLogin' => Route::has('login'),  
         'canRegister' => Route::has('register'),
         'laravelVersion' => Application::VERSION,
         'phpVersion' => PHP_VERSION,
         'products' => $products
      ]);
   }
}
