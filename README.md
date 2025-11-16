# Orders & Payments - Laravel (API + Blade Views)

Repositorio de ejemplo para la prueba técnica: API REST para gestionar Orders y Payments, con vistas Blade opcionales para demostración.

## Resumen
- Laravel (9/10 compatible) + PHP 8.1+
- API para Orders y Payments (`/api/v1/...`)
- Vistas Blade para crear/ver órdenes y procesar pagos (`/orders/...`)
- Servicio `PaymentGateway` desacopla integración con gateway externo (configurable vía `.env`)
- Tests feature cubriendo flujos críticos con `Http::fake()`

## Principales decisiones técnicas
- **Service layer** (`PaymentGateway`) para desacoplar integración externa y facilitar tests.
- **FormRequest** para validar la creación de órdenes (`StoreOrderRequest`).
- **Payments**: cada intento crea un registro en `payments`. El campo `gateway_response` almacena la respuesta (JSON) del gateway.
- **Estado de orden**: `pending`, `paid`, `failed`. `paid` se actualiza cuando un intento exitoso cubre el saldo pendiente.
- **Testing**: `Http::fake()` en tests para simular gateway externo y evitar dependencias de red.
- **DB**: migraciones preparadas; recomendación de usar sqlite (in-memory) para tests.

## Instalación (rápido)
1. `composer install`
2. Copiar `.env.example` a `.env` y ajustar variables (DB, PAYMENT_GATEWAY_ENDPOINT)
3. `php artisan key:generate`
4. Crear DB (o usar sqlite):
   - Para sqlite: `touch database/database.sqlite` y en `.env` poner `DB_CONNECTION=sqlite` y `DB_DATABASE=/absolute/path/to/database.sqlite`
5. `php artisan migrate`
6. `php artisan serve`

## Endpoints principales (API)
- `POST /api/v1/orders` — crear orden  
  Body: `{ "customer_name": "...", "total_amount": 100.00 }`
- `GET /api/v1/orders` — listar órdenes (incluye pagos)
- `GET /api/v1/orders/{order}` — ver orden
- `GET /api/v1/orders/{order}/payments` — listar pagos de orden
- `POST /api/v1/orders/{order}/payments` — intentar pago  
  Body: `{ "amount": 100.00 }`

## Uso desde la vista (Blade)
- `GET /orders` — lista de órdenes
- `GET /orders/create` — crear orden
- `GET /orders/{order}` — ver detalle + botón **Intentar Pago**
- `POST /orders/{order}/pay` — ejecutar intento de pago desde la vista

## Tests
Ejecutar:
```bash
php artisan test
# o
vendor/bin/phpunit
