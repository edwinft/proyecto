<!DOCTYPE html>
<html>
<head>
    <title>Orders & Payments</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
</head>
<body class="bg-light">

<nav class="navbar navbar-dark bg-dark mb-4 p-2">
    <div class="container">
        <a href="{{ route('orders.index') }}" class="navbar-brand">Orders & Payments</a>
    </div>
</nav>

<div class="container">
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @yield('content')
</div>

</body>
</html>
