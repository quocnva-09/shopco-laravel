## URL

http://localhost:8000/api

## Endpoint:

/api/admin/reviews/{id}/approve

## Method:

PATCH

## Authentication:

Authorization: Bearer {{access_token}}

## Request:

**Parameters:**
- `id` (path): integer (required)

```json
{
  "is_approved": true
}
```

## Response:

```json
{}
```

