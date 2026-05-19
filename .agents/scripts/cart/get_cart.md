## URL

http://localhost:8000/api

## Endpoint:

/cart

## Method:

GET

## Authentication:

Authorization: Bearer {{access_token}}

## Request:

N/A

## Response:

```json
{
  "status": "success",
  "message": "Cart retrieved successfully",
  "data": {
    "id": 3,
    "user_id": 1,
    "items": [
      {
        "id": 1,
        "cart_id": 3,
        "product_id": 7,
        "quantity": 2,
        "options": {
          "sizes": "M",
          "colors": "Red"
        },
        "product": {
          "id": 7,
          "name": "Classic T-Shirt",
          "price": 150000,
          "price_discount": 120000,
          "images": [
            {
              "id": 1,
              "url": "https://example.com/images/shirt.jpg"
            }
          ]
        }
      }
    ],
    "created_at": "2024-01-15 10:30:00",
    "updated_at": "2024-01-15 12:00:00"
  }
}
```
