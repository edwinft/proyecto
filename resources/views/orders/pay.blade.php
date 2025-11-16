@extends('layouts.app')

@section('content')
<div class="container">
    <h2>Pagar Orden #{{ $order->id }}</h2>

    <p><strong>Cliente:</strong> {{ $order->customer_name }}</p>
    <p><strong>Total:</strong> S/ {{ number_format($order->total_amount, 2) }}</p>

    @php
        $pagado = $order->payments()->where('status','success')->sum('amount');
        $pendiente = $order->total_amount - $pagado;
    @endphp

    <p><strong>Pagado:</strong> S/ {{ number_format($pagado, 2) }}</p>
    <p><strong>Pendiente:</strong> S/ {{ number_format($pendiente, 2) }}</p>

    @if(session('message'))
        <div class="alert alert-info">{{ session('message') }}</div>
    @endif

    @if($order->status === 'paid')
        <div class="alert alert-success">La orden ya está totalmente pagada.</div>
        <a href="{{ route('orders.index') }}" class="btn btn-primary">Volver</a>
    @else
        <form method="POST" action="{{ route('orders.pay.process', $order) }}">
            @csrf
            <label>Monto a pagar</label>
            <input type="number" step="0.01" class="form-control" name="amount" value="{{ $pendiente }}" required>

            <button class="btn btn-success mt-3">Pagar ahora</button>
        </form>
    @endif
</div>
@endsection
