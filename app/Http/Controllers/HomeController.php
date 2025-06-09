<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Customer;
use App\Models\Product;
use Illuminate\Support\Facades\DB;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $orders = Order::with(['items', 'payments'])->get();
        $customers_count = Customer::count();

        $low_stock_products = Product::where('quantity', '<', 10)->get();

//        $bestSellingProducts = DB::table('products')
//            ->select('products.*', DB::raw('SUM(order_items.quantity) AS total_sold'))
//            ->join('order_items', 'order_items.product_id', '=', 'products.id')
//            ->join('orders', 'orders.id', '=', 'order_items.order_id')
//            ->groupBy('products.id')
//            ->havingRaw('SUM(order_items.quantity) > 10')
//            ->get();
        $bestSellingProducts = DB::table('products')
            ->select('products.*', 't.total_sold')
            ->joinSub(function ($query) {
                $query->select('order_items.product_id', DB::raw('SUM(order_items.quantity) AS total_sold'))
                    ->from('order_items')
                    ->join('orders', 'orders.id', '=', 'order_items.order_id')
                    ->groupBy('order_items.product_id')
                    ->havingRaw('SUM(order_items.quantity) > 10');
            }, 't', 'products.id', '=', 't.product_id')
            ->get();

//        $currentMonthBestSelling = DB::table('products')
//            ->select('products.*', DB::raw('SUM(order_items.quantity) AS total_sold'))
//            ->join('order_items', 'order_items.product_id', '=', 'products.id')
//            ->join('orders', 'orders.id', '=', 'order_items.order_id')
//            ->whereYear('orders.created_at', date('Y'))
//            ->whereMonth('orders.created_at', date('m'))
//            ->groupBy('products.id')
//            ->havingRaw('SUM(order_items.quantity) > 500')  // Best-selling threshold for the current month
//            ->get();
        $currentMonthBestSelling = DB::table('products')
            ->select('products.*', 't.total_sold')
            ->joinSub(function ($query) {
                $query->select('order_items.product_id', DB::raw('SUM(order_items.quantity) AS total_sold'))
                    ->from('order_items')
                    ->join('orders', 'orders.id', '=', 'order_items.order_id')
                    ->whereYear('orders.created_at', date('Y'))
                    ->whereMonth('orders.created_at', date('m'))
                    ->groupBy('order_items.product_id')
                    ->havingRaw('SUM(order_items.quantity) > 500');
            }, 't', 'products.id', '=', 't.product_id')
            ->get();

//        $pastSixMonthsHotProducts = DB::table('products')
//            ->select('products.*', DB::raw('SUM(order_items.quantity) AS total_sold'))
//            ->join('order_items', 'order_items.product_id', '=', 'products.id')
//            ->join('orders', 'orders.id', '=', 'order_items.order_id')
//            ->where('orders.created_at', '>=', now()->subMonths(6))  // Filter for the past 6 months
//            ->groupBy('products.id')
//            ->havingRaw('SUM(order_items.quantity) > 1000')  // Hot product threshold for past 6 months
//            ->get();
        $pastSixMonthsHotProducts = DB::table('products')
            ->select('products.*', 't.total_sold')
            ->joinSub(function ($query) {
                $query->select('order_items.product_id', DB::raw('SUM(order_items.quantity) AS total_sold'))
                    ->from('order_items')
                    ->join('orders', 'orders.id', '=', 'order_items.order_id')
                    ->where('orders.created_at', '>=', now()->subMonths(6))
                    ->groupBy('order_items.product_id')
                    ->havingRaw('SUM(order_items.quantity) > 1000');
            }, 't', 'products.id', '=', 't.product_id')
            ->get();


        return view('home', [
            'orders_count' => $orders->count(),
            'income' => $orders->map(function ($i) {
                return $i->receivedAmount() > $i->total() ? $i->total() : $i->receivedAmount();
            })->sum(),
            'income_today' => $orders->where('created_at', '>=', date('Y-m-d') . ' 00:00:00')->map(function ($i) {
                return $i->receivedAmount() > $i->total() ? $i->total() : $i->receivedAmount();
            })->sum(),
            'customers_count' => $customers_count,
            'low_stock_products' => $low_stock_products,
            'best_selling_products' => $bestSellingProducts,
            'current_month_products' => $currentMonthBestSelling,
            'past_months_products' => $pastSixMonthsHotProducts,
        ]);
    }
}
