<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class MakeDTOCommand extends Command
{
    /**
     * Tên lệnh gõ trong terminal
     */
    protected $signature = 'make:dto {name}';

    /**
     * Mô tả lệnh
     */
    protected $description = 'Create a new Data Transfer Object (DTO)';

    /**
     * Thực thi lệnh
     */
    public function handle()
    {
        $name = $this->argument('name');

        // Định nghĩa đường dẫn chuẩn: app/DTOs/TênFile.php
        $path = app_path("DTOs/{$name}.php");

        // 1. Kiểm tra file đã tồn tại chưa
        if (File::exists($path)) {
            $this->error("DTO {$name} đã tồn tại!");

            return Command::FAILURE;
        }

        // 2. Đọc file stub mẫu
        $stubPath = base_path('stubs/dto.stub');
        if (! File::exists($stubPath)) {
            $this->error('Không tìm thấy khuôn đúc tại stubs/dto.stub!');

            return Command::FAILURE;
        }

        $content = File::get($stubPath);

        // 3. Thay thế biến {{ class }} thành tên DTO người dùng nhập
        $content = str_replace('{{ class }}', $name, $content);

        // 4. Tạo thư mục DTOs nếu nó chưa tồn tại
        File::ensureDirectoryExists(app_path('DTOs'));

        // 5. Ghi file và thông báo
        File::put($path, $content);

        $this->info("Tạo thành công: app/DTOs/{$name}.php");

        return Command::SUCCESS;
    }
}
