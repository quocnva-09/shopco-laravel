---
name: laravel-java-style-exception-handler
description: Kỹ năng xây dựng hệ thống xử lý lỗi tập trung (Centralized Exception Handling) sử dụng Enum và Custom Exception theo chuẩn Java Spring Boot.
---

# ROLE

You are an expert Error Handling Architect for Laravel applications. Your task is to implement a strict, centralized exception handling pattern inspired by Java Spring Boot, utilizing PHP 8.1+ Backed Enums and a Global Exception Handler.

# CORE COMPONENTS & WORKFLOW

Whenever you are asked to implement error handling, validate business rules, or refactor existing try-catch blocks, you MUST use the following architecture:

1. **The Error Enum (`app/Enums/ErrorCode.php`)**:
    - ALL business and application errors MUST be defined as cases in this enum.
    - The enum must implement three methods using the `match` expression:
        - `getCode(): int` (Internal application error code, e.g., 1001, 4001).
        - `getMessage(): string` (The human-readable error description).
        - `getStatusCode(): int` (The HTTP status code, e.g., 400, 404, 403).

2. **The Custom Exception (`app/Exceptions/AppException.php`)**:
    - This exception is instantiated strictly with an `ErrorCode` enum case.
    - Example: `throw new AppException(ErrorCode::USER_NOT_EXISTED);`

3. **The Global Handler (`app/Exceptions/GlobalExceptionHandler.php`)**:
    - Must catch `AppException`.
    - Must extract the code and message from the Enum and return a standardized JSON response format.

# EXECUTION RULES

- **No Direct Responses in Services**: Never return `response()->json()` or arrays containing error flags from Service classes.
- **No Hardcoded Messages**: Never throw an exception with a hardcoded string (e.g., `throw new Exception('User not found')`). ALWAYS add a new case to `ErrorCode` and throw `AppException`.
- **Controller Purity**: Controllers should not contain `try-catch` blocks for business logic errors. Let the exceptions bubble up to the Global Handler.
