<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class MakeDTOCommand extends Command
{
    /**
     * Command signature used in the terminal
     */
    protected $signature = 'make:dto {name}';

    /**
     * Command description
     */
    protected $description = 'Create a new Data Transfer Object (DTO)';

    /**
     * Execute the command
     */
    public function handle()
    {
        $name = $this->argument('name');

        // Define the standard file path: app/DTOs/FileName.php
        $path = app_path("DTOs/{$name}.php");

        // 1. Check if the file already exists
        if (File::exists($path)) {
            $this->error("DTO {$name} already exists!");

            return Command::FAILURE;
        }

        // 2. Read the stub template file
        $stubPath = base_path('stubs/dto.stub');
        if (! File::exists($stubPath)) {
            $this->error('Stub file not found at stubs/dto.stub!');

            return Command::FAILURE;
        }

        $content = File::get($stubPath);

        // 3. Replace the {{ class }} placeholder with the user-provided DTO name
        $content = str_replace('{{ class }}', $name, $content);

        // 4. Create the DTOs directory if it does not exist
        File::ensureDirectoryExists(app_path('DTOs'));

        // 5. Write the file and report
        File::put($path, $content);

        $this->info("Created successfully: app/DTOs/{$name}.php");

        return Command::SUCCESS;
    }
}
