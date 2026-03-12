<?php

namespace Database\Seeders;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CartSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::all()->each(function ($user) {
            $cart = Cart::create([
                'user_id' => $user->id,
            ]);

            CartItem::factory(rand(1, 5))->create([
                'cart_id' => $cart->id,
            ]);
        });
    }
}
