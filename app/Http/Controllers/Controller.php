<?php

namespace App\Http\Controllers;

use App\Traits\ApiResponseTrait;
use OpenApi\Attributes as OA;

#[OA\Info(
    version: "1.0.0",
    title: "SHOP.CO API Documentation",
    description: "Tài liệu tích hợp API cho hệ thống E-commerce"
)]
#[OA\Server(
    url: L5_SWAGGER_CONST_HOST,
    description: "Local Server"
)]
#[OA\Server(
    url: "https://api.quocnva09.me/api",
    description: "Production Server (Cloudflare SSL)"
)]
#[OA\SecurityScheme(
    securityScheme: "bearerAuth",
    type: "http",
    scheme: "bearer",
    description: "Nhập Sanctum Personal Access Token của bạn vào đây"
)]
abstract class Controller
{
    use ApiResponseTrait;
    //
}

#[OA\Schema(
    schema: "PaginatedMeta",
    title: "Pagination Meta",
    description: "Các thông số toán học để vẽ giao diện phân trang",
    properties: [
        new OA\Property(property: "current_page", type: "integer", example: 1),
        new OA\Property(property: "last_page", type: "integer", example: 10),
        new OA\Property(property: "per_page", type: "integer", example: 15),
        new OA\Property(property: "total", type: "integer", example: 150),
    ]
)]
class PaginatedMetaSchema
{
} // Class ảo

#[OA\Schema(
    schema: "PaginatedLinks",
    title: "Pagination Links",
    description: "Các đường dẫn có sẵn hỗ trợ tính năng Cuộn vô tận (Infinite Scroll)",
    properties: [
        new OA\Property(property: "first", type: "string", example: "http://localhost:8000/api/{resource}?page=1"),
        new OA\Property(property: "last", type: "string", example: "http://localhost:8000/api/{resource}?page=10"),
        new OA\Property(property: "prev", type: "string", nullable: true, example: null),
        new OA\Property(property: "next", type: "string", nullable: true, example: "http://localhost:8000/api/{resource}?page=2"),
    ]
)]
class PaginatedLinksSchema
{
} // Class ảo
