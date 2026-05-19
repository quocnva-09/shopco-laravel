## URL

http://localhost:8000/api

## Endpoint:

/api/admin/exports/{id}

## Method:

GET

## Authentication:

Authorization: Bearer {{access_token}}

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
  "message": "success",
  "data": {
    "id": 1,
    "type": "products",
    "format": "csv",
    "status": "completed",
    "file_path": "exports/products_2024-01-15.csv",
    "error_message": null,
    "created_at": "2024-01-15T10:30:00+07:00"
  }
}
```

