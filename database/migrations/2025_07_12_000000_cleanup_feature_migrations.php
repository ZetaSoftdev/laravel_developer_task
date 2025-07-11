<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        // Remove the duplicate migration from migrations table
        DB::table('migrations')
            ->where('migration', '2025_07_02_052542_add_deleted_at_to_features_table')
            ->delete();

        // Make sure our correct migration is marked as run
        if (!DB::table('migrations')->where('migration', '2024_05_21_094713_add_soft_deletes_to_features_table')->exists()) {
            DB::table('migrations')->insert([
                'migration' => '2024_05_21_094713_add_soft_deletes_to_features_table',
                'batch' => DB::table('migrations')->max('batch')
            ]);
        }
    }

    public function down()
    {
        // No need for down as this is a cleanup migration
    }
}; 