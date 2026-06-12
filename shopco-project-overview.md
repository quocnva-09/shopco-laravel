# ShopCo Platform - Project Overview & Tech Stack

This document serves as a comprehensive guide to the ShopCo architecture, technology stack, and module workflows. It is intended to help developers (specifically the Frontend team) understand the system's inner workings.

> [!TIP]
> **Swagger/OpenAPI UI** is available at `/api/documentation`. Use this interactive UI as your primary reference for exact request payloads and schemas.

---

## 1. Technology Stack

### Backend Stack

- **Framework:** Laravel 11/13 (PHP 8.2+)
- **Database:** MySQL 8.x
- **Caching & Queue:** Redis (Used for extremely fast OTP lookups, caching catalog queries, and processing background jobs).
- **Authentication:** Laravel Sanctum (Token-based API Authentication).
- **API Documentation:** L5-Swagger (OpenAPI 3.0 generation).
- **Mail Server:** SMTP (Integrated with background queue processing to prevent API blockages).

---

## 2. Architectural Patterns

The backend rigorously follows modern, maintainable design patterns:

- **"Thin Controller, Fat Service":** Controllers only handle HTTP requests and responses. All business logic is strictly encapsulated inside dedicated Service classes (e.g., `GuestOrderService`, `ProductService`).
- **Data Transfer Objects (DTOs):** Incoming request data is parsed into heavily-typed DTOs before being passed to Service classes. This ensures type safety across the application.
- **Form Requests:** All endpoint validation (required fields, array structures, email formatting) happens at the Request layer before hitting the controller.
- **Repository Pattern:** Database interactions are abstracted away via Repositories (`ProductRepository`, `OrderRepository`), allowing for clean and testable database queries.
- **Enums:** Statuses and configurations are strongly typed using PHP Enums (e.g., `OrderStatus::PENDING`).

---

## 3. Deep Dive: Core Modules

### 🛍️ Product Catalog Module

Handles everything related to displaying products to users.

- **Entities:** `Category`, `Product`, `ProductVariant`, `ProductImage`, `Color`, `Size`.
- **Variants:** Products have variants that can be resolved in two ways during checkout:
    - **Mode 1:** Passing a specific `product_variant_id`.
    - **Mode 2:** Passing a `color_id` and `size_id` (the backend will dynamically find the correct variant).
- **Caching:** Expensive public listing queries are cached in Redis.

### 🛒 Shopping Cart Module

Manages the user's pre-checkout state.

- _Requires Authentication._
- **Entities:** `Cart`, `CartItem`.
- **Workflow:** The cart is maintained entirely on the backend server. The frontend can add/update/remove items via `/api/cart/*`.
- **Security:** The backend completely ignores any price sent by the frontend. Prices are always re-calculated from the database `Product` table during cart operations.

### 📦 Order & Checkout Module

This is the most complex module, branching into two distinct workflows:

#### Flow A: Authenticated Checkout

- Uses the saved database Cart.
- Orders are automatically attached to the `user_id`.

#### Flow B: Guest Checkout

- **Cartless:** The payload contains an array of `items` and guest shipping information directly.
- **Anti-Spam & Security (OTP):**
    1. The order is inserted into the DB with the status `OrderStatus::NOT_VERIFY`.
    2. A random 6-digit OTP is generated and cached in Redis with a strict 5-minute TTL.
    3. An email is dispatched asynchronously to the guest.
    4. The frontend redirects to an OTP screen and calls `/api/guest/orders/{order_id}/verify-otp`.
    5. Upon success, the OTP is deleted, and the order status becomes `pending`.
- **Abandoned Orders:** Unverified orders (`not_verify` status) older than 24 hours are automatically purged by a scheduled backend Cron Job (`orders:cleanup-unverified`).

### ⭐ Review & Rating Module

Handles user-generated feedback on products.

- **Entities:** `Review`.
- **Submission:**
    - Authenticated users link reviews via `order_item_id`.
    - Guest users submit via a special `/api/guest/reviews` route utilizing their specific `order_id`.
- **Moderation Workflow:** All submitted reviews default to `is_approved = false`. They will not be sent to the frontend product details page until an administrator explicitly approves them. Ratings (`int` -> `float`) support half-star precise values (e.g., `4.5`).

### 📁 File & Media Upload Module

Centralized module for handling binary file uploads across the application.

- **Endpoints:** `/api/users/upload` (for user avatars) and `/api/admin/products/upload` (for product imagery).
- **Storage:** Files are securely stored, and the endpoints return the accessible public URL path of the uploaded file so the frontend can immediately display the preview or attach the path to a database entity.

### 📊 Data Export Module

Designed for Admin reporting, allowing robust extraction of system data into CSV/Excel formats.

- **Entities:** `ExportHistory`.
- **Asynchronous Workflow:** Generating large exports synchronously can freeze the server, so exports are handled via background queues:
    1. Admin requests an export (e.g., `POST /api/admin/exports/products`).
    2. The backend dispatches a background job and immediately returns an `ExportHistory` record with a `pending` status.
    3. The backend queue worker generates the file in the background. Once finished, the status updates to `completed`.
    4. The frontend can query `GET /api/admin/exports` and provide a secure download link (`/api/admin/exports/{id}/download`) to the admin once ready.

### 🔐 User & Authentication Module

Manages identity and access.

- **Token Management:** Upon login, Sanctum generates a Bearer token.
- **Account Recovery:** The Forgot Password flow mimics the guest checkout flow by sending a 6-digit OTP to the user's email to verify identity before allowing a password reset.
- **Admin Access:** Certain users have roles/permissions that allow them to access the `/api/admin/*` suite, which is guarded by a specific Admin Middleware.
