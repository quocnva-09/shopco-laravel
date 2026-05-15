<?php

declare(strict_types=1);

namespace App\DTOs\Upload;

use App\Http\Requests\Upload\FileUploadRequest;
use Illuminate\Http\UploadedFile;

class FileUploadDTO
{
    public function __construct(
        public readonly UploadedFile $file,
        public readonly string $type
    ) {
    }

    public static function fromRequest(FileUploadRequest $request, string $type): self
    {
        return new self(
            $request->file('image'),
            $type
        );
    }
}
