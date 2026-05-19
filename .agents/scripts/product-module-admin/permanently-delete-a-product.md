## URL

http://localhost:8000/api

## Endpoint:

/api/admin/products/{id}/force-delete

## Method:

DELETE

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
  "message": "Product permanently deleted successfully",
  "data": null
}
```

