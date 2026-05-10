<?php

namespace App\Exceptions;

use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class GlobalExceptionHandler
{
    public function __invoke(Exceptions $exceptions): void
    {
        $this->registerErrorRenderers($exceptions);
    }

    private function registerErrorRenderers(Exceptions $exceptions): void
    {
        // 1. Bắt AppException (Lỗi nghiệp vụ chủ động ném ra)
        $exceptions->render(function (AppException $e, Request $request) {
            if ($request->is('api/*') || $request->wantsJson()) {
                $errorCode = $e->getErrorCode();
                
                return response()->json([
                    'code'    => $errorCode->getCode(), // Mã lỗi nội bộ (vd: 1002)
                    'message' => $errorCode->getMessage(), // Thông báo (vd: "User existed")
                ], $errorCode->getStatusCode()); // HTTP Status (vd: 400)
            }
        });

        // 2. Bắt Exception thường (Tương tự Uncategorized_Exception bên Java)
        $exceptions->render(function (\Exception $e, Request $request) {
            if ($request->is('api/*') || $request->wantsJson()) {
                // Trong môi trường dev, có thể trả về lỗi gốc để dễ debug
                // Nhưng trên production thì nên che đi bằng lỗi 999
                $isProduction = app()->isProduction();

                return response()->json([
                    'code'    => 999,
                    'message' => $isProduction ? 'Uncategorized exception.' : $e->getMessage(),
                ], Response::HTTP_INTERNAL_SERVER_ERROR);
            }
        });
        
        // ... Các lỗi 404, 401 khác bạn giữ nguyên như cũ
    }
}
