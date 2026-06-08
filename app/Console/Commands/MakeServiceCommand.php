<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class MakeServiceCommand extends Command
{
    /**
     * Command signature used in the terminal.
     * {name} is the input argument (e.g. ProductService)
     */
    protected $signature = 'make:service {name}';

    /**
     * Command description (shown when running php artisan list)
     */
    protected $description = 'Create a new Service class';

    /**
     * Execute the command
     */
    public function handle()
    {
        // 1. Get the Service name from the argument
        $name = $this->argument('name');

        // 2. Define the file path
        $path = app_path("Services/{$name}.php");

        // 3. Check if the file already exists to avoid overwriting existing code
        if (File::exists($path)) {
            $this->error("Service {$name} already exists!");

            return Command::FAILURE;
        }

        // 4. Create the app/Services directory if it does not exist
        File::ensureDirectoryExists(app_path('Services'));

        // 5. Template content for the Service file
        // Automatically infer the Contract name (e.g. ProductService -> ProductServiceInterface)
        $contractName = str_replace('Service', 'ServiceInterface', $name);

        $content = <<<EOT
<?php

namespace App\Services;

use App\Contracts\\{$contractName};

class {$name} implements {$contractName}
{
    /**
     * Class constructor
     */
    public function __construct()
    {
        // Inject repositories or other dependencies here
    }

    /**
     * Retrieve a paginated and filtered list
     */
    public function getAll(array \$filters = [], int \$perPage = 15)
    {
        // TODO: Implement getAll() method.
    }

    /**
     * Retrieve a single record by ID
     */
    public function findById(int \$id)
    {
        // TODO: Implement findById() method.
    }

    /**
     * Create a new record from a DTO
     */
    public function create(object \$dto)
    {
        // TODO: Implement create() method.
    }

    /**
     * Update a record from a DTO
     */
    public function update(\$model, object \$dto)
    {
        // TODO: Implement update() method.
    }

    /**
     * Delete a record
     */
    public function delete(int \$id)
    {
        // TODO: Implement delete() method.
    }
}
EOT;

        // 6. Write the file and report success
        File::put($path, $content);
        $this->info("Created successfully: app/Services/{$name}.php");

        return Command::SUCCESS;
    }
}
