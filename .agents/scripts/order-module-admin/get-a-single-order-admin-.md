## URL

http://localhost:8000/api

## Endpoint:

/api/admin/orders/{order}

## Method:

GET

## Authentication:

Authorization: Bearer {{access_token}}

## Request:

**Parameters:**
- `order` (path): integer (required)

```json
{}
```

## Response:

```json
{
  "status": 200,
  "message": "success",
  "data": {
    "id": 1,
    "user_id": 3,
    "status": "pending",
    "totalAmount": 450000,
    "created_at": "2024-01-15 10:30:00",
    "updated_at": "2024-01-15 12:00:00",
    "items": [
      null
    ]
  }
}
```

