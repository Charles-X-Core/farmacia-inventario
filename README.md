# Dielventory - Sistema de Gestión de Inventario

## Requisitos previos
- PHP >= 8.1
- Composer
- MySQL o MariaDB
- Node.js y npm (opcional, si usas frontend adicional)

## Instalación

1. Clona el repositorio o descarga el código
2. Instala las dependencias de Composer:
   ```bash
   composer install
   ```

3. Copia el archivo de configuración de ejemplo:
   ```bash
   cp .env.example .env
   ```

4. Genera la clave de aplicación:
   ```bash
   php artisan key:generate
   ```

5. Configura la base de datos en el archivo `.env`:
   ```
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=dielventory
   DB_USERNAME=root
   DB_PASSWORD=
   ```

## Migraciones y Seeders

### Ejecutar migraciones
Crea las tablas en la base de datos:
```bash
php artisan migrate
```

### Ejecutar Seeders
Existen 3 seeders adicionales que debes ejecutar individualmente:

1. TypeManagementSeeder (gestión de tipos: LIBRE, LOTES):
   ```bash
   php artisan db:seed --class=TypeManagementSeeder
   ```

2. TypeDocumentsSeeder:
   ```bash
   php artisan db:seed --class=TypeDocumentsSeeder
   ```

3. MovementTypeSeeder:
   ```bash
   php artisan db:seed --class=MovementTypeSeeder
   ```

O, si prefieres ejecutar todos los seeders a la vez (incluyendo DatabaseSeeder):
```bash
php artisan db:seed
```

## Ejecución del proyecto

Inicia el servidor de desarrollo:
```bash
php artisan serve
```

El proyecto estará disponible en `http://localhost:8000`

## Funcionalidades principales

- **Productos**: CRUD de productos con gestión por lotes o libre
- **Movimientos**: Ingresos y salidas de mercadería
- **Lotes (Allotments)**: Gestión de lotes con fecha de vencimiento y días restantes
- **Almacenes y Sucursales**: Gestión de almacenes y sucursales
- **Autenticación**: Sistema de login con roles (admin y usuario normal)

## Frontend básico
Se incluye un frontend básico en `public/index.html` para probar las funcionalidades:
- Iniciar sesión
- Crear productos
- Registrar ingresos y salidas de mercadería
- Ver listado de productos y lotes
