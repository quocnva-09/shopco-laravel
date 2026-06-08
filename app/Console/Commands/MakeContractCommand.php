<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class MakeContractCommand extends Command
{
    /**
     * Command signature used in the terminal.
     */
    protected $signature = 'make:contract {name}';

    /**
     * Command description
     */
    protected $description = 'Create a new Contract (Interface)';

    /**
     * Execute the command
     */
    public function handle()
    {
        $name = $this->argument('name');
        $path = app_path("Contracts/{$name}.php");

        // 1. Read the stub template file
        $stubPath = base_path('stubs/contract.stub');
        if (! File::exists($stubPath)) {
            $this->error('Stub file not found at stubs/contract.stub');

            return Command::FAILURE;
        }

        $content = File::get($stubPath);

        // 2. Replace the {{ class }} placeholder with the actual name
        $content = str_replace('{{ class }}', $name, $content);

        // 3. Create the file
        File::ensureDirectoryExists(app_path('Contracts'));
        File::put($path, $content);

        $this->info("Created successfully: app/Contracts/{$name}.php with default CRUD methods!");

        return Command::SUCCESS;
    }
}
