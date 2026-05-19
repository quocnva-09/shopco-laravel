## URL

http://localhost:8000/api

## Endpoint:

/api/admin/categories

## Method:

POST

## Authentication:

Authorization: Bearer {{access_token}}

## Request:

```json
{
  "name": "Electronics",
  "slug": "electronics",
  "description": "Electronic items and gadgets"
}
```

## Response:

```json
{
  "status": 201,
  "message": "Category created successfully",
  "data": {
    "id": 1,
    "name": "Electronics",
    "slug": "electronics",
    "description": "Electronic items and gadgets",
    "created_at": "2023-01-01 12:00:00",
    "updated_at": "2023-01-01 12:00:00"
  }
}
```

