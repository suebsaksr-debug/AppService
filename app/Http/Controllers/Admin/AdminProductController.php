<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class AdminProductController extends Controller
{
    private const CATEGORIES = ['Clothing', 'Shoes', 'Bags', 'Home', 'Tech', 'Accessories'];

    public function dashboard()
    {
        return view('template.backend.master', [
            'products' => Product::latest()->take(8)->get(),
            'totalProducts' => Product::count(),
            'activeProducts' => Product::where('is_active', true)->count(),
            'catalogValue' => Product::sum('price'),
        ]);
    }

    public function index()
    {
        return view('admin.products.index', [
            'products' => Product::latest()->paginate(15),
            'categories' => self::CATEGORIES,
        ]);
    }

    public function create()
    {
        return view('admin.products.form', [
            'product' => new Product(),
            'categories' => self::CATEGORIES,
        ]);
    }

    public function store(Request $request)
    {
        Product::create($this->validatedData($request));

        return redirect()->route('admin.products.index')->with('success', 'Product created.');
    }

    public function edit(Product $product)
    {
        return view('admin.products.form', [
            'product' => $product,
            'categories' => self::CATEGORIES,
        ]);
    }

    public function update(Request $request, Product $product)
    {
        $product->update($this->validatedData($request));

        return redirect()->route('admin.products.index')->with('success', 'Product updated.');
    }

    public function destroy(Product $product)
    {
        $product->delete();

        return redirect()->route('admin.products.index')->with('success', 'Product deleted.');
    }

    private function validatedData(Request $request): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'category' => ['required', Rule::in(self::CATEGORIES)],
            'description' => ['nullable', 'string', 'max:5000'],
            'price' => ['required', 'numeric', 'min:0', 'max:99999999.99'],
            'image' => ['nullable', 'string', 'max:2048', 'regex:/^(https:\/\/|images\/)[^\s"\'<>]*$/i'],
            'is_active' => ['required', 'boolean'],
        ]);
    }
}
