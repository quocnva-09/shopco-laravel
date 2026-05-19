## URL

http://localhost:8000/api

## Endpoint:

/api/register

## Method:

POST

## Authentication:

No Authentication Required

## Request:

```json
{
  "name": "John Doe",
  "email": "john.doe@example.com",
  "phone": "0123456789",
  "password": "password123",
  "password_confirmation": "password123"
}
```

## Response:

```json
{
  "status": 201,
  "message": "Register successfully",
  "data": {
    "user": null,
    "access_token": "2|abc...",
    "token_type": "Bearer"
  }
}
```

