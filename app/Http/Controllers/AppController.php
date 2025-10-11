<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Models\Product;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;

class AppController extends Controller
{
    public function welcome()
    {
        $products = Product::with(['brand', 'images'])->orderBy('name')->get();

        return view('welcome', compact('products'));
    }
    public function user()
    {
        $roles = Role::all();
        return view('users/index', compact('roles'));
    }
    public function product()
    {
        $brands = Brand::all();
        return view('products/index', compact('brands'));
    }
    public function brand()
    {
        return view('brands/index');
    }
    public function movement()
    {
        $products = Product::all();
        return view('movement/index', compact('products'));
    }
    public function chart()
    {
        return view('chart/index');
    }
}
