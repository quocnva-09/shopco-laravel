## URL

http://localhost:8000/api

## Endpoint:

/api/login

## Method:

POST

## Authentication:

No Authentication Required

## Request:

```json
{
  "email": "admin@example.com",
  "password": "password"
}
```

## Response:

```json
{
  "status": 200,
  "message": "Login successfully",
  "data": {
    "user": null,
    "access_token": "1|xyz...",
    "token_type": "Bearer"
  }
}
```

