## URL

http://localhost:8000/api

## Endpoint:

/api/cart/items/{itemId}

## Method:

PUT

## Authentication:

Authorization: Bearer {{access_token}}

## Request:

**Parameters:**
- `itemId` (path): integer (required)

```json
{
  "quantity": 3
}
```

## Response:

```json
{
  "status": 200,
  "message": "Cart item updated successfully",
  "data": true
}
```

