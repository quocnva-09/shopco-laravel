## URL

http://localhost:8000/api

## Endpoint:

/api/admin/exports

## Method:

GET

## Authentication:

Authorization: Bearer {{access_token}}

## Request:

```json
{}
```

## Response:

```json
{
  "status": 200,
  "message": "success",
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

