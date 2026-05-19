## URL

http://localhost:8000/api

## Endpoint:

/api/admin/orders

## Method:

GET

## Authentication:

Authorization: Bearer {{access_token}}

## Request:

**Parameters:**
- `search` (query): string 
- `status` (query): string 
- `per_page` (query): integer 
- `sort_by` (query): string 
- `sort_dir` (query): string 

```json
{}
```

## Response:

```json
{
  "status": 200,
  "message": "Order list retrieved successfully",
  "data": [
    null
  ],
  "meta": {
    "current_page": 1,
    "last_page": 10,
    "per_page": 15,
    "total": 150
  }
}
```

