<?php

namespace App\Http\Controllers;

use App\Http\Requests\OrderStoreRequest;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $orders = new Order();
        if ($request->start_date) {
            $orders = $orders->where('created_at', '>=', $request->start_date);
        }
        if ($request->end_date) {
            $orders = $orders->where('created_at', '<=', $request->end_date . ' 23:59:59');
        }
        $orders = $orders->with(['items.product', 'payments', 'customer'])->latest()->paginate(10);

        $total = $orders->map(function ($i) {
            return $i->total();
        })->sum();
        $receivedAmount = $orders->map(function ($i) {
            return $i->receivedAmount();
        })->sum();

        return view('orders.index', compact('orders', 'total', 'receivedAmount'));
    }

    /**
     * Place a new order from the cart WITHOUT payment.
     * Order is created with status=open so items can be added later.
     */
    public function store(OrderStoreRequest $request)
    {
        $discount = $request->input('discount', 0);

        $order = Order::create([
            'customer_id' => $request->customer_id,
            'user_id' => $request->user()->id,
            'instruction' => $request->instruction ?? null,
            'type' => $request->type,
            'discount' => $request->discount,
            'table_no' => $request->table_no ?? null,
            'status' => 'open',
        ]);

        $cart = $request->user()->cart()->get();
        foreach ($cart as $item) {
            $order->items()->create([
                'price' => $item->price * $item->pivot->quantity,
                'quantity' => $item->pivot->quantity,
                'product_id' => $item->id,
            ]);
            $item->quantity = $item->quantity - $item->pivot->quantity;
            $item->save();
        }
        $request->user()->cart()->detach();

        // If amount is provided (optional), create a payment
        if ($request->amount && $request->amount > 0) {
            $order->payments()->create([
                'amount' => $request->amount,
                'user_id' => $request->user()->id,
            ]);
        }

        $data['order'] = $order->load('items.product');
        $data['message'] = 'success';
        return $data;
    }

    /**
     * Add more items to an existing open order from the current cart.
     */
    public function addItems(Request $request, Order $order)
    {
        if ($order->isCompleted()) {
            return response()->json(['message' => 'Cannot add items to a completed order.'], 400);
        }

        $cart = $request->user()->cart()->get();

        if ($cart->isEmpty()) {
            return response()->json(['message' => 'Cart is empty.'], 400);
        }

        foreach ($cart as $item) {
            // Check if this product already exists in the order
            $existingItem = $order->items()->where('product_id', $item->id)->first();

            if ($existingItem) {
                // Update existing item quantity and price
                $existingItem->quantity += $item->pivot->quantity;
                $existingItem->price += $item->price * $item->pivot->quantity;
                $existingItem->save();
            } else {
                // Create new order item
                $order->items()->create([
                    'price' => $item->price * $item->pivot->quantity,
                    'quantity' => $item->pivot->quantity,
                    'product_id' => $item->id,
                ]);
            }

            // Decrease product stock
            $item->quantity = $item->quantity - $item->pivot->quantity;
            $item->save();
        }

        $request->user()->cart()->detach();

        $data['order'] = $order->load('items.product');
        $data['message'] = 'Items added successfully';
        return $data;
    }

    /**
     * Checkout an open order — record payment and mark as completed.
     */
    public function checkout(Request $request, Order $order)
    {
        $request->validate([
            'amount' => 'required|numeric|min:0',
        ]);

        if ($order->isCompleted()) {
            return response()->json(['message' => 'Order is already completed.'], 400);
        }

        DB::transaction(function () use ($order, $request) {
            $order->payments()->create([
                'amount' => $request->amount,
                'user_id' => $request->user()->id,
            ]);

            $order->status = 'completed';
            $order->save();
        });

        $data['order'] = $order->load('items.product', 'payments');
        $data['message'] = 'Order checked out successfully';
        return $data;
    }

    public function partialPayment(Request $request)
    {
        $orderId = $request->order_id;
        $amount = $request->amount;

        $order = Order::findOrFail($orderId);

        $remainingAmount = $order->total() - $order->receivedAmount();
        if ($amount > $remainingAmount) {
            return redirect()->route('orders.index')->withErrors('Amount exceeds remaining balance');
        }

        DB::transaction(function () use ($order, $amount) {
            $order->payments()->create([
                'amount' => $amount,
                'user_id' => auth()->user()->id,
            ]);
        });

        return redirect()->route('orders.index')->with('success', 'Partial payment of ' . config('settings.currency_symbol') . number_format($amount, 2) . ' made successfully.');
    }

    public function updateCookStatus(Request $request)
    {
        $request->validate([
            'order_id' => 'required|exists:orders,id',
            'cook_status' => 'required|in:pending,preparing,complete',
        ]);

        $order = Order::findOrFail($request->order_id);
        $order->cook_status = $request->cook_status;
        $order->save();

        return response()->json(['message' => 'Status updated successfully']);
    }

    public function printReceipt(Order $order)
    {
        return view('orders.receipt', compact('order'));
    }

    /**
     * Print Kitchen Receipt — focusing on items and instructions (no prices).
     */
    public function printKitchenReceipt(Order $order)
    {
        return view('orders.kitchen_receipt', compact('order'));
    }

    /**
     * Export all sold items and their quantities (Admin only).
     */
    public function exportSoldItems()
    {
        $soldItems = DB::table('order_items')
            ->join('products', 'order_items.product_id', '=', 'products.id')
            ->select('products.name', DB::raw('SUM(order_items.quantity) as total_quantity'))
            ->groupBy('products.id', 'products.name')
            ->get();

        $headers = [
            'Content-type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename=sold_items_' . date('Y-m-d') . '.csv',
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ];

        $callback = function () use ($soldItems) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['Item Name', 'Quantity Sold']);
            foreach ($soldItems as $item) {
                fputcsv($file, [$item->name, $item->total_quantity]);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Export all orders for the current day with items and details (Admin only).
     */
    public function exportDailyOrders()
    {
        $today = date('Y-m-d');
        $orders = Order::whereDate('created_at', $today)
            ->with(['items.product', 'payments'])
            ->get();

        $headers = [
            'Content-type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename=daily_orders_' . $today . '.csv',
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ];

        $callback = function () use ($orders) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['Order ID', 'Table', 'Type', 'Total', 'Paid', 'Status', 'Cook Status', 'Items (Qty)']);

            foreach ($orders as $order) {
                $itemsStr = '';
                foreach ($order->items as $item) {
                    $itemsStr .= ($item->product->name ?? 'Unknown') . ' (' . $item->quantity . '), ';
                }
                $itemsStr = rtrim($itemsStr, ', ');

                fputcsv($file, [
                    '#' . $order->id,
                    $order->table_no ?? 'N/A',
                    $order->type ?? 'N/A',
                    number_format($order->total(), 2),
                    number_format($order->receivedAmount(), 2),
                    $order->status,
                    $order->cook_status,
                    $itemsStr
                ]);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
