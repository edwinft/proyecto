<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class PaymentGateway
{
    protected string $endpoint;

    public function __construct()
    {
        // Toma la URL de config/services.php y si no existe usa el default
        $this->endpoint = config('services.payment_gateway.endpoint')
            ?: env('PAYMENT_GATEWAY_ENDPOINT', 'https://pagos-test.free.beeceptor.com/payments');
    }

    public function processPayment(int $orderId, float $amount): array
    {
        $payload = [
            'order_id' => $orderId,
            'amount'   => $amount,
        ];

        try {
            // dd($this->endpoint);
            $response = Http::post($this->endpoint, $payload);

            $status = $response->status();
            $body = $response->json();

            // Determinar éxito
            $success = false;

            if (is_array($body) && array_key_exists('success', $body)) {
                $success = (bool) $body['success'];
            } else {
                $success = in_array($status, [200, 201]);
            }

            return [
                'success' => $success,
                'response' => $body,
                'statusCode' => $status,
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'response' => ['error' => $e->getMessage()],
                'statusCode' => null,
            ];
        }
    }
}
