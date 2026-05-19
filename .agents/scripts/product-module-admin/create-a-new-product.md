## URL

http://localhost:8000/api

## Endpoint:

/api/admin/products

## Method:

POST

## Authentication:

Authorization: Bearer {{access_token}}

## Request:

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
  "status": 201,
  "message": "Product created successfully",
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

