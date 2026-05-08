# Đánh giá CRUD Product

## 1. Validation Request
- **Ưu điểm**:
  - Tách logic `ProductRequest` và `ProductFilterRequest`.
  - Khai báo validate ảnh tốt: `images.* => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048'`.
- **Thiếu sót/Gợi ý**:
  - Logic validate giá: Chưa kiểm tra `price_discount` phải nhỏ hơn hoặc bằng `price`. Sẽ có lỗi nghiệp vụ nếu giá giảm cao hơn giá gốc.
  - Rule Update (`PUT/PATCH`) đang đè logic `sometimes|required`. Cần check cẩn thận xem nếu client không truyền field `price` thì có bị lỗi khi update record không.

## 2. Chuẩn hóa Resource trả về
- Đã sử dụng `ProductResource`, phân trang tốt, lấy được nested data (`images`).
- Vẫn mắc lỗi lặp lại format response giống `CategoryController`. 

## 3. Trách nhiệm của Service/Controller
- **Ưu điểm**:
  - `ProductService` đảm nhận tốt logic tự động sinh `slug` từ `name` nếu thiếu.
  - Chịu trách nhiệm upload images và tách ra một private function `uploadImages()`. 
  - Dùng `loadMissing` tối ưu query N+1.
- **Thiếu sót**: 
  - Có thể consider chuyển phần xử lý file upload ra một `UploadService` hoặc `FileService` riêng để tuân thủ Single Responsibility, vì ProductService có thể sẽ phình to ra.

## 4. Model Fillable
- `$fillable` config đầy đủ.
- Xử lý hay ở việc sử dụng event `booted()` và `forceDeleting` để trigger việc xóa vật lý image trong Storage. 
- **Lưu ý**: Product model dùng `SoftDeletes`, nhưng hiện tại controller/route chưa hề có route để lấy danh sách rác (`trashed`), hay route để `forceDelete`. Hình ảnh sẽ không bao giờ bị xóa trong ổ cứng nếu product chỉ bị soft delete.

## 5. Xử lý Exception
- Sử dụng `findOrFail()` giống Category. Không có ngoại lệ nào không lường trước. Mọi lỗi khác nếu có (ví dụ xóa file lỗi) sẽ văng Exception và được Global bắt lấy (status 500).
