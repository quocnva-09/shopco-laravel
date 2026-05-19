## URL

http://localhost:8000/api

## Endpoint:

/api/admin/products/upload

## Method:

POST

## Authentication:

Authorization: Bearer {{access_token}}

## Request:

```json
{
  "required": true,
  "content": {
    "multipart/form-data": {
      "schema": {
        "required": [
          "image"
        ],
        "properties": {
          "image": {
            "description": "Image file (max 5MB, jpeg/png/jpg/webp)",
            "type": "string",
            "format": "binary"
          }
        },
        "type": "object"
      }
    }
  }
}
```

## Response:

```json
{
  "status": 200,
  "message": "Uploaded successfully",
  "data": {
    "img_path": "products/xyz.jpg",
    "image_url": "https://s3.amazonaws.com/bucket/products/xyz.jpg"
  }
}
```

