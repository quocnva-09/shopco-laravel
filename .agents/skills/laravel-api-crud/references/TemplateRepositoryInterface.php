<?php

declare(strict_types=1);

namespace App\Contracts\Repositories;

use App\DTOs\Template\TemplateFilterDTO;
use App\Models\Template;
use Illuminate\Pagination\LengthAwarePaginator;

interface TemplateRepositoryInterface
{
    public function paginateAll(TemplateFilterDTO $filter): LengthAwarePaginator;

    public function findById(int $id): Template;

    public function findTrashedById(int $id): Template;

    public function create(array $data): Template;

    public function update(Template $template, array $data): Template;

    public function delete(Template $template): bool;

    public function paginateTrashed(TemplateFilterDTO $filter): LengthAwarePaginator;

    public function restore(Template $template): Template;

    public function forceDelete(Template $template): bool;
}
