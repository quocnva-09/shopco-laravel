## URL

http://localhost:8000/api

## Endpoint:

/api/admin/categories/{id}

## Method:

PUT

## Authentication:

Authorization: Bearer {{access_token}}

## Request:

**Parameters:**
- `id` (path): integer (required)

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
  "status": 200,
  "message": "Category updated successfully",
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

