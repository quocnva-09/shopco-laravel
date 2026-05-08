<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Contracts\TemplateServiceInterface;
use App\DTOs\TemplateDTO;
use App\DTOs\TemplateFilterDTO;
use App\Http\Requests\TemplateFilterRequest;
use App\Http\Requests\TemplateRequest;
use App\Http\Resources\TemplateResource;
use App\Models\Template;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\JsonResponse;

class TemplateController extends Controller
{
    use ApiResponseTrait;

    public function __construct(
        private readonly TemplateServiceInterface $templateService
    ) {}

    public function index(TemplateFilterRequest $request): JsonResponse
    {
        $dto = TemplateFilterDTO::fromRequest($request);
        $templates = $this->templateService->list($dto);

        return $this->paginatedResponse(TemplateResource::collection($templates));
    }

    public function store(TemplateRequest $request): JsonResponse
    {
        $dto = TemplateDTO::fromRequest($request);
        $template = $this->templateService->create($dto);

        return $this->successResponse(new TemplateResource($template), 'Created successfully', 201);
    }

    public function show(int $id): JsonResponse
    {
        $template = $this->templateService->findById($id);

        return $this->successResponse(new TemplateResource($template));
    }

    public function update(TemplateRequest $request, Template $template): JsonResponse
    {
        $dto = TemplateDTO::fromRequest($request);
        $updatedTemplate = $this->templateService->update($template, $dto);

        return $this->successResponse(new TemplateResource($updatedTemplate), 'Updated successfully');
    }

    public function destroy(Template $template): JsonResponse
    {
        $this->templateService->delete($template);

        return $this->successResponse(null, 'Deleted successfully', 204);
    }

    // Optional Soft Delete endpoints (only if routes exist)
    public function trashed(TemplateFilterRequest $request): JsonResponse
    {
        $dto = TemplateFilterDTO::fromRequest($request);
        $templates = $this->templateService->trashed($dto);

        return $this->paginatedResponse(TemplateResource::collection($templates));
    }

    public function restore(int $id): JsonResponse
    {
        $template = $this->templateService->restore($id);

        return $this->successResponse(new TemplateResource($template), 'Restored successfully');
    }

    public function forceDelete(int $id): JsonResponse
    {
        $this->templateService->forceDelete($id);

        return $this->successResponse(null, 'Permanently deleted', 204);
    }
}
