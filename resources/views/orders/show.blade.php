@extends('layouts.app')

@section('content')

<h2>Pedido #{{ $order->id }}</h2>

@if (session('message'))
    <div class="alert alert-info">
        {{ session('message') }}
    </div>
@endif

<div class="card mb-4">
    <div class="card-body">
        <p><strong>Cliente:</strong> {{ $order->customer_name }}</p>
        <p><strong>Monto Total:</strong> ${{ number_format($order->total_amount, 2) }}</p>
        <p>
            <strong>Estado:</strong>
            <span class="badge bg-{{ $order->status === 'paid' ? 'success' : ($order->status === 'failed' ? 'danger' : 'secondary') }}">
                {{ ucfirst($order->status) }}
            </span>
        </p>

        @if ($order->status !== 'paid')
            <form action="{{ route('orders.pay', $order) }}" method="POST">
                @csrf
                <button class="btn btn-success">
                    Intentar Pago
                </button>
            </form>
        @else
            <button class="btn btn-success" disabled>Pago Completo</button>
        @endif
    </div>
</div>

<h3>Pagos Realizados</h3>

@if ($order->payments->isEmpty())
    <p>No hay pagos registrados.</p>
@else
<table class="table table-bordered">
    <thead class="table-dark">
        <tr>
            <th>ID</th>
            <th>Monto</th>
            <th>Estado</th>
            <th>Transaction ID</th>
            <th>Fecha</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($order->payments as $payment)
        <tr>
            <td>{{ $payment->id }}</td>
            <td>${{ number_format($payment->amount, 2) }}</td>
            <td>
                <span class="badge bg-{{ $payment->status === 'success' ? 'success' : 'danger' }}">
                    {{ ucfirst($payment->status) }}
                </span>
            </td>
            <td>
                {{ $payment->gateway_response['transaction_id'] ?? '—' }}
            </td>
            <td>{{ $payment->created_at }}</td>
        </tr>
        @endforeach
    </tbody>
</table>
@endif

@endsection
