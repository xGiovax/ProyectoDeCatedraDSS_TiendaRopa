# 🛍️ Sistema de Gestión - Tienda de Ropa

Sistema web desarrollado con **Laravel 12** + **MySQL** que permite gestionar productos, inventario, órdenes y ventas de una tienda de ropa. Incluye una API REST con autenticación mediante Laravel Sanctum y un frontend construido con Blade + Bootstrap 5.

---

## 📋 Requisitos previos

- PHP 8.2 o superior
- Composer 2.x
- MySQL 5.7 o superior (XAMPP recomendado)
- Node.js 18 o superior
- NPM

---

## ⚙️ Instalación y configuración

### 1. Clonar o descargar el proyecto

```bash
git clone https://github.com/tu-usuario/tienda-ropa.git
cd tienda-ropa
```

### 2. Instalar dependencias PHP

```bash
composer install
```

### 3. Instalar dependencias Node

```bash
npm install
```

### 4. Configurar el archivo de entorno

Copia el archivo de ejemplo y edítalo:

```bash
cp .env.example .env
```

Edita `.env` con tus datos:

```env
APP_NAME="Tienda Ropa"
APP_ENV=local
APP_DEBUG=true
APP_URL=http://127.0.0.1:8000

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=tienda_ropa
DB_USERNAME=root
DB_PASSWORD=
```

### 5. Generar clave de aplicación

```bash
php artisan key:generate
```

### 6. Crear la base de datos

Abre phpMyAdmin en `http://localhost/phpmyadmin` y crea una base de datos llamada `tienda_ropa` con cotejamiento `utf8mb4_unicode_ci`.

### 7. Ejecutar migraciones y seeders

```bash
php artisan migrate:fresh --seed
```

### 8. Iniciar el servidor

```bash
php artisan serve
```

Accede al sistema en: `http://127.0.0.1:8000`

---

## 👥 Usuarios de prueba

| Rol           | Email                  | Contraseña |
|---------------|------------------------|------------|
| Administrador | admin@tienda.com       | password   |
| Vendedor      | vendedor@tienda.com    | password   |
| Cajero        | cajero@tienda.com      | password   |

---

## 🏗️ Estructura del sistema

### Módulos principales

| Módulo    | Descripción                              |
|-----------|------------------------------------------|
| Usuarios  | Gestión de usuarios y roles              |
| Productos | CRUD con control de stock                |
| Bodegas   | Ubicaciones físicas de productos         |
| Órdenes   | Gestión del proceso de venta             |
| Ventas    | Procesamiento de pagos                   |
| Reportes  | Estadísticas y análisis                  |
| Historial | Registro de movimientos de inventario    |

### Roles y permisos

| Funcionalidad       | Administrador | Vendedor | Cajero |
|---------------------|---------------|----------|--------|
| CRUD Productos      | ✅            | ❌       | ❌     |
| Ver Productos       | ✅            | ✅       | ❌     |
| Gestionar Usuarios  | ✅            | ❌       | ❌     |
| Gestionar Bodegas   | ✅            | ❌       | ❌     |
| Crear Órdenes       | ✅            | ✅       | ❌     |
| Gestionar Órdenes   | ✅            | ✅       | ❌     |
| Ver Órdenes en Caja | ✅            | ❌       | ✅     |
| Procesar Pagos      | ✅            | ❌       | ✅     |
| Ver Reportes        | ✅            | ❌       | ❌     |
| Ver Historial       | ✅            | ❌       | ❌     |

---

## 🔄 Flujo del sistema

1. Vendedor crea una orden para un cliente
2. Vendedor busca y agrega productos a la orden
   - El stock se reserva automáticamente
3. Vendedor envía la orden a caja
4. Cajero ve la orden en su panel
5. Cajero procesa el pago (efectivo o tarjeta)
   - El stock se actualiza automáticamente
   - Se registra la venta en el historial
6. Si el cliente cancela → productos vuelven a disponible

### Estados de productos

disponible → reservado → vendido
     ↑____________|
   (si se cancela)

### Estados de órdenes

pendiente → en_proceso → enviada_a_caja → pagada
     └──────────────────────┘
              cancelada

---

## 🌐 API REST - Endpoints

Base URL: http://127.0.0.1:8000/api

### Autenticación

| Método | Endpoint  | Descripción        | Acceso      |
|--------|-----------|--------------------|-------------|
| POST   | /login    | Iniciar sesión     | Público     |
| POST   | /logout   | Cerrar sesión      | Autenticado |
| GET    | /me       | Usuario actual     | Autenticado |

Ejemplo de login:
POST /api/login
{
    "email": "admin@tienda.com",
    "password": "password"
}

Respuesta:
{
    "message": "Inicio de sesión exitoso.",
    "token": "1|xxxxxxxxxxxxx",
    "user": {
        "id": 1,
        "name": "Administrador",
        "email": "admin@tienda.com",
        "role": "administrador"
    }
}

### Productos

| Método | Endpoint               | Descripción        | Acceso          |
|--------|------------------------|--------------------|-----------------|
| GET    | /products              | Listar productos   | Admin, Vendedor |
| POST   | /products              | Crear producto     | Admin           |
| GET    | /products/{id}         | Ver producto       | Admin, Vendedor |
| PUT    | /products/{id}         | Actualizar producto| Admin           |
| DELETE | /products/{id}         | Eliminar producto  | Admin           |
| POST   | /products/{id}/reserve | Reservar producto  | Admin, Vendedor |

Filtros disponibles en GET /products:
  ?search=camiseta
  ?status=disponible|reservado|vendido
  ?category=Camisetas
  ?size=M
  ?color=Negro

### Usuarios

| Método | Endpoint     | Descripción       | Acceso |
|--------|--------------|-------------------|--------|
| GET    | /users       | Listar usuarios   | Admin  |
| POST   | /users       | Crear usuario     | Admin  |
| GET    | /users/{id}  | Ver usuario       | Admin  |
| PUT    | /users/{id}  | Actualizar usuario| Admin  |
| DELETE | /users/{id}  | Eliminar usuario  | Admin  |

### Bodegas

| Método | Endpoint          | Descripción       | Acceso |
|--------|-------------------|-------------------|--------|
| GET    | /warehouses       | Listar bodegas    | Admin  |
| POST   | /warehouses       | Crear bodega      | Admin  |
| GET    | /warehouses/{id}  | Ver bodega        | Admin  |
| PUT    | /warehouses/{id}  | Actualizar bodega | Admin  |
| DELETE | /warehouses/{id}  | Eliminar bodega   | Admin  |

### Órdenes

| Método | Endpoint                          | Descripción        | Acceso                    |
|--------|-----------------------------------|--------------------|---------------------------|
| GET    | /orders                           | Listar órdenes     | Admin, Vendedor, Cajero   |
| POST   | /orders                           | Crear orden        | Admin, Vendedor           |
| GET    | /orders/{id}                      | Ver orden          | Admin, Vendedor, Cajero   |
| POST   | /orders/{id}/items                | Agregar producto   | Admin, Vendedor           |
| DELETE | /orders/{id}/items/{itemId}       | Remover producto   | Admin, Vendedor           |
| POST   | /orders/{id}/send-to-cashier      | Enviar a caja      | Admin, Vendedor           |
| POST   | /orders/{id}/cancel               | Cancelar orden     | Admin, Vendedor           |

### Ventas

| Método | Endpoint                          | Descripción        | Acceso          |
|--------|-----------------------------------|--------------------|-----------------|
| GET    | /sales                            | Listar ventas      | Admin, Cajero   |
| GET    | /sales/{id}                       | Ver venta          | Admin, Cajero   |
| POST   | /orders/{id}/process-payment      | Procesar pago      | Admin, Cajero   |

### Reportes

| Método | Endpoint                          | Descripción              | Acceso |
|--------|-----------------------------------|--------------------------|--------|
| GET    | /reports/dashboard                | Resumen general          | Admin  |
| GET    | /reports/ventas-diarias           | Ventas por día           | Admin  |
| GET    | /reports/productos-mas-vendidos   | Top productos vendidos   | Admin  |
| GET    | /reports/inventario               | Estado del inventario    | Admin  |

### Historial

| Método | Endpoint        | Descripción          | Acceso |
|--------|-----------------|----------------------|--------|
| GET    | /history        | Listar movimientos   | Admin  |
| GET    | /history/{id}   | Ver movimiento       | Admin  |

Filtros disponibles en GET /history:
  ?action=reservado|vendido|cancelado|liberado
  ?product_id=1

---

## 🗄️ Base de datos

### Tablas principales

| Tabla        | Descripción                              |
|--------------|------------------------------------------|
| users        | Usuarios del sistema con roles           |
| products     | Productos con control de stock           |
| warehouses   | Ubicaciones en bodega                    |
| orders       | Órdenes de clientes                      |
| order_items  | Productos dentro de cada orden           |
| sales        | Ventas procesadas                        |
| sale_items   | Productos dentro de cada venta           |
| history      | Historial de movimientos de inventario   |

---

## 🔒 Seguridad

- Autenticación mediante Laravel Sanctum (tokens Bearer)
- Control de acceso por roles con middleware personalizado
- Validación de datos en todos los endpoints
- Contraseñas encriptadas con bcrypt
- Protección CSRF en formularios web
- Consultas preparadas mediante Eloquent ORM

---

## 🛠️ Tecnologías utilizadas

| Tecnología       | Versión | Uso                  |
|------------------|---------|----------------------|
| Laravel          | 12.x    | Framework backend    |
| PHP              | 8.2     | Lenguaje backend     |
| MySQL            | 8.x     | Base de datos        |
| Laravel Sanctum  | 4.x     | Autenticación API    |
| Bootstrap        | 5.3     | Framework CSS        |
| Bootstrap Icons  | 1.11    | Iconografía          |

---
