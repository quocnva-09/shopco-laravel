<?php

declare(strict_types=1);

namespace App\Contracts;

use App\DTOs\TemplateDTO;
use App\DTOs\TemplateFilterDTO;
use App\Models\Template;
use Illuminate\Pagination\LengthAwarePaginator;

interface TemplateServiceInterface
{
    public function list(TemplateFilterDTO $dto): LengthAwarePaginator;

    public function findById(int $id): Template;

    public function create(TemplateDTO $dto): Template;

    public function update(Template $template, TemplateDTO $dto): Template;

    public function delete(Template $template): void;

    public function trashed(TemplateFilterDTO $dto): LengthAwarePaginator;

    public function restore(int $id): Template;

    public function forceDelete(int $id): void;
}
