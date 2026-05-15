<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Contracts\Repositories\TemplateRepositoryInterface;
use App\DTOs\Template\TemplateFilterDTO;
use App\Enums\FilterEnum;
use App\Models\Template;
use Illuminate\Pagination\LengthAwarePaginator;

class TemplateRepository implements TemplateRepositoryInterface
{
    public function paginateAll(TemplateFilterDTO $filter): LengthAwarePaginator
    {
        $query = Template::query()->select(['id', 'name', 'email', 'role', 'created_at']);

        if ($filter->search) {
            $query->where(function ($q) use ($filter) {
                $q->where('name', 'like', '%' . $filter->search . '%')
                    ->orWhere('email', 'like', '%' . $filter->search . '%');
            });
        }

        if (in_array($filter->sortBy, FilterEnum::USER_SORT)) {
            $direction = in_array(strtolower($filter->sortDir), FilterEnum::DIRECTION)
                ? $filter->sortDir
                : 'desc';
            $query->orderBy($filter->sortBy, $direction);
        } else {
            $query->orderBy('created_at', 'desc');
        }

        return $query->paginate($filter->perPage, ['*'], 'page', $filter->page);
    }

    public function findById(int $id): Template
    {
        return Template::findOrFail($id);
    }

    public function findTrashedById(int $id): Template
    {
        return Template::onlyTrashed()->findOrFail($id);
    }

    public function findByEmail(string $email): ?Template
    {
        return User::where('email', $email)->first();
    }

    public function create(array $data): Template
    {
        return Template::create($data);
    }

    public function update(Template $template, array $data): Template
    {
        $template->update($data);

        return $template;
    }

    public function delete(Template $template): bool
    {
        return (bool) $template->delete();
    }

    public function paginateTrashed(TemplateFilterDTO $filter): LengthAwarePaginator
    {
        $query = Template::onlyTrashed()->select(['id', 'name', 'email', 'role', 'deleted_at']);

        if ($filter->search) {
            $query->where(function ($q) use ($filter) {
                $q->where('name', 'like', '%' . $filter->search . '%')
                    ->orWhere('email', 'like', '%' . $filter->search . '%');
            });
        }

        if (in_array($filter->sortBy, FilterEnum::USER_SORT)) {
            $direction = in_array(strtolower($filter->sortDir), FilterEnum::DIRECTION)
                ? $filter->sortDir
                : 'desc';
            $query->orderBy($filter->sortBy, $direction);
        } else {
            $query->orderBy('deleted_at', 'desc');
        }

        return $query->paginate($filter->perPage, ['*'], 'page', $filter->page);
    }

    public function restore(Template $template): Template
    {
        $template->restore();

        return $template;
    }

    public function forceDelete(Template $template): bool
    {
        return (bool) $template->forceDelete();
    }
}
