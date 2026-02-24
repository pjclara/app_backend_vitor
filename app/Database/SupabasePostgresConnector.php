<?php

namespace App\Database;

use Illuminate\Database\Connectors\PostgresConnector;

class SupabasePostgresConnector extends PostgresConnector
{
    /**
     * Create a DSN string from a configuration.
     * Adds Supabase project reference via options parameter.
     */
    protected function getDsn(array $config): string
    {
        $dsn = parent::getDsn($config);

        $ref = env('SUPABASE_DB_REF');
        if ($ref) {
            $dsn .= ";options='reference={$ref}'";
        }

        return $dsn;
    }
}
