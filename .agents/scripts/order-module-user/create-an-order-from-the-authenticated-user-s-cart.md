## URL

http://localhost:8000/api

## Endpoint:

/api/orders

## Method:

POST

## Authentication:

Authorization: Bearer {{access_token}}

## Request:

```json
{}
```

## Response:

```json
{
  "status": 201,
  "message": "Order created successfully",
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

