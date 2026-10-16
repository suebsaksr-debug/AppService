<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $products = [
            ['Organic Cotton T-Shirt', 'Clothing', 24, 'Soft, midweight organic cotton with a relaxed fit.', 'images/categories/clothing.jpg'],
            ['Classic Denim Jacket', 'Clothing', 79, 'Washed denim with a timeless cut and sturdy metal buttons.', 'images/categories/clothing.jpg'],
            ['Canvas Low-Top Sneakers', 'Shoes', 59, 'Lightweight everyday sneakers with a cushioned insole.', 'images/categories/shoes.jpg'],
            ['Everyday Canvas Tote', 'Bags', 32, 'Roomy tote with an inner pocket and reinforced handles.', 'images/categories/bags.jpg'],
            ['Ceramic Pour-Over Set', 'Home', 38, 'Hand-glazed dripper and carafe for a slow morning coffee.', 'images/categories/home.jpg'],
            ['Linen Throw Blanket', 'Home', 54, 'Washed linen that gets softer with every wash.', 'images/categories/home.jpg'],
            ['Wireless Earbuds', 'Tech', 69, 'Bluetooth earbuds with 24 hours of battery in the case.', 'images/categories/tech.jpg'],
            ['Minimalist Wrist Watch', 'Accessories', 95, 'Stainless steel case, sapphire-coated glass and a leather strap.', 'images/categories/accessories.jpg'],
        ];

        foreach ($products as [$name, $category, $price, $description, $image]) {
            Product::firstOrCreate(
                ['name' => $name],
                compact('category', 'price', 'description', 'image')
            );
        }
    }
}
