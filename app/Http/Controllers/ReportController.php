<?php

namespace App\Http\Controllers;

use App\Models\Address;
use App\Models\Category;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    // Sale Per Day
    public function salesPerDay()
    {
        return Payment::selectRaw('DATE(date) as day, SUM(amount) as total')
            ->where('status', 'aprobado')
            ->groupBy('day')
            ->orderBy('day')
            ->get();
    }

    // Sales by category
    public function salesByCategory()
    {
        return Category::select('categories.name')
            ->selectRaw('SUM(order_items.quantity * order_items.emit_price) as total')
            ->join('product_category', 'product_category.category_id', '=', 'categories.id')
            ->join('products', 'products.id', '=', 'product_category.product_id')
            ->join('order_items', 'order_items.product_id', '=', 'products.id')
            ->join('orders', 'orders.id', '=', 'order_items.order_id')
            ->where('orders.status', '!=', 'pendiente')
            ->groupBy('categories.name')
            ->orderByDesc('total')
            ->get();
    }

    // Sale per customer
    public function salesPerCustomer()
    {
        return User::select('users.id', 'users.name', 'users.email')
            ->selectRaw('SUM(payments.amount) as total_spent')
            ->join('orders', 'orders.user_id', '=', 'users.id')
            ->join('payments', 'payments.order_id', '=', 'orders.id')
            ->where('payments.status', 'aprobado')
            ->groupBy('users.id', 'users.name', 'users.email')
            ->orderByDesc('total_spent')
            ->get();
    }

    // Sales per product
    public function salesPerProduct()
    {
        return OrderItem::select('products.name')
            ->selectRaw('SUM(order_items.quantity) as units_sold')
            ->selectRaw('SUM(order_items.quantity * order_items.emit_price) as total_generado')
            ->join('products', 'products.id', '=', 'order_items.product_id')
            ->join('orders', 'orders.id', '=', 'order_items.order_id')
            ->where('orders.status', '!=', 'pendiente')
            ->groupBy('products.name')
            ->orderByDesc('total_generado')
            ->get();
    }

    // Sales by rank
    public function salesByRank(Request $request)
    {
        $request->validate([
            'from' => 'required|date',
            'until' => 'required|date'
        ]);

        return Payment::selectRaw('DATE(date) as day, SUM(amount) as total')
            ->where('status', 'aprobado')
            ->whereBetween('date', [$request->from, $request->until])
            ->groupBy('day')
            ->orderBy('day')
            ->get();
    }

    // Sales by payment method
    public function salesByPaymentMethod()
    {
        return Payment::select('method')
            ->selectRaw('COUNT(*) as quantity')
            ->selectRaw('SUM(amount) as total')
            ->where('status', 'aprobado')
            ->groupBy('method')
            ->orderByDesc('total')
            ->get();
    }

    // Sales by status order
    public function salesByStatusOrder()
    {
        return Order::select('status')
            ->selectRaw('COUNT(*) as quantity')
            ->selectRaw('SUM(total) as total')
            ->groupBy('status')
            ->orderByDesc('quantity')
            ->get();
    }

    // Sales by region
    public function salesByRegion()
    {
        return Address::select('city')
            ->selectRaw('SUM(orders.total) as total')
            ->join('orders', 'orders.address_id', '=', 'addresses.id')
            ->join('payments', 'payments.order_id', '=', 'orders.id')
            ->where('payments.status', 'aprobado')
            ->groupBy('city')
            ->orderByDesc('total')
            ->get();
    }
}
