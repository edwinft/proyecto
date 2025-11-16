@extends('layouts.app')

@section('content')
<div class="d-flex justify-content-between mb-3">
    <h2>Pedidos</h2>
    <a href="{{ route('orders.create') }}" class="btn btn-primary">Crear Pedido</a>
</div>

<table class="table table-bordered table-striped">
    <thead class="table-dark">
        <tr>
            <th>ID</th>
            <th>Cliente</th>
            <th>Monto</th>
            <th>Estado</th>
            <th>Pagos</th>
            <th>Acciones</th>
        </tr>
    </thead>
    <tbody>
    @foreach ($orders as $order)
        <tr>
            <td>{{ $order->id }}</td>
            <td>{{ $order->customer_name }}</td>
            <td>$ {{ number_format($order->total_amount, 2) }}</td>
            <td>
                <span class="badge bg-{{ $order->status === 'paid' ? 'success' : ($order->status === 'failed' ? 'danger' : 'secondary') }}">
                    {{ $order->status }}
                </span>
            </td>
            <td>{{ $order->payments->count() }}</td>
            <td>
                <a href="{{ route('orders.show', $order) }}" class="btn btn-sm btn-info">Ver</a>
            </td>
        </tr>
    @endforeach
    </tbody>
</table>
@endsection
