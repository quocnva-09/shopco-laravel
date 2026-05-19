## URL

http://localhost:8000/api

## Endpoint:

/cart/items

## Method:

POST

## Authentication:

Authorization: Bearer {{access_token}}

## Request:

```json
{
  "product_id": 7,
  "quantity": 2,
  "options": {
    "sizes": "M",
    "colors": "Red"
  }
}
```

## Response:

```json
{
  "status": "success",
  "message": "Item added to cart successfully",
  "data": {
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
}
```
