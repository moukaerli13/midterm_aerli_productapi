<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed sample categories and products.
     */
    public function run(): void
    {
        $drinks = Category::create([
            'category_name' => 'Drinks',
            'description' => 'Soft drinks, water and juice',
            'image' => 'https://picsum.photos/seed/drinks/200',
        ]);

        $snacks = Category::create([
            'category_name' => 'Snacks',
            'description' => 'Chips, cookies and candy',
            'image' => 'https://picsum.photos/seed/snacks/200',
        ]);

        Category::create([
            'category_name' => 'Household',
            'description' => 'Cleaning and home items',
            'image' => 'https://picsum.photos/seed/household/200',
        ]);

        Product::create([
            'ProductName' => 'Mineral Water 500ml',
            'Description' => 'Bottled drinking water',
            'CategoryID' => $drinks->category_id,
            'Barcode' => '8850001000011',
            'Expiredate' => '2027-06-30',
            'Qty' => 120,
            'UnitPriceIn' => 0.25,
            'UnitPriceOut' => 0.50,
            'ProductImage' => null,
        ]);

        Product::create([
            'ProductName' => 'Potato Chips',
            'Description' => 'Original flavour 50g',
            'CategoryID' => $snacks->category_id,
            'Barcode' => '8850001000028',
            'Expiredate' => '2027-01-15',
            'Qty' => 45,
            'UnitPriceIn' => 0.60,
            'UnitPriceOut' => 1.00,
            'ProductImage' => null,
        ]);
    }
}
