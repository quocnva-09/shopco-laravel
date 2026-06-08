<?php

namespace App\Http\Controllers;

use App\Traits\ApiResponseTrait;
use OpenApi\Attributes as OA;

#[OA\Info(
    version: "1.0.0",
    title: "SHOP.CO API Documentation",
    description: "API integration documentation for the E-commerce system"
)]
#[OA\Server(
    url: L5_SWAGGER_CONST_HOST,
    description: "Dynamic Server (Follow file .env)"
)]
#[OA\Server(
    url: "https://api.quocnva09.me/api",
    description: "Production Server (Cloudflare SSL)"
)]
#[OA\Server(
    url: "http://localhost:8000/api",
    description: "Local Server (Fallback)"
)]
#[OA\SecurityScheme(
    securityScheme: "bearerAuth",
    type: "http",
    scheme: "bearer",
    description: "Enter your Sanctum Personal Access Token here"
)]
abstract class Controller
{
    use ApiResponseTrait;
    //
}

#[OA\Schema(
    schema: "PaginatedMeta",
    title: "Pagination Meta",
    description: "Numeric parameters used to render pagination UI",
    properties: [
        new OA\Property(property: "current_page", type: "integer", example: 1),
        new OA\Property(property: "last_page", type: "integer", example: 10),
        new OA\Property(property: "per_page", type: "integer", example: 15),
        new OA\Property(property: "total", type: "integer", example: 150),
    ]
)]
class PaginatedMetaSchema
{
} // Virtual class (OA schema placeholder)

#[OA\Schema(
    schema: "PaginatedLinks",
    title: "Pagination Links",
    description: "Available links supporting Infinite Scroll",
    properties: [
        new OA\Property(property: "first", type: "string", example: "http://localhost:8000/api/{resource}?page=1"),
        new OA\Property(property: "last", type: "string", example: "http://localhost:8000/api/{resource}?page=10"),
        new OA\Property(property: "prev", type: "string", nullable: true, example: null),
        new OA\Property(property: "next", type: "string", nullable: true, example: "http://localhost:8000/api/{resource}?page=2"),
    ]
)]
class PaginatedLinksSchema
{
} // Virtual class (OA schema placeholder)
