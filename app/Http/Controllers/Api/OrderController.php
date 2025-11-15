<?php


namespace App\Http\Controllers\Api;


use App\Http\Controllers\Controller;
use App\Http\Requests\StoreOrderRequest;
use App\Models\Order;
use Illuminate\Http\Request;


class OrderController extends Controller
{
    public function store(StoreOrderRequest $request)
    {
        $order = Order::create($request->validated());


        return response()->json($order, 201);
    }


    public function index()
    {
        $orders = Order::with('payments')->get()->map(function (Order $order) {
            return [
                'id' => $order->id,
                'customer_name' => $order->customer_name,
                'total_amount' => (float)$order->total_amount,
                'status' => $order->status,
                'payment_attempts' => $order->payments->count(),
                'payments' => $order->payments->map(function ($p) {
                    return [
                        'id' => $p->id,
                        'amount' => (float)$p->amount,
                        'status' => $p->status,
                        'gateway_response' => $p->gateway_response,
                        'created_at' => $p->created_at,
                    ];
                }),
                'created_at' => $order->created_at,
            ];
        });


        return response()->json($orders);
    }


    public function show(Order $order)
    {
        $order->load('payments');
        return response()->json($order);
    }
}
