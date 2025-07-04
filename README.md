# 🍽️ Sistema de Gestión de Pedidos para Restaurante 🍽️

[![PHP Version](https://img.shields.io/badge/php-%3E%3D8.1-8892BF.svg)](https://php.net)
[![Laravel Version](https://img.shields.io/badge/laravel-Framework-FF2D20.svg)](https://laravel.com)
[![FilamentPHP](https://img.shields.io/badge/filament-Admin%20Panel-F59E0B.svg)](https://filamentphp.com)
[![License: MIT](https://img.shields.io/badge/License-MIT-yellow.svg)](https://opensource.org/licenses/MIT)

Este proyecto es un sistema de gestión de pedidos para restaurantes desarrollado con **Laravel** y **FilamentPHP**. Permite administrar categorías de productos, productos, mesas, clientes y pedidos, tanto para consumo en local como para llevar.

## ✨ Características Principales

*   **Gestión de Categorías:** Crear, editar y eliminar categorías para organizar los productos.
*   **Gestión de Productos:** Añadir nuevos productos con nombre, precio y asignarlos a una categoría.
*   **Gestión de Mesas:** Administrar las mesas del restaurante, incluyendo su número y estado (disponible, ocupada, atendida).
*   **Gestión de Clientes:** Registrar clientes para pedidos para llevar.
*   **Gestión de Pedidos:**
    *   Crear pedidos seleccionando el tipo (consumo en local o para llevar).
    *   Asignar mesas a pedidos en local.
    *   Asignar clientes a pedidos para llevar.
    *   Añadir múltiples productos a un pedido, especificando la cantidad.
    *   Calcular automáticamente el subtotal por producto y el total del pedido.
    *   Seguimiento del estado del pedido (pendiente ➡️ en preparación ➡️ listo ➡️ entregado / cancelado).
    *   Acciones rápidas para cambiar el estado de los pedidos (Preparar, Marcar Listo, Entregar, Cancelar).
*   **Interfaz de Cocina 🧑‍🍳:** Una vista especializada (`/cocina`) para que el personal de cocina pueda ver y gestionar los pedidos pendientes y en preparación.

## 🛠️ Tecnologías Utilizadas

*   **Laravel:** Framework PHP robusto y elegante para el desarrollo de aplicaciones web.
*   **FilamentPHP:** Un panel de administración para Laravel que permite crear interfaces de usuario de forma rápida y eficiente.
*   **PHP**
*   **MySQL** (o la base de datos configurada en Laravel)
*   **Tailwind CSS** (a través de Filament)

## 📋 Requisitos Previos

*   PHP >= 8.1
*   Composer
*   Node.js & NPM (para assets de frontend, si se modifican)
*   Servidor web (Nginx, Apache)
*   Base de datos (MySQL, PostgreSQL, SQLite, etc.)

## 🚀 Instalación

1.  **Clonar el repositorio:**
    ```bash
    git clone https://URL_DEL_REPOSITORIO_AQUI
    cd NOMBRE_DEL_DIRECTORIO_DEL_PROYECTO
    ```

2.  **Instalar dependencias de PHP:**
    ```bash
    composer install
    ```

3.  **Copiar el archivo de entorno:**
    ```bash
    cp .env.example .env
    ```

4.  **Generar la clave de la aplicación:**
    ```bash
    php artisan key:generate
    ```

5.  **Configurar la base de datos:**
    Abre el archivo `.env` y configura los detalles de tu base de datos (DB_CONNECTION, DB_HOST, DB_PORT, DB_DATABASE, DB_USERNAME, DB_PASSWORD).

6.  **Ejecutar las migraciones y seeders (si existen seeders para datos iniciales):**
    ```bash
    php artisan migrate --seed
    ```
    *(Nota: Este proyecto actualmente no tiene seeders específicos más allá del `UserFactory` por defecto de Laravel. Puedes crear un usuario administrador a través de Filament o usando `php artisan make:filament-user`)*

7.  **Crear un usuario administrador para Filament (si no lo hiciste con seeders):**
    ```bash
    php artisan make:filament-user
    ```
    Sigue las instrucciones para crear tu cuenta de administrador.

8.  **Configurar el servidor web:**
    Asegúrate de que tu servidor web apunte al directorio `public` del proyecto. (Ej. `/var/www/html/NOMBRE_DEL_DIRECTORIO_DEL_PROYECTO/public`)

9.  **(Opcional) Compilar assets de frontend (si has modificado archivos JS/CSS):**
    ```bash
    npm install
    npm run dev # o npm run build para producción
    ```

## 💻 Uso

1.  Accede a la URL de tu proyecto en el navegador.
2.  Para acceder al panel de administración, navega a `/admin` (o la ruta que hayas configurado para Filament).
3.  Inicia sesión con el usuario administrador creado durante la instalación.
4.  Desde el panel de administración podrás gestionar:
    *   **Categorías:** `/admin/categorias`
    *   **Productos:** `/admin/productos`
    *   **Mesas:** `/admin/mesas`
    *   **Clientes:** `/admin/clientes`
    *   **Pedidos:** `/admin/pedidos`
    *   **Cocina 🧑‍🍳:** `/admin/pedidos/cocina` (Vista especial para la gestión de pedidos en cocina)

## 📂 Estructura del Proyecto (Resumen)

*   `app/Filament/Resources/`: Contiene la lógica y definición de los recursos de Filament (cómo se muestran y gestionan los modelos en el panel de administración).
    *   `CategoriaResource.php` 📁
    *   `ClienteResource.php` 👤
    *   `MesaResource.php` 🍽️
    *   `PedidoResource.php` 🧾
        *   `Pages/Cocina.php`: Página personalizada para la vista de cocina.
    *   `ProductoResource.php` 🍔
*   `app/Http/Controllers/`: Controladores estándar de Laravel.
*   `app/Models/`: Modelos Eloquent (representación de tablas de BD).
    *   `Categoria.php` 📁
    *   `Cliente.php` 👤
    *   `DetallePedido.php` (Tabla pivot Pedidos-Productos)
    *   `Mesa.php` 🍽️
    *   `Pedido.php` 🧾
    *   `Producto.php` 🍔
    *   `User.php` 🧑‍💻
*   `database/migrations/`: Migraciones para la estructura de la BD.
*   `routes/web.php`: Rutas web. (Filament registra las suyas).

## 📜 Licencia

Este proyecto es un software de código abierto licenciado bajo la [MIT license](https://opensource.org/licenses/MIT).

---

<p align="center">Este proyecto fue desarrollado utilizando Laravel y FilamentPHP.</p>
<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="200" alt="Laravel Logo"></a> &nbsp; <a href="https://filamentphp.com" target="_blank"><img src="https://filamentphp.com/images/logo.svg" width="200" alt="Filament Logo"></a></p>
