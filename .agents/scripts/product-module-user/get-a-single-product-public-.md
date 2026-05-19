## URL

http://localhost:8000/api

## Endpoint:

/api/products/{id}

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
  "message": "Product retrieved successfully",
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

