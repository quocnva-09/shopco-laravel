<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class MakeServiceCommand extends Command
{
    /**
     * Tên lệnh bạn sẽ gõ trong terminal.
     * {name} là tham số truyền vào (VD: ProductService)
     */
    protected $signature = 'make:service {name}';

    /**
     * Mô tả của lệnh (hiển thị khi gõ php artisan list)
     */
    protected $description = 'Create a new Service class';

    /**
     * Thực thi lệnh
     */
    public function handle()
    {
        // 1. Lấy tên Service người dùng nhập vào
        $name = $this->argument('name');

        // 2. Định nghĩa đường dẫn file
        $path = app_path("Services/{$name}.php");

        // 3. Kiểm tra xem file đã tồn tại chưa để tránh ghi đè mất code cũ
        if (File::exists($path)) {
            $this->error("Service {$name} đã tồn tại!");

            return Command::FAILURE;
        }

        // 4. Tạo thư mục app/Services nếu nó chưa có
        File::ensureDirectoryExists(app_path('Services'));

        // 5. Nội dung mẫu (Template) của file Service
        // Tự động suy ra tên Contract (VD: ProductService -> ProductContract)
        $contractName = str_replace('Service', 'ServiceInterface', $name);

        $content = <<<EOT
<?php

namespace App\Services;

use App\Contracts\\{$contractName};

class {$name} implements {$contractName}
{
    /**
     * Khởi tạo class
     */
    public function __construct()
    {
        // Inject repository hoặc các dependency khác vào đây
    }

    /**
     * Lấy danh sách dữ liệu (hỗ trợ phân trang & lọc)
     */
    public function getAll(array \$filters = [], int \$perPage = 15)
    {
        // TODO: Implement getAll() method.
    }

    /**
     * Lấy chi tiết một bản ghi theo ID
     */
    public function findById(int \$id)
    {
        // TODO: Implement findById() method.
    }

    /**
     * Tạo mới dữ liệu từ DTO
     */
    public function create(object \$dto)
    {
        // TODO: Implement create() method.
    }

    /**
     * Cập nhật dữ liệu từ DTO
     */
    public function update(\$model, object \$dto)
    {
        // TODO: Implement update() method.
    }

    /**
     * Xóa bản ghi
     */
    public function delete(int \$id)
    {
        // TODO: Implement delete() method.
    }
}
EOT;

        // 6. Ghi file và thông báo thành công
        File::put($path, $content);
        $this->info("Tạo thành công: app/Services/{$name}.php");

        return Command::SUCCESS;
    }
}
