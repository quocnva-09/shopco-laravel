## URL

http://localhost:8000/api

## Endpoint:

/api/cart

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
  "message": "Cart retrieved successfully",
  "data": {
    "id": 3,
    "user_id": 1,
    "items": [
      null
    ],
    "created_at": "2024-01-15 10:30:00",
    "updated_at": "2024-01-15 12:00:00"
  }
}
```

