## URL

http://localhost:8000/api

## Endpoint:

/api/admin/categories/{id}

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
  "message": "Category deleted successfully",
  "data": null
}
```

