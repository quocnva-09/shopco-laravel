## URL

http://localhost:8000/api

## Endpoint:

/api/products/{productId}/reviews

## Method:

GET

## Authentication:

No Authentication Required

## Request:

**Parameters:**
- `productId` (path): integer (required)
- `sort_by` (query): string 
- `sort_direction` (query): string 
- `limit` (query): integer 

```json
{}
```

## Response:

```json
{
  "status": 200,
  "message": "Success",
  "data": [
    null
  ],
  "meta": {},
  "links": {}
}
```

