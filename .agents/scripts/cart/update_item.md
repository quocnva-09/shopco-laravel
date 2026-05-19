## URL

http://localhost:8000/api

## Endpoint:

/cart/items/{itemId}

## Method:

PUT

## Authentication:

Authorization: Bearer {{access_token}}

## Request:

```json
{
  "quantity": 3
}
```

## Response:

```json
{
  "status": "success",
  "message": "Cart item updated successfully",
  "data": true
}
```
