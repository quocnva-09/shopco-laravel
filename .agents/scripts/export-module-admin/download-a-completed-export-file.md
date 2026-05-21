## URL

http://localhost:8000/api

## Endpoint:

/api/admin/exports/{id}/download

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
    "download_url": "https://s3.amazonaws.com/bucket/exports/export_123.csv"
  }
}
```

