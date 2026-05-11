<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\DTOs\ExportDTO;
use App\Http\Controllers\Controller;
use App\Http\Requests\ExportRequest;
use App\Http\Resources\ExportResource;
use App\Contracts\Services\ExportServiceInterface;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class ExportController extends Controller
{
    use ApiResponseTrait;

    private ExportServiceInterface $exportService;

    public function __construct(ExportServiceInterface $exportService)
    {
        $this->exportService = $exportService;
    }

    public function store(ExportRequest $request): JsonResponse
    {
        $dto = ExportDTO::fromRequest($request->validated());

        $exportHistory = $this->exportService->requestProductExport($dto);

        return $this->successResponse(
            new ExportResource($exportHistory),
            'Export processing started successfully.',
            Response::HTTP_ACCEPTED
        );
    }

    public function index(): JsonResponse
    {
        $histories = $this->exportService->getUserExportHistories();

        return $this->paginatedResponse(ExportResource::collection($histories));
    }

    public function show(int $id): JsonResponse
    {
        $exportHistory = $this->exportService->getUserExportHistory($id);

        return $this->successResponse(new ExportResource($exportHistory));
    }

    public function download(int $id): Response
    {
        try {
            return $this->exportService->downloadProductExport($id);
        } catch (\LogicException $e) {
            return $this->errorResponse($e->getMessage(), Response::HTTP_BAD_REQUEST);
        }
    }
}
