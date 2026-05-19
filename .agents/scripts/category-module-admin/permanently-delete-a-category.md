## URL

http://localhost:8000/api

## Endpoint:

/api/admin/categories/{id}/force-delete

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
  "message": "Category permanently deleted successfully",
  "data": null
}
```

