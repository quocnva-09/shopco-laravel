## URL

http://localhost:8000/api

## Endpoint:

/api/cart/items/{itemId}

## Method:

DELETE

## Authentication:

Authorization: Bearer {{access_token}}

## Request:

**Parameters:**
- `itemId` (path): integer (required)

```json
{}
```

## Response:

```json
{
  "status": 200,
  "message": "Cart item removed successfully",
  "data": true
}
```

