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
- Sử dụng `Route::middleware('auth:sanctum')` để bảo vệ route.

### 2. Form Request (`app/Http/Requests`)

- **Nhiệm vụ:** Chỉ thực hiện Validation và Authorization.
- **Authorization:** Chú ý logic kiểm tra quyền trong hàm `authorize()`.
- **Validation:**
    - Kiểm tra phương thức request (`$this->isMethod('post')` hay `put`) để trả về mảng rule tương ứng.
    - Xử lý cực kỳ cẩn thận với luật `unique` trong phương thức update, phải tự động lấy ID từ Route Model Binding để bỏ qua bản ghi hiện tại.
    - Khai báo custom messages nếu dự án yêu cầu đa ngôn ngữ hoặc thông báo cụ thể.

### 3. DTO - Data Transfer Object (`app/DTOs`)

- **Nhiệm vụ:** Đóng gói và chuẩn hóa dữ liệu từ Request trước khi đưa vào Service.
- **Quy tắc:**
    - Dùng `readonly class` (PHP 8.2+).
    - Khai báo constructor dùng Constructor Property Promotion với type-hint rõ ràng (`int`, `string`, `?string`).
    - Viết method static `fromRequest(FormRequest $request): self` để khởi tạo DTO trực tiếp từ Request. Trong method này chỉ lấy dữ liệu đã qua validate bằng `$request->validated()`.
    - KHÔNG truyền ID lấy từ URL Parameter vào trong properties của DTO. ID nên được Controller truyền trực tiếp làm tham số độc lập vào Service.

### 4. Controller (`app/Http/Controllers`)

- **Nhiệm vụ:** Điều hướng request và response. **KHÔNG CHỨA BUSINESS LOGIC**.
- **Quy tắc:**
    - Inject Service thông qua `Interface` ở constructor.
    - Tận dụng triệt để Route Model Binding: Khi Controller đã nhận được Model (VD: `update(CategoryRequest $request, Category $category)`), hãy truyền thẳng đối tượng Model này vào Service thay vì truyền tham số ID, qua đó tránh cho Service phải query database lại lần 2.
    - Response luôn trả về thông qua lớp `Resource`. Đảm bảo format JSON nhất quán (VD: có keys `data`, `message`, `status`).

### 5. Service & Interface (`app/Services` & `app/Contracts`)

- **Nhiệm vụ:** Xử lý nghiệp vụ lõi (Business Logic).
- **Quy tắc:**
    - Phải có file `Interface` định nghĩa contract rõ ràng tại `app/Contracts` (Ví dụ: `ProductServiceInterface`).
    - Các hàm như `findById`, `update`, `delete` nên nhận tham số trực tiếp là đối tượng Model (Ví dụ: `public function findById(Category $category)`) nếu Model đã được Controller lấy sẵn thông qua Route Model Binding.
    - Type-hint DTO cụ thể ở các tham số thay vì dùng kiểu `object` chung chung. (Ví dụ: `public function create(CategoryDTO $dto)`).
    - Tối ưu truy vấn (Tránh N+1 query, tuyệt đối không gọi `Model::findOrFail()` bên trong Service nếu đã được truyền trực tiếp Model vào).
    - Chỉ gán và cập nhật các trường được truyền thông qua DTO để phòng tránh Mass Assignment.

### 6. API Resource (`app/Http/Resources`)

- **Nhiệm vụ:** Chuyển đổi Eloquent Model thành JSON Object chuẩn bị trả về.
- **Quy tắc:** Mapping thủ công từng field cần thiết, ẩn đi các field nhạy cảm hoặc thừa thãi. Không dùng `$this->toArray($request)` nếu không cần xuất toàn bộ bảng.
