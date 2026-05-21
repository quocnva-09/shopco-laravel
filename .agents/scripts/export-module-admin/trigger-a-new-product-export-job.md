## URL

http://localhost:8000/api

## Endpoint:

/api/admin/exports/products

## Method:

POST

## Authentication:

Authorization: Bearer {{access_token}}

## Request:

```json
{
  "format": "csv",
  "search": "shirt",
  "status": "string"
}
```

## Response:

```json
{
  "status": 202,
  "message": "Export processing started successfully.",
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

