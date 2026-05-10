# EXCEPTION HANDLING REFERENCES & STANDARDS

## 1. Technical Constraints

- **PHP Version**: Must use PHP 8.1+ native Enums (Backed Enums).
- **Type Hinting**: Strict typing is mandatory for all Enum methods and Exception constructors.

## 2. Standardized JSON Error Response

Whenever the `GlobalExceptionHandler` intercepts an `AppException`, the HTTP response body MUST strictly follow this JSON structure:

```json
{
    "code": 1005,
    "message": "User not existed"
}

Note: The code here refers to the internal business logic code defined in the Enum, NOT the HTTP status code. The HTTP status code (e.g., 404, 400) is applied to the response header.
```

## 3. Error Code Numbering Convention

When defining new errors in ErrorCode.php, follow this internal numbering block standard to keep errors organized by domain:

999: Uncategorized / System Errors.

1000 - 1999: User, Authentication, and Authorization Errors (e.g., UNAUTHENTICATED, TOKEN_EXPIRED).

2000 - 2999: Permission & Role Errors.

4000 - 4999: Employee / Staff Errors.

5000 - 5999: Department & HR Errors (Leave, Salary, Contract).

8000 - 8999: E-commerce / Cart / Order Errors (e.g., CART_IS_EMPTY).

## 4. Unhandled Exceptions (Fallback)

If an unexpected system error occurs (standard \Exception or \Error) in a production environment (app()->isProduction()), the Global Handler must mask the error details and return the generic ErrorCode::UNCATEGORIZED_EXCEPTION to prevent exposing sensitive stack traces to the client.

```

```
