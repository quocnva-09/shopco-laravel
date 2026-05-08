# Đánh giá CRUD User

## 1. Validation Request
- **Ưu điểm**:
  - Tách bạch rules rõ ràng trong `UserRequest`.
  - Check dynamic Rule cho Update (`$this->isMethod('post')` để required password, còn `put` thì `nullable`).
  - Dùng Enum `UserRole::getValues()` linh hoạt để check role input.
- **Thiếu sót/Gợi ý**: 
  - Khuyến khích dùng Rule Object ví dụ `Rule::unique('users', 'email')->ignore($userId)` thay vì concat string, sẽ an toàn và sạch sẽ hơn cho Laravel.

## 2. Chuẩn hóa Resource trả về
- **Ưu điểm**: 
  - Đã có `UserResource`, format timestamp đúng chuẩn `'Y-m-d H:i:s'`.
- **Thiếu sót**: Vẫn chung tình trạng format JSON cứng ở controller. Nên gom chung lại.

## 3. Trách nhiệm của Service/Controller
- **Ưu điểm**:
  - `UserService` làm rất tốt việc băm (hash) password bằng `bcrypt` trước khi save.
  - Phân trang đầy đủ.
- **Thiếu sót**: 
  - Ở `updateUser`, check `if ($dto->password) { userData['password'] = bcrypt() }` thực hiện đúng trách nhiệm không đè password trống.
  - Code controller sạch, truyền DTO chuẩn. Không có gì phàn nàn nhiều ở flow này.

## 4. Model Fillable
- Có sử dụng Enum casting `UserRole::class`, một tính năng rất tốt của PHP 8.1.
- Có cài đặt `$hidden` để giấu `password` và `remember_token` khi trả object ra.
- `$fillable` đúng chuẩn.

## 5. Xử lý Exception
- Sử dụng `findOrFail()` để bắt 404 cho User, an toàn. Cùng chung luồng exception toàn cục.
