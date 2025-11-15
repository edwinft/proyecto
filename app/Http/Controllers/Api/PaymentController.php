<?php


namespace App\Http\Controllers\Api;


use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Payment;
use App\Services\PaymentGateway;
use Illuminate\Http\Request;
use Illuminate\Http\Response;


class PaymentController extends Controller
{
    protected PaymentGateway $gateway;


    public function __construct(PaymentGateway $gateway)
    {
        $this->gateway = $gateway;
    }


    /**
     * Store a payment attempt for an order. Amount is the order's total_amount (business rule).
     */
    public function store(Request $request, Order $order)
    {
        // Business rule: payment amount is the order's total
        $amount = (float)$order->total_amount;


        // Create a payment record in 'processing'
        $payment = Payment::create([
            'order_id' => $order->id,
            'amount' => $amount,
            'status' => 'processing',
        ]);


        // Process payment with gateway
        $result = $this->gateway->processPayment($order->id, $amount);


        $payment->gateway_response = $result['response'] ?? null;


        if ($result['success']) {
            $payment->status = 'success';
            $order->status = 'paid';
        } else {
            $payment->status = 'failed';
            // Only set order to failed if not already paid
            if ($order->status !== 'paid') {
                $order->status = 'failed';
            }
        }


        $payment->save();
        $order->save();


        return response()->json([
            'payment' => $payment,
            'order' => $order->fresh(),
        ], Response::HTTP_CREATED);
    }
}
