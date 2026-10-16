<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class StorefrontController extends Controller
{
    private const CATEGORIES = ['Clothing', 'Shoes', 'Bags', 'Home', 'Tech', 'Accessories'];

    public function home()
    {
        $products = Product::where('is_active', true)->latest()->take(4)->get();

        return view('welcome', [
            'categories' => self::CATEGORIES,
            'products' => $products,
        ]);
    }

    public function shop(Request $request)
    {
        $products = Product::where('is_active', true)
            ->when($request->filled('category'), function ($query) use ($request) {
                $query->where('category', (string) $request->query('category'));
            })
            ->when($request->filled('q'), function ($query) use ($request) {
                $term = (string) $request->query('q');
                $query->where(function ($search) use ($term) {
                    $search->where('name', 'like', "%{$term}%")
                        ->orWhere('description', 'like', "%{$term}%");
                });
            })
            ->orderBy('name')
            ->paginate(12)
            ->withQueryString();

        return view('shop', [
            'categories' => self::CATEGORIES,
            'products' => $products,
            'selectedCategory' => $request->query('category'),
        ]);
    }
}
