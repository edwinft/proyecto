<?php

namespace Tests\Feature;

use App\Models\Order;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class PaymentProcessingTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function successful_payment_marks_order_as_paid()
    {
        // Crear orden
        $order = Order::create([
            'customer_name' => 'Juan Perez',
            'total_amount' => 100.00,
            'status' => 'pending',
        ]);

        // Falsificar la API externa: responde success true
        Http::fake([
            '*' => Http::response(['success' => true, 'transaction_id' => 'tx_123'], 200),
        ]);

        // Hacer POST al endpoint de pagos
        $response = $this->postJson("/api/v1/orders/{$order->id}/payments");

        $response->assertStatus(201);
        $response->assertJsonPath('order.status', 'paid');
        $this->assertDatabaseHas('payments', [
            'order_id' => $order->id,
            'status' => 'success',
        ]);
    }

    /** @test */
    public function failed_payment_keeps_order_failed_and_allows_new_attempts()
    {
        $order = Order::create([
            'customer_name' => 'Maria',
            'total_amount' => 50.00,
            'status' => 'pending',
        ]);

        // Primer intento -> falla
        Http::fake([
            '*' => Http::response(['success' => false, 'error' => 'insufficient_funds'], 400),
        ]);

        $first = $this->postJson("/api/v1/orders/{$order->id}/payments");
        $first->assertStatus(201);
        $first->assertJsonPath('order.status', 'failed');

        // Segundo intento -> ahora exitoso (permitido)
        Http::fake([
            '*' => Http::response(['success' => true, 'transaction_id' => 'tx_456'], 200),
        ]);

        $second = $this->postJson("/api/v1/orders/{$order->id}/payments");
        $second->assertStatus(201);
        $second->assertJsonPath('order.status', 'paid');

        $this->assertDatabaseCount('payments', 2);
    }
}
