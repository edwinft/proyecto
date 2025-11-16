<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Payment;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function store(Request $request, Order $order)
    {
        $request->validate([
            'amount' => 'required|numeric|min:1'
        ]);

        // Total pagado anteriormente
        $alreadyPaid = $order->payments()->where('status', 'success')->sum('amount');

        // Saldo pendiente
        $remaining = $order->total_amount - $alreadyPaid;

        // Logica de exito del pago
        $success = $request->amount == $remaining;

        // Crear intento
        $payment = $order->payments()->create([
            'amount' => $request->amount,
            'status' => 'processing'
        ]);

        $response = [
            'success' => $success,
            'transaction_id' => uniqid('txn_'),
            'amount' => $request->amount,
            'provider' => 'FakePay',
            'timestamp' => now()->format("Y-m-d H:i:s"),
            'message' => $success ? "Payment approved" : "Payment rejected"
        ];

        // Actualizar intento
        $payment->update([
            'status' => $success ? 'success' : 'failed',
            'gateway_response' => $response
        ]);

        // Si es éxito, actualizar orden
        if ($success) {
            $order->update(['status' => 'paid']);
        }

        return response()->json([
            'message' => 'Payment processed',
            'payment' => $payment,
            'order_paid' => $order->status === 'paid'
        ]);
    }

    private function simulateGateway($amount)
    {
        $isSuccess = rand(0, 10) > 4;

        return [
            'success' => $isSuccess,
            'transaction_id' => uniqid('txn_'),
            'amount' => $amount,
            'provider' => 'FakePay',
            'timestamp' => now()->toDateTimeString(),
            'message' => $isSuccess ? 'Payment approved' : 'Payment rejected',
        ];
    }
}
