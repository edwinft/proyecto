@extends('layouts.app')

@section('content')
<h2>Crear Pedido</h2>

<form action="{{ route('orders.store') }}" method="POST" class="mt-3">
    @csrf

    <div class="mb-3">
        <label>Nombre del Cliente</label>
        <input type="text" name="customer_name" class="form-control" required>
    </div>

    <div class="mb-3">
        <label>Monto Total</label>
        <input type="number" step="0.01" name="total_amount" class="form-control" required>
    </div>

    <button class="btn btn-primary">Guardar</button>
</form>
@endsection
