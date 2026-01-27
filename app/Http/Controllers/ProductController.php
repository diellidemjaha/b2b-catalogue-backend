<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index()
    {
        return Product::where('is_active', true)->get();
    }

    public function show($id)
    {
        return Product::findOrFail($id);
    }
}
