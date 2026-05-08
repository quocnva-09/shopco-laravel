<?php

declare(strict_types=1);

namespace App\Services;

use App\Contracts\TemplateServiceInterface;
use App\DTOs\TemplateDTO;
use App\DTOs\TemplateFilterDTO;
use App\Models\Template;
use Illuminate\Pagination\LengthAwarePaginator;

class TemplateService implements TemplateServiceInterface
{
    public function list(TemplateFilterDTO $dto): LengthAwarePaginator
    {
        $query = Template::query();

        if ($dto->keyword) {
            $query->where('name', 'like', "%{$dto->keyword}%");
        }

        // Example assuming an Enum 'TemplateSort' exists
        // $sortColumn = $dto->sort ? $dto->sort->value : 'created_at';
        $sortColumn = $dto->sort ?? 'created_at';
        $order = $dto->order ?? 'desc';

        return $query->orderBy($sortColumn, $order)->paginate($dto->perPage);
    }

    public function findById(int $id): Template
    {
        return Template::findOrFail($id);
    }

    public function create(TemplateDTO $dto): Template
    {
        return Template::create($dto->toArray());
    }

    public function update(Template $template, TemplateDTO $dto): Template
    {
        $template->update($dto->toArray());

        return $template;
    }

    public function delete(Template $template): void
    {
        $template->delete();
    }

    public function trashed(TemplateFilterDTO $dto): LengthAwarePaginator
    {
        $query = Template::onlyTrashed();

        if ($dto->keyword) {
            $query->where('name', 'like', "%{$dto->keyword}%");
        }

        $sortColumn = $dto->sort ?? 'deleted_at';
        $order = $dto->order ?? 'desc';

        return $query->orderBy($sortColumn, $order)->paginate($dto->perPage);
    }

    public function restore(int $id): Template
    {
        $template = Template::onlyTrashed()->findOrFail($id);
        $template->restore();

        return $template;
    }

    public function forceDelete(int $id): void
    {
        $template = Template::withTrashed()->findOrFail($id);
        $template->forceDelete();
    }
}
