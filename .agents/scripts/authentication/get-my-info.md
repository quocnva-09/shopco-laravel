## URL

http://localhost:8000/api

## Endpoint:

/api/me

## Method:

GET

## Authentication:

Authorization: Bearer {{access_token}}

## Request:

```json
{}
```

## Response:

```json
{
  "status": 200,
  "message": "My info fetched successfully",
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

