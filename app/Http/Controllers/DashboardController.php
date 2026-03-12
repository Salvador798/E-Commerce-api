<?php

namespace App\Http\Controllers;

use App\Models\Inventory;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Payment;
use App\Models\Product;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function summarySale()
    {
        return [
            'total_sales' => Payment::where('status', 'aprobado')->sum('amount'),
            'total_orders_paid' => Order::where('status', 'pagado')->count(),
            'average_ticket' => Payment::where('status', 'aprobado')->avg('amount')
        ];
    }

    public function SalesPerMonth()
    {
        return Payment::selectRaw('MONTH(date) as mes, SUM(amount) as total')
            ->where('status', 'aprobado')
            ->groupBy('mes')
            ->orderBy('mes')
            ->get();
    }

    public function BestsellingProducts()
    {
        return OrderItem::selectRaw('product_id, SUM(QUANTITY) as total_sale')
            ->groupBy('product_id')
            ->orderByDesc('total_sale')
            ->with('produict')
            ->limit(10)
            ->get();
    }

    public function summaryOrders()
    {
        return [
            'total_orders' => Order::count(),
            'pendientes' => Order::where('status', 'pendiente')->count(),
            'pagados' => Order::where('status', 'pagado')->count(),
            'enviados' => Order::where('status', 'enviado')->count(),
            'completados' => Order::where('status', 'completado')->count()
        ];
    }

    public function OrderByStatus()
    {
        return Order::selectRaw('status, COUNT(*) as total')
            ->groupBy('status')
            ->get();
    }

    public function summaryStock()
    {
        return [
            'total_products' => Product::count(),
            'products_in_stock' => Inventory::where('available_quantity', '>', 0),
            'out_of_stock products' => Inventory::where('available_quantity', '=', 0),
        ];
    }

    public function stockLow()
    {
        return Inventory::where('available_quantity', '<=', 5)
            ->with('product')
            ->orderBy('available_quantity')
            ->get();
    }
}
