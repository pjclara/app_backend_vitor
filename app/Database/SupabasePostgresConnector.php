<?php

namespace App\Database;

use Illuminate\Database\Connectors\PostgresConnector;

class SupabasePostgresConnector extends PostgresConnector
{
    /**
     * Create a DSN string from a configuration.
     * Note: For Supabase pooler, the username must be in format: postgres.project_ref
     * This is configured in .env as DB_USERNAME=postgres.emwgjilzdxlvpkrvkhmc
     */
    protected function getDsn(array $config): string
    {
        return parent::getDsn($config);
    }
}


