<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ImportSql extends Command
{
    protected $signature = 'import:sql';
    protected $description = 'Import SQL file via Laravel DB connection';

    public function handle()
    {
        $sqlFiles = [
            base_path('database/seeders/import/cities.sql'),
            base_path('database/seeders/import/districts.sql'),
            //base_path('database/seeders/import/locations.sql'),
            base_path('database/seeders/import/quarters.sql'),
        ];

        foreach ($sqlFiles as $file) {
            if (!file_exists($file)) {
                $this->error("File not found: $file");
                continue;
            }

            $this->info("Importing $file...");

            // SQL dosyasını oku
            $sql = file_get_contents($file);

            try {
                // SQL'i doğrudan çalıştır
                DB::unprepared($sql);
                $this->info("✅ Imported $file");
            } catch (\Exception $e) {
                $this->error("❌ Failed to import $file: " . $e->getMessage());
            }
        }

        $this->info("Import işlemi tamamlandı.");
    }
}
