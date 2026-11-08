# E-commerce API

API RESTful segura para un sistema de comercio electrónico desarrollada con Laravel 12.

El proyecto permite gestionar productos, usuarios, órdenes y pagos, incorporando autenticación mediante JWT, validación de datos con Form Requests, integración con Stripe y documentación interactiva mediante Swagger/OpenAPI.

## Tecnologías utilizadas

- PHP 8.3+
- Laravel 12
- MySQL
- JWT (`tymon/jwt-auth`)
- Stripe (`stripe/stripe-php`)
- Swagger/OpenAPI (`darkaonline/l5-swagger`)
- Postman / Swagger UI para pruebas de la API

## Funcionalidades

- Registro e inicio de sesión de usuarios.
- Autenticación mediante JSON Web Tokens (JWT).
- Consulta pública de productos.
- CRUD de productos.
- Creación y consulta de órdenes.
- Historial de órdenes del usuario autenticado.
- Descuento automático del stock al crear una orden.
- Validación de datos mediante Laravel Form Requests.
- Procesamiento de pagos mediante Stripe.
- Manejo de errores mediante respuestas JSON.
- Documentación completa de la API mediante Swagger/OpenAPI.

## Requisitos

Antes de instalar el proyecto se necesita:

- PHP 8.2 o superior.
- Composer.
- MySQL.
- Una cuenta de Stripe para utilizar la integración de pagos.
- Git.

## Instalación

Clonar el repositorio:

```bash
git clone <URL_DEL_REPOSITORIO>
cd ecommerce-api
```

Instalar las dependencias de PHP:

```bash
composer install
```

Crear el archivo de configuración:

```bash
cp .env.example .env
```

En Windows PowerShell también puede utilizarse:

```powershell
Copy-Item .env.example .env
```

Generar la clave de la aplicación:

```bash
php artisan key:generate
```

## Configuración de la base de datos

Crear una base de datos MySQL llamada:

```text
ecommerce_api
```

Configurar las variables correspondientes en `.env`:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=ecommerce_api
DB_USERNAME=root
DB_PASSWORD=
```

## Configuración de JWT

Configurar la clave secreta utilizada para la autenticación JWT:

```env
JWT_SECRET=
```

Si es necesario generar una nueva clave JWT:

```bash
php artisan jwt:secret
```

> No se deben publicar claves JWT reales en el repositorio.

## Configuración de Stripe

Agregar las credenciales de Stripe al archivo `.env`:

```env
STRIPE_KEY=
STRIPE_SECRET_KEY=
```

> Las claves reales de Stripe deben mantenerse únicamente en `.env` y nunca deben publicarse en GitHub.

## Migraciones y seeders

Ejecutar las migraciones:

```bash
php artisan migrate
```

Para poblar la tabla de productos con datos de prueba:

```bash
php artisan db:seed
```

También puede utilizarse:

```bash
php artisan migrate --seed
```

## Ejecutar el proyecto

Iniciar el servidor de desarrollo:

```bash
php artisan serve
```

La API estará disponible normalmente en:

```text
http://127.0.0.1:8000
```

## Documentación Swagger

La API cuenta con documentación interactiva mediante Swagger UI.

Después de iniciar el servidor, acceder a:

```text
http://127.0.0.1:8000/api/documentation
```

Desde Swagger es posible consultar los endpoints, enviar parámetros y cuerpos JSON y probar las rutas protegidas utilizando un token JWT mediante el botón **Authorize**.

## Autenticación

Los endpoints protegidos utilizan un token JWT mediante el encabezado:

```text
Authorization: Bearer {token}
```

Primero se debe iniciar sesión mediante:

```text
POST /api/auth/login
```

El token recibido puede utilizarse posteriormente para acceder a los endpoints protegidos.

## Endpoints principales

### Autenticación

| Método | Endpoint | Descripción | Auth |
|---|---|---|---|
| POST | `/api/auth/register` | Registrar usuario | No |
| POST | `/api/auth/login` | Iniciar sesión | No |
| GET | `/api/auth/me` | Obtener usuario autenticado | JWT |
| POST | `/api/auth/logout` | Cerrar sesión | JWT |

### Productos

| Método | Endpoint | Descripción | Auth |
|---|---|---|---|
| GET | `/api/products` | Listar productos | No |
| GET | `/api/products/{id}` | Consultar producto | No |
| POST | `/api/products` | Crear producto | JWT |
| PUT | `/api/products/{id}` | Actualizar producto | JWT |
| DELETE | `/api/products/{id}` | Eliminar producto | JWT |

### Órdenes

| Método | Endpoint | Descripción | Auth |
|---|---|---|---|
| POST | `/api/orders` | Crear una orden | JWT |
| GET | `/api/orders` | Consultar historial de órdenes | JWT |

### Pagos

| Método | Endpoint | Descripción | Auth |
|---|---|---|---|
| POST | `/api/payments` | Procesar el pago de una orden | JWT |

## Ejemplo de creación de una orden

```json
{
  "items": [
    {
      "product_id": 1,
      "quantity": 2
    }
  ]
}
```

Al crear una orden, el sistema calcula el total, registra los productos adquiridos y descuenta las cantidades correspondientes del stock.

## Validaciones

La API utiliza Laravel Form Requests para validar los datos recibidos.

Entre ellos:

- `ProductRequest`
- `OrderRequest`
- `RegisterRequest`
- `PaymentRequest`

Las solicitudes que contienen datos inválidos generan respuestas HTTP `422 Unprocessable Content` con información sobre los campos que no cumplen las reglas de validación.

## Manejo de errores

Las rutas de la API devuelven respuestas en formato JSON.

Los errores utilizan códigos HTTP apropiados, incluyendo:

- `401` — No autenticado.
- `403` — Acción no permitida.
- `404` — Recurso no encontrado.
- `422` — Datos de validación incorrectos.
- `500` — Error interno del servidor.

Las respuestas de error de la API no exponen información interna como rutas de archivos o trazas de ejecución.

## Estructura principal

```text
app/
├── Http/
│   ├── Controllers/
│   └── Requests/
├── Models/
routes/
├── api.php
├── web.php
database/
├── migrations/
└── seeders/
storage/
└── api-docs/
bootstrap/
└── app.php
```

## Seguridad

- Las rutas sensibles están protegidas mediante JWT.
- Las contraseñas de los usuarios se almacenan utilizando el sistema de hashing de Laravel.
- Las credenciales de base de datos y Stripe se mantienen en `.env`.
- `.env` no debe incluirse en el repositorio.
- `.env.example` contiene únicamente valores de ejemplo.
- Las entradas de la API son validadas mediante Form Requests.

## Licencia

Este proyecto fue desarrollado como parte de una práctica académica de desarrollo de APIs RESTful con Laravel.
