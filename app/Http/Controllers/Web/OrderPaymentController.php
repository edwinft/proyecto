<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;

class OrderPaymentController extends Controller
{
    public function showPaymentForm(Order $order)
    {
        $order->load('payments');
        return view('orders.pay', compact('order'));
    }

    public function processPayment(Request $request, Order $order)
    {
        $request->validate([
            'amount' => 'required|numeric|min:0.1'
        ]);

        // total ya pagado
        $paid = $order->payments()->where('status', 'success')->sum('amount');
        $remaining = $order->total_amount - $paid;

        $success = $request->amount >= $remaining;

        // crear intento de pago
        $payment = $order->payments()->create([
            'amount' => $request->amount,
            'status' => 'processing'
        ]);

        // respuesta fake
        $gatewayResponse = [
            'success' => $success,
            'transaction_id' => uniqid('txn_'),
            'amount' => $request->amount,
            'provider' => 'FakePay',
            'timestamp' => now()->format('Y-m-d H:i:s'),
            'message' => $success ? 'Payment approved' : 'Payment rejected'
        ];

        $payment->update([
            'status' => $success ? 'success' : 'failed',
            'gateway_response' => $gatewayResponse
        ]);

        if ($success) {
            $order->update(['status' => 'paid']);
        }

        return redirect()
            ->back()
            ->with('message', $success ? 'Pago exitoso' : 'Pago rechazado');
    }
}
