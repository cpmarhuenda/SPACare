<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use PDOException;

class CreateDatabase extends Command
{
    // Nombre del comando
    protected $signature = 'db:create {name?}';
    protected $description = 'Crea una base de datos si no existe';

    public function handle(): void
    {
        $dbName = $this->argument('name') ?? config('database.connections.mysql.database');
        $charset = config('database.connections.mysql.charset', 'utf8mb4');
        $collation = config('database.connections.mysql.collation', 'utf8mb4_unicode_ci');

        try {
            // Conexión sin especificar base de datos
            $query = "CREATE DATABASE IF NOT EXISTS `$dbName` CHARACTER SET $charset COLLATE $collation;";
            DB::statement($query);

            $this->info("✅ Base de datos '$dbName' creada o ya existente.");
        } catch (PDOException $e) {
            $this->error("❌ Error al crear la base de datos: " . $e->getMessage());
        }
    }
}
