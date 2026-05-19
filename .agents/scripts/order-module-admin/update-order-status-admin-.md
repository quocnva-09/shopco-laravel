## URL

http://localhost:8000/api

## Endpoint:

/api/admin/orders/{order}/status

## Method:

PATCH

## Authentication:

Authorization: Bearer {{access_token}}

## Request:

**Parameters:**
- `order` (path): integer (required)

```json
{
  "status": "paid"
}
```

## Response:

```json
{
  "status": 200,
  "message": "Order status updated successfully",
  "data": null
}
```

