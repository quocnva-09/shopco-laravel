<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class MakeContractCommand extends Command
{
    /**
     * Tên lệnh gõ trong terminal.
     */
    protected $signature = 'make:contract {name}';

    /**
     * Mô tả của lệnh
     */
    protected $description = 'Create a new Contract (Interface)';

    /**
     * Thực thi lệnh
     */
    public function handle()
    {
        $name = $this->argument('name');
        $path = app_path("Contracts/{$name}.php");

        // 1. Đọc nội dung từ file khuôn mẫu
        $stubPath = base_path('stubs/contract.stub');
        if (! File::exists($stubPath)) {
            $this->error('Không tìm thấy file stubs/contract.stub');

            return Command::FAILURE;
        }

        $content = File::get($stubPath);

        // 2. Thay thế biến {{ class }} bằng tên thực tế
        $content = str_replace('{{ class }}', $name, $content);

        // 3. Tạo file
        File::ensureDirectoryExists(app_path('Contracts'));
        File::put($path, $content);

        $this->info("Tạo thành công: app/Contracts/{$name}.php với các hàm CRUD mặc định!");

        return Command::SUCCESS;
    }
}
