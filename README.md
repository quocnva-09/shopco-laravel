<div align="center">

# 🛍️ Shopco Laravel — Backend API

[![PHP](https://img.shields.io/badge/PHP-8.3%2B-777BB4?style=for-the-badge&logo=php&logoColor=white)](https://www.php.net/)
[![Laravel](https://img.shields.io/badge/Laravel-13.x-FF2D20?style=for-the-badge&logo=laravel&logoColor=white)](https://laravel.com/)
[![License](https://img.shields.io/badge/License-MIT-green?style=for-the-badge)](LICENSE)
[![Docker](https://img.shields.io/badge/Docker-Ready-2496ED?style=for-the-badge&logo=docker&logoColor=white)](https://hub.docker.com/r/anhquoc09/shopco-laravel)
[![MySQL](https://img.shields.io/badge/MySQL-8.0-4479A1?style=for-the-badge&logo=mysql&logoColor=white)](https://www.mysql.com/)

**A production-ready RESTful API backend for the Shopco e-commerce platform.**  
Built with Laravel 13, secured with Laravel Sanctum, and containerized with Docker.

</div>

---

## 📖 Overview

Shopco Laravel Backend powers all server-side operations for the Shopco e-commerce platform. It provides a structured, token-authenticated REST API covering user authentication, product & category management, shopping cart, order lifecycle management, and asynchronous CSV/Excel product export — all served behind a clean, role-based access control layer separating regular users from administrators.

---

## ✨ Core Features

| Feature                    | Description                                                                  |
| -------------------------- | ---------------------------------------------------------------------------- |
| 🔐 **Authentication**      | Registration, Login, Logout via Laravel Sanctum (Bearer Token)               |
| 👤 **User Management**     | Admin CRUD with soft-delete, restore, and force-delete                       |
| 📦 **Product Management**  | Full CRUD with image upload, soft-delete, and search/filter/pagination       |
| 🗂️ **Category Management** | Full CRUD with soft-delete, restore, and force-delete (admin)                |
| 🛒 **Shopping Cart**       | Add, update, remove items, count, and clear cart per user                    |
| 📋 **Order Management**    | Place orders, track status, admin order overview and status updates          |
| 📤 **Export**              | Async product export jobs to CSV/XLSX via queue worker                       |
| 📄 **API Documentation**   | Interactive Swagger UI via `darkaonline/l5-swagger`                          |
| 🐳 **Docker Ready**        | Multi-service `docker-compose` with App, MySQL, Queue Worker, and phpMyAdmin |

---

## 🛠️ Tech Stack

| Layer                | Technology                                       |
| -------------------- | ------------------------------------------------ |
| **Language**         | PHP 8.3+                                         |
| **Framework**        | Laravel 13.x                                     |
| **Authentication**   | Laravel Sanctum 4.x                              |
| **Database**         | MySQL 8.0 (Production)                           |
| **Queue**            | Database queue (configurable to Redis in future) |
| **Export**           | Maatwebsite Excel 3.1 (CSV / XLSX)               |
| **API Docs**         | DarkaOnline L5-Swagger 11.x (OpenAPI 3.0)        |
| **Testing**          | PHPUnit 12.x                                     |
| **Containerization** | Docker + Docker Compose                          |

---

## 🔗 Important Links

- 📚 **GitHub Pages (Danh sách bài tập):** [https://quocnva-09.github.io/](https://quocnva-09.github.io/)
- 💻 **Source Code (shopco-laravel):** [https://github.com/quocnva-09/shopco-laravel](https://github.com/quocnva-09/shopco-laravel)
- 🚀 **Production API (AWS EC2):** [https://api.quocnva09.me/](https://api.quocnva09.me/)
- 🐳 **Docker Hub Image:** [https://hub.docker.com/r/anhquoc09/shopco-laravel](https://hub.docker.com/r/anhquoc09/shopco-laravel)
- 📬 **Postman Collection:** [View API Documentation](https://documenter.getpostman.com/view/38748390/2sBXqNnya5)
- 📘 **Swagger UI:** _Đang cập nhật_

---

## 🐳 Setup — With Docker

### Option A: Using Docker Compose (Recommended)

This is the recommended approach. It starts the app server, a dedicated queue worker, MySQL 8.0, and phpMyAdmin together.

**1. Clone the repository and create your `.env` file:**

```bash
git clone https://github.com/quocnva-09/shopco-laravel.git
cd shopco-laravel
cp .env.example .env
```

**2. Set the database credentials in `.env` to match the Docker services:**

```dotenv
DB_CONNECTION=mysql
DB_HOST=shopco_db
DB_PORT=3306
DB_DATABASE=shopco
DB_USERNAME=root
DB_PASSWORD=your_secure_password
```

**3. Build and start all containers:**

```bash
docker compose up -d --build
```

**4. Run migrations inside the running container:**

```bash
docker compose exec app php artisan migrate --seed
```

**5. Generate Swagger documentation:**

```bash
docker compose exec app php artisan l5-swagger:generate
```

| Service        | URL                                       |
| -------------- | ----------------------------------------- |
| **API**        | `http://localhost:8000/api`               |
| **Swagger UI** | `http://localhost:8000/api/documentation` |
| **phpMyAdmin** | `http://localhost:8081`                   |

---

### Option B: Using the Pre-built Docker Hub Image

**1. Pull the image:**

```bash
docker pull anhquoc09/shopco-laravel:latest
```

**2. Run the container (pass all required environment variables):**

```bash
docker run -d \
  --name shopco_app \
  -p 8000:80 \
  -e APP_KEY=base64:YOUR_KEY_HERE \
  -e APP_ENV=production \
  -e DB_CONNECTION=mysql \
  -e DB_HOST=your_db_host \
  -e DB_PORT=3306 \
  -e DB_DATABASE=shopco \
  -e DB_USERNAME=root \
  -e DB_PASSWORD=your_db_password \
  -e QUEUE_CONNECTION=database \
  anhquoc09/shopco-laravel:latest
```

> ⚠️ **Note:** When using the standalone image, you must provide an external MySQL instance. Use `docker-compose.yaml` for a self-contained local stack.

---

## 📨 Queue & Email Setup

### Email Configuration

The application uses Laravel's mail system. Update the following variables in `.env`:

```dotenv
MAIL_MAILER=smtp          # Use 'log' for local development (no emails sent)
MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=your_mailtrap_username
MAIL_PASSWORD=your_mailtrap_password
MAIL_FROM_ADDRESS="noreply@shopco.me"
MAIL_FROM_NAME="Shopco"
```

> 💡 **Tip:** Use [Mailtrap.io](https://mailtrap.io) for local email testing.  
> Set `MAIL_MAILER=log` to only log emails to `storage/logs/laravel.log` without sending.

---

### Queue Configuration

The default queue driver is **`database`**. All export jobs are dispatched to this queue.

```dotenv
QUEUE_CONNECTION=database   # Options: sync | database | redis
```

**To use Redis (recommended for production):**

```dotenv
QUEUE_CONNECTION=redis
REDIS_HOST=127.0.0.1
REDIS_PORT=6379
REDIS_PASSWORD=null
```

**Starting the queue worker:**

```bash
# Standard worker (processes one job at a time)
php artisan queue:work

# Inside Docker (already handled by the 'worker' service)
docker compose exec worker php artisan queue:work

# With retry and timeout settings for production
php artisan queue:work --tries=3 --timeout=90 --sleep=3
```

> The `docker-compose.yaml` includes a dedicated **`shopco_worker`** container that runs `php artisan queue:work` automatically.

---

## 📋 API Summary

| Module         | Endpoint Prefix               | Auth Required           | Role           |
| -------------- | ----------------------------- | ----------------------- | -------------- |
| Auth           | `/api/login`, `/api/register` | No                      | Public         |
| Auth           | `/api/logout`, `/api/me`      | Yes                     | User           |
| Categories     | `/api/categories`             | No (read) / Yes (write) | Public / Admin |
| Products       | `/api/products`               | No (read) / Yes (write) | Public / Admin |
| Cart           | `/api/cart`                   | Yes                     | User           |
| Orders         | `/api/orders`                 | Yes                     | User           |
| Admin — Users  | `/admin/users`                | Yes                     | Admin          |
| Admin — Orders | `/admin/orders`               | Yes                     | Admin          |
| Admin — Export | `/api/exports`                | Yes                     | Admin          |

---

## 🧪 Running Tests

```bash
# Run all tests
php artisan test

# Or using Composer script
composer test
```

---

## 📂 Project Structure (Key Directories)

```
app/
├── Contracts/Services/     # Service interfaces
├── DTOs/                   # Data Transfer Objects
├── Enums/                  # PHP 8.1+ Enums (OrderStatus, ExportStatus)
├── Http/
│   ├── Controllers/Api/    # Thin controllers with OA Attributes
│   ├── Requests/           # FormRequests with validation & OA Schemas
│   └── Resources/          # API Resources with OA Schemas
├── Models/                 # Eloquent models
└── Services/               # Business logic layer
routes/
├── api.php                 # User-facing API routes
└── admin.php               # Admin-protected routes
```

---

## 📄 License

This project is open-sourced under the [MIT license](LICENSE).
