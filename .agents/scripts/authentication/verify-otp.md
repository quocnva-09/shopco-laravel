## URL

http://localhost:8000/api

## Endpoint:

/api/verify-otp

## Method:

POST

## Authentication:

No Authentication Required

## Request:

```json
{
  "otp": "123456",
  "type": "forget",
  "email": "kenjiprovip@gmail.com",
  "password": "password",
  "password_confirmation": "password"
}
```

## Response:

```json
{
  "status": 200,
  "message": "Verify otp successfully",
  "data": true
}
```

