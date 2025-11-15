<?php


namespace App\Services;


use Illuminate\Support\Facades\Http;
use Illuminate\Http\Client\Response;


class PaymentGateway
{
    protected string $endpoint;


    public function __construct()
    {
        $this->endpoint = config('services.payment_gateway.endpoint')
            ?? env('PAYMENT_GATEWAY_ENDPOINT', 'https://reqres.in/api/payments');
    }


    /**
     * Process payment against external gateway.
     * Returns array with keys: success (bool), response (array|null), statusCode (int|null)
     */
    public function processPayment(int $orderId, float $amount): array
    {
        $payload = [
            'order_id' => $orderId,
            'amount' => $amount,
        ];


        try {
            $response = Http::post($this->endpoint, $payload);


            $status = $response->status();
            $body = $response->json();


            // Decide success flag: if 'success' key present and truthy.
            $success = false;
            if (is_array($body) && array_key_exists('success', $body)) {
                $success = (bool)$body['success'];
            } else {
                // fallback: http 200/201 considered success
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
