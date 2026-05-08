---
name: laravel-api-crud
description: Hướng dẫn tạo luồng API CRUD chuẩn xác với kiến trúc Request, DTO, Service, và Resource cho Laravel.
---

# Laravel Clean API CRUD Pattern

Mục đích của skill này là định hướng cho AI agent cách xây dựng cấu trúc API CRUD chuẩn, dễ bảo trì, bảo mật cao và hiệu suất tốt cho các ứng dụng Laravel, kế thừa từ cấu trúc hiện có.

## Kiến trúc luồng dữ liệu (Data Flow)

Mọi chức năng CRUD (Create, Read, Update, Delete) phải tuân theo luồng nghiêm ngặt sau:
`Route` ➡️ `Form Request` ➡️ `DTO` ➡️ `Controller` ➡️ `Service Interface` ➡️ `Service Implementation` ➡️ `API Resource` ➡️ `Response`

## Quy tắc Implement Chi Tiết

### 1. Routes (`routes/api.php`)

- Sử dụng `Route::apiResource()` cho các route CRUD tiêu chuẩn.
- Gom nhóm bằng `Route::prefix` và sử dụng tên route số nhiều (VD: `categories`, `products`).
- Sử dụng `Route::middleware('auth:sanctum')` để bảo vệ route nếu cần.
- **Lưu ý Soft Delete**: Tính năng này (như `trashed`, `restore`, `force-delete`) **chỉ implement khi thực sự cần thiết**. Tuy nhiên, một khi đã khai báo route cho các chức năng này thì **bắt buộc** phải triển khai đầy đủ logic trong Controller và Service để tránh lỗi `BadMethodCallException`.

### 2. Form Request (`app/Http/Requests`)

- **Nhiệm vụ:** Chỉ thực hiện Validation và Authorization.
- **Authorization:** Xử lý logic kiểm tra quyền trong hàm `authorize()` (ví dụ check Admin, Ownership).
- **Validation:**
  - Khai báo rules theo dạng **mảng** (array) thay vì string (VD: `['required', 'string', 'max:255']`) để dễ bảo trì và đọc hiểu.
  - Phân tách rules dựa trên phương thức request (POST hoặc PUT/PATCH).
  - Với logic `unique` khi update: tự động bỏ qua ID của bản ghi hiện tại qua hàm route parameter. VD: `Rule::unique('table')->ignore($this->route('model'))`.
  - Khai báo class riêng `TemplateFilterRequest` dành cho các API dạng `GET /index` chứa logic rules tìm kiếm và phân trang.

### 3. DTO - Data Transfer Object (`app/DTOs`)

- **Nhiệm vụ:** Đóng gói và chuẩn hóa dữ liệu từ Request trước khi đưa vào Service.
- **Quy tắc:**
  - Dùng `readonly class` (PHP 8.2+).
  - Khai báo constructor dùng Constructor Property Promotion với type-hint.
  - Method static `fromRequest(FormRequest $request): self` chỉ lấy dữ liệu hợp lệ `$request->validated()`.
  - Tách riêng `TemplateDTO` (chứa data payload CRUD) và `TemplateFilterDTO` (chứa query constraints cho list: keyword, sort, limit).
  - Không nhét URL param `id` vào DTO; truyền thẳng object Model từ Route Model Binding xuống Service.

### 4. Controller (`app/Http/Controllers`)

- **Nhiệm vụ:** Nhận request, gọi Service và chuẩn hóa Response. **KHÔNG CHỨA BUSINESS LOGIC**.
- **Quy tắc:**
  - Inject Dependency thông qua `Interface` ở constructor.
  - Tận dụng triệt để **Route Model Binding**.
  - **Sử dụng `ApiResponseTrait`**: Thay vì viết hardcode `response()->json(...)` lặp lại, hãy dùng trait (`successResponse`, `paginatedResponse`, `errorResponse`) để định dạng JSON trả về chuẩn nhất quán.

### 5. Service & Interface (`app/Services` & `app/Contracts`)

- **Nhiệm vụ:** Xử lý nghiệp vụ lõi (Business Logic).
- **Quy tắc:**
  - Có Interface đi kèm.
  - Type-hint đúng DTO. Nhận vào tham số là Object Model nếu đã được Binding.
  - **Dynamic Ordering & Enum**: Tránh hardcode giá trị string khi sort (`if ($sort == 'name')`). Hãy dùng **Enums** để định nghĩa danh sách các trường được phép sort/filter. Điều này giúp code dễ mở rộng, an toàn và đồng nhất.
  - Tối ưu hóa Database queries, tránh N+1. Tận dụng `$model->load('relations')`.

### 6. API Resource (`app/Http/Resources`)

- **Nhiệm vụ:** Chuyển đổi Eloquent Model thành JSON Object trả về frontend.
- **Quy tắc:** Mapping thủ công field, ẩn metadata/fields nhạy cảm.

## File Mẫu (References)

Trong quá trình khởi tạo một CRUD module mới (ví dụ `Post`), hãy tham khảo bộ template sau trong thư mục `references/`:
- `TemplateController.php`
- `TemplateService.php`
- `TemplateServiceInterface.php`
- `TemplateDTO.php` & `TemplateFilterDTO.php`
- `TemplateRequest.php` & `TemplateFilterRequest.php`
- `TemplateResource.php`
