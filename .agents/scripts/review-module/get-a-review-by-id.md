## URL

http://localhost:8000/api

## Endpoint:

/api/reviews/{id}

## Method:

GET

## Authentication:

No Authentication Required

## Request:

**Parameters:**
- `id` (path): integer (required)

```json
{}
```

## Response:

```json
{
  "status": 200,
  "message": "Success",
  "data": {
    "id": 1,
    "user_id": 1,
    "user_name": "John Doe",
    "product_id": 1,
    "order_item_id": 10,
    "rating": 5,
    "comment": "Great product!",
    "is_approved": true,
    "created_at": "string",
    "updated_at": "string"
  }
}
```

