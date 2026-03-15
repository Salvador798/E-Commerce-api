<?php

namespace App\Services;

use App\Models\Address;
use App\Models\Category;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Payment;
use App\Models\User;

class ReportService
{
    // Ventas por día
    public function salesPerDay()
    {
        return Payment::selectRaw('DATE(date) as day, SUM(amount) as total')
            ->where('status', 'aprobado')
            ->groupBy('day')
            ->orderBy('day')
            ->get();
    }

    // Ventas por categoría
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

    // Ventas por cliente
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

    // Ventas por producto
    public function salesPerProduct()
    {
        return OrderItem::select('products.name')
            ->selectRaw('SUM(order_items.quantity) as units_sold')
            ->selectRaw('SUM(order_items.quantity * order_items.emit_price) as total_generated')
            ->join('products', 'products.id', '=', 'order_items.product_id')
            ->join('orders', 'orders.id', '=', 'order_items.order_id')
            ->where('orders.status', '!=', 'pendiente')
            ->groupBy('products.name')
            ->orderByDesc('total_generated')
            ->get();
    }

    // Ventas en rango de fechas
    public function salesByRank($from, $until)
    {
        return Payment::selectRaw('DATE(date) as day, SUM(amount) as total')
            ->where('status', 'aprobado')
            ->whereBetween('date', [$from, $until])
            ->groupBy('day')
            ->orderBy('day')
            ->get();
    }

    // Ventas por método de pago
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

    // Ventas por estado de pedido
    public function salesByStatusOrder()
    {
        return Order::select('status')
            ->selectRaw('COUNT(*) as quantity')
            ->selectRaw('SUM(total) as total')
            ->groupBy('status')
            ->orderByDesc('quantity')
            ->get();
    }

    // Ventas por región / ciudad
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
