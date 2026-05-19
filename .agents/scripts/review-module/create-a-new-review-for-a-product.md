## URL

http://localhost:8000/api

## Endpoint:

/api/reviews

## Method:

POST

## Authentication:

Authorization: Bearer {{access_token}}

## Request:

```json
{
  "product_id": 1,
  "order_item_id": 10,
  "rating": 5,
  "comment": "Great product!"
}
```

## Response:

```json
{
  "status": 201,
  "message": "Review created successfully.",
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

