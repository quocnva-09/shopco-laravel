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
  "application/octet-stream": {
    "schema": {
      "type": "string",
      "format": "binary"
    }
  }
}
```

