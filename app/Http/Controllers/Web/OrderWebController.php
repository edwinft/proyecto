<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use App\Services\PaymentGateway;

class OrderWebController extends Controller
{
    public function index()
    {
        $orders = Order::with('payments')->latest()->get();
        return view('orders.index', compact('orders'));
    }

    public function create()
    {
        return view('orders.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'customer_name' => 'required|string',
            'total_amount' => 'required|numeric|min:0.1',
        ]);

        $order = Order::create([
            'customer_name' => $request->customer_name,
            'total_amount' => $request->total_amount,
            'status' => 'pending',
        ]);

        return redirect()->route('orders.show', $order)->with('success', 'Pedido creado');
    }

    public function show(Order $order)
    {
        $order->load('payments');
        return view('orders.show', compact('order'));
    }

    public function pay(Order $order, PaymentGateway $gateway)
    {
        $result = $gateway->processPayment($order->id, $order->total_amount);

        $success = $result['success'];

        // Registrar el pago con el response completo
        $order->payments()->create([
            'amount' => $order->total_amount,
            'status' => $success ? 'success' : 'failed',
            'gateway_response' => $result, // <-- IMPORTANTE
        ]);

        // Actualizar orden
        $order->status = $success ? 'paid' : 'failed';
        $order->save();

        return back()->with('message', $success ? 'Pago exitoso' : 'Pago fallido');
    }
}
