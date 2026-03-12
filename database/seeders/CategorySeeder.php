<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            'Bebidas',
            'Snacks',
            'Lácteos',
            'Aseo Personal',
            'Hogar'
        ];

        foreach ($categories as $name) {
            Category::create([
                'name' => $name,
                'status' => true,
            ]);
        }
    }
}
