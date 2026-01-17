<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use PDO;

class MigrateSqliteToMysql extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'db:migrate-sqlite-to-mysql 
                            {--force : Force migration without confirmation}
                            {--skip-tables= : Comma-separated list of tables to skip}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Migrate all data from SQLite database to MySQL database';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting SQLite to MySQL migration...');
        $this->newLine();

        // Check if SQLite database exists
        $sqlitePath = database_path('database.sqlite');
        if (!file_exists($sqlitePath)) {
            $this->error("SQLite database not found at: {$sqlitePath}");
            return Command::FAILURE;
        }

        // Check MySQL connection
        try {
            DB::connection('mysql')->getPdo();
            $this->info('✓ MySQL connection successful');
        } catch (\Exception $e) {
            $this->error('✗ MySQL connection failed: ' . $e->getMessage());
            $this->error('Please configure MySQL in your .env file');
            return Command::FAILURE;
        }

        // Check SQLite connection
        try {
            $sqliteConnection = new PDO('sqlite:' . $sqlitePath);
            $sqliteConnection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->info('✓ SQLite connection successful');
        } catch (\Exception $e) {
            $this->error('✗ SQLite connection failed: ' . $e->getMessage());
            return Command::FAILURE;
        }

        $this->newLine();

        // Get all tables from SQLite
        $tables = $this->getSqliteTables($sqliteConnection);
        
        if (empty($tables)) {
            $this->warn('No tables found in SQLite database');
            return Command::SUCCESS;
        }

        $this->info('Found ' . count($tables) . ' tables in SQLite database');
        $this->newLine();

        // Get tables to skip
        $skipTables = $this->option('skip-tables') 
            ? explode(',', $this->option('skip-tables')) 
            : [];
        $skipTables = array_map('trim', $skipTables);

        // Filter out skipped tables
        $tables = array_filter($tables, function($table) use ($skipTables) {
            return !in_array($table, $skipTables);
        });

        // Confirm migration
        if (!$this->option('force')) {
            $this->table(['Table'], array_map(fn($t) => [$t], $tables));
            $this->newLine();
            
            if (!$this->confirm('Do you want to migrate all data from SQLite to MySQL?', true)) {
                $this->info('Migration cancelled');
                return Command::SUCCESS;
            }
        }

        $this->newLine();
        $this->info('Starting data migration...');
        $this->newLine();

        $successCount = 0;
        $errorCount = 0;
        $skippedCount = 0;

        foreach ($tables as $table) {
            try {
                $this->line("Migrating table: <comment>{$table}</comment>");
                
                // Check if table exists in MySQL
                if (!Schema::connection('mysql')->hasTable($table)) {
                    $this->warn("  ⚠ Table '{$table}' does not exist in MySQL. Skipping...");
                    $skippedCount++;
                    continue;
                }

                // Get data from SQLite
                $data = $this->getSqliteTableData($sqliteConnection, $table);
                
                if (empty($data)) {
                    $this->line("  ℹ No data to migrate");
                    continue;
                }

                // Disable foreign key checks temporarily
                DB::connection('mysql')->statement('SET FOREIGN_KEY_CHECKS=0');

                // Clear existing data to avoid duplicates
                DB::connection('mysql')->table($table)->truncate();

                // Insert data in chunks
                $chunkSize = 500;
                $chunks = array_chunk($data, $chunkSize);
                
                foreach ($chunks as $chunk) {
                    DB::connection('mysql')->table($table)->insert($chunk);
                }

                // Re-enable foreign key checks
                DB::connection('mysql')->statement('SET FOREIGN_KEY_CHECKS=1');

                $this->info("  ✓ Migrated " . count($data) . " rows");
                $successCount++;

            } catch (\Exception $e) {
                $this->error("  ✗ Error migrating table '{$table}': " . $e->getMessage());
                $errorCount++;
                
                // Re-enable foreign key checks in case of error
                try {
                    DB::connection('mysql')->statement('SET FOREIGN_KEY_CHECKS=1');
                } catch (\Exception $ex) {
                    // Ignore
                }
            }
        }

        $this->newLine();
        $this->info('Migration Summary:');
        $this->table(
            ['Status', 'Count'],
            [
                ['Success', $successCount],
                ['Errors', $errorCount],
                ['Skipped', $skippedCount],
            ]
        );

        if ($errorCount > 0) {
            $this->newLine();
            $this->warn('Some tables failed to migrate. Please check the errors above.');
            return Command::FAILURE;
        }

        $this->newLine();
        $this->info('✓ Migration completed successfully!');
        return Command::SUCCESS;
    }

    /**
     * Get all table names from SQLite database
     */
    private function getSqliteTables(PDO $connection): array
    {
        $tables = [];
        $result = $connection->query("SELECT name FROM sqlite_master WHERE type='table' AND name NOT LIKE 'sqlite_%' ORDER BY name");
        
        while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
            $tables[] = $row['name'];
        }
        
        return $tables;
    }

    /**
     * Get all data from a SQLite table
     */
    private function getSqliteTableData(PDO $connection, string $table): array
    {
        $data = [];
        $result = $connection->query("SELECT * FROM `{$table}`");
        
        while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
            // Convert SQLite types to proper PHP types
            $processedRow = [];
            foreach ($row as $key => $value) {
                // Handle NULL values
                if ($value === null) {
                    $processedRow[$key] = null;
                } 
                // Handle other types
                else {
                    $processedRow[$key] = $value;
                }
            }
            $data[] = $processedRow;
        }
        
        return $data;
    }
}

