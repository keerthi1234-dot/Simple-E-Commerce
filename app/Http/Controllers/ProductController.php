<?php

namespace App\Http\Controllers;

use App\Models\Product;

class ProductController extends Controller
{
    // Get all products
    public function index()
    {
        return response()->json([
            'products' => Product::all()
        ]);
    }
}
