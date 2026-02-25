<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // For PostgreSQL, we need to use raw SQL to properly change the column type
        // Using enum() with ->change() generates invalid PostgreSQL syntax
        DB::statement(<<<SQL
            ALTER TABLE exercises
            ALTER COLUMN difficulty TYPE varchar(255) USING difficulty::text
        SQL);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert back to integer type
        DB::statement(<<<SQL
            ALTER TABLE exercises
            ALTER COLUMN difficulty TYPE text USING CASE 
                WHEN difficulty = 'easy' THEN 'easy'
                WHEN difficulty = 'medium' THEN 'medium'
                WHEN difficulty = 'hard' THEN 'hard'
                ELSE 'easy'
            END
        SQL);
    }
};
