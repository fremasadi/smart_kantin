<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function print(Order $order)
    {
        // Load the order items relationship
        $order->load('orderItems.product');
        
        // Get order items
        $items = $order->orderItems;
        
        // Calculate total items
        $total_items = $items->sum('jumlah');
        
        // Format print date
        $print_date = now()->format('d/m/Y H:i:s');
        
        return view('order.print', compact('order', 'items', 'total_items', 'print_date'));
    }
}