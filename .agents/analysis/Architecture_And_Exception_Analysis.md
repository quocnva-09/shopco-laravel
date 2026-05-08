# Đánh giá chung Architecture & Exception (Dựa theo CRUD)

## 1. Thiết kế Architecture chung (Controller -> Service -> Repo/Model)
- **Điểm sáng**: 
  - Dự án triển khai mô hình DTO (Data Transfer Object) để truyền data giữa Request và Service cực kì bài bản (VD: `ProductDTO::fromRequest()`).
  - Phân tách rõ ràng layer Interface (`ProductServiceInterface`) và Implementation (`ProductService`).
- **Điểm yếu**:
  - Không có BaseResponseTrait hoặc ResponseHelper. Tất cả các method controller đang lặp đi lặp lại khối lệnh:
    ```php
    return response()->json([
        'data' => ...,
        'message' => ...,
        'status' => ...
    ]);
    ```
    Nên gom logic này lại thành 1 hàm duy nhất ví dụ `$this->successResponse($data, 'Message')`.
  - Chưa sử dụng Repository Pattern toàn diện, Service vẫn đang query trực tiếp qua Model (`Product::query()`). Nếu dự án không quá lớn thì đây cũng không hẳn là lỗi, mà là lựa chọn thiết kế.

## 2. Global Exception Handling (Bắt lỗi toàn cục)
- **Ưu điểm**:
  - Đã khai báo catch exception tại `app/Exceptions/GlobalExceptionHandler.php` cho các lỗi phổ biến (401, 403, 404, 422, 405, 500).
- **Vấn đề tiềm ẩn**:
  - Bạn có bắt exception tổng `Exception $exception` thành status 500 kèm message mặc định. Ở môi trường development (khi `APP_DEBUG=true`), làm như thế này sẽ làm mất đi Stack Trace của Laravel, khiến developer rất khó debug khi code bị crash. Nên thêm điều kiện: `if (config('app.debug'))` thì văng ra full trace, còn lại trên Production thì mới giấu lỗi đi.

## 3. Lỗi Controller thiếu hàm (Rất nguy hiểm)
- Dựa trên `routes/admin.php`, **Category** khai báo 3 endpoint:
  - `GET /categories/trashed` -> `CategoryController@trashed`
  - `PATCH /categories/{id}/restore` -> `CategoryController@restore`
  - `DELETE /categories/{id}/force-delete` -> `CategoryController@forceDelete`
- Tuy nhiên, trong class `CategoryController` **hoàn toàn chưa được viết (missing methods)**. Khi frontend hoặc postman gọi API, sẽ bị crash `BadMethodCallException` hoặc 500. Bạn cần bổ sung các API này sớm nhất có thể.
