## URL

http://localhost:8000/api

## Endpoint:

/api/admin/users

## Method:

POST

## Authentication:

Authorization: Bearer {{access_token}}

## Request:

```json
{
  "name": "John Doe",
  "email": "john@example.com",
  "role": "user",
  "password": "password",
  "profile_image": "users/avatar.jpg",
  "address": "123 Main St",
  "phone": "1234567890",
  "bio": "I am a user"
}
```

## Response:

```json
{
  "status": 201,
  "message": "User created successfully",
  "data": {
    "id": 1,
    "name": "John Doe",
    "email": "john@example.com",
    "role": "user",
    "profile_image": "https://s3.amazonaws.com/bucket/users/avatar.jpg",
    "address": "123 Main St",
    "phone": "1234567890",
    "bio": "I am a user",
    "created_at": "2023-01-01 12:00:00"
  }
}
```

