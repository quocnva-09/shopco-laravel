# Đánh giá CRUD Category

## 1. Validation Request

- **Ưu điểm**:
    - Đã tách riêng form request (`CategoryRequest`, `CategoryFilterRequest`).
    - Xử lý check `unique:categories` khá tốt khi update bằng cách bỏ qua ID hiện tại (`$categoryId = $this->route('category') ?? null;`).
    - Phân tách rules rành mạch giữa `POST` và `PUT/PATCH`.
- **Thiếu sót/Gợi ý**:
    - Có thể thêm xử lý tự động tạo slug nếu request không gửi lên (mặc dù hiện tại required).
    - Khuyến khích define format rule rõ ràng thay vì dùng string (dùng array `['required', 'string', 'max:100']` dễ maintain hơn).

## 2. Chuẩn hóa Resource trả về

- **Ưu điểm**: Đã dùng `CategoryResource` để format data trả về. Xử lý phân trang có bọc thêm meta, links.
- **Thiếu sót/Lỗi**:
    - Code format response bị lặp lại ở mọi controller (VD: `return response()->json(['data' => ..., 'message' => ..., 'status' => ...]);`). Nên đưa vào 1 Trait (ví dụ `ApiResponseTrait`) hoặc BaseController để tái sử dụng.
    - Phân trang ở `index` phải bóc tách thủ công từ `$resource['data']`, `$resource['meta']`, `$resource['links']`.

## 3. Trách nhiệm của Service/Controller

- **Ưu điểm**: Controller gọi qua Service, Service nhận DTO để xử lý. Dependency Injection tốt.
- **Thiếu sót/Lỗi nghiêm trọng (Syntax/Logic)**:
    - Trong `routes/admin.php` có khai báo 3 routes: `trashed`, `{id}/restore`, `{id}/force-delete` trỏ tới `CategoryController`. Tuy nhiên, trong `CategoryController` **hoàn toàn không định nghĩa** 3 function này. Sẽ bị lỗi `BadMethodCallException` nếu call api.
    - [FIX WITH ENUM] Service lấy filter sort cứng `in_array($filter->sort, ['name', 'created_at'])`. Nếu mở rộng cột cần sort sẽ phải sửa ở Service.

## 4. Model Fillable

- Đã config đúng các trường `$fillable = ['name', 'description', 'slug']`.
- Đã import và sử dụng trait `SoftDeletes`.

## 5. Xử lý Exception

- Ném ra `ModelNotFoundException` tại method `findById()` nhờ hàm `findOrFail()`. Global Exception Handler bắt được và trả về 404 là đúng chuẩn.
