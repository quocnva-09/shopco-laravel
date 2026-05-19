## URL

http://localhost:8000/api

## Endpoint:

/api/admin/products/{id}

## Method:

PUT

## Authentication:

Authorization: Bearer {{access_token}}

## Request:

**Parameters:**
- `id` (path): integer (required)

```json
{
  "name": "Classic T-Shirt",
  "slug": "classic-t-shirt",
  "price": 150000,
  "price_discount": 120000,
  "description": "A comfortable everyday t-shirt",
  "category_id": 2,
  "images": [
    "products/xyz.jpg"
  ],
  "sizes": [
    "M"
  ],
  "colors": [
    "Red"
  ],
  "is_active": true
}
```

## Response:

```json
{
  "status": 200,
  "message": "Product updated successfully",
  "data": {
    "id": 1,
    "name": "Classic T-Shirt",
    "slug": "classic-t-shirt",
    "description": "A comfortable everyday t-shirt",
    "price": 150000,
    "price_discount": 120000,
    "sizes": [
      "S",
      "M",
      "L"
    ],
    "colors": [
      "Red",
      "Blue",
      "Black"
    ],
    "is_active": true,
    "category": {
      "id": 2,
      "name": "T-Shirts",
      "slug": "t-shirts"
    },
    "images": [
      null
    ],
    "created_at": "2023-01-01 12:00:00",
    "updated_at": "2023-01-01 12:00:00"
  }
}
```

