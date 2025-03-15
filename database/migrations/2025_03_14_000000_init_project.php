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
        $connType = env('DB_CONNECTION');
        if (in_array($connType, ['mysql', 'mariadb'])) { $this->mysqlUp(); }
        if ($connType === 'pgsql') { $this->postgresUp(); }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("DROP TABLE IF EXISTS books");
        DB::statement("DROP TABLE IF EXISTS settled_orders");
        DB::statement("DROP TABLE IF EXISTS timetables");
    }

    private function postgresUp(): void
    {
        DB::statement("CREATE TABLE IF NOT EXISTS timetables (
            id UUID NOT NULL DEFAULT gen_random_uuid() PRIMARY KEY,
            product_id UUID NOT NULL,
            time TIMESTAMPZ NOT NULL
        )");
        DB::statement("CREATE TABLE IF NOT EXISTS settled_orders (
            id UUID NOT NULL DEFAULT gen_random_uuid() PRIMARY KEY,
            customers_phone VARCHAR(20) NOT NULL,
            product_id UUID NOT NULL,
            start_time TIMESTAMPTZ NOT NULL,
            end_time TIMESTAMPTZ NOT NULL,
            created_at TIMESTAMPTZ NOT NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMPTZ NOT NULL DEFAULT CURRENT_TIMESTAMP
        )");
        DB::statement("CREATE TABLE IF NOT EXISTS books (
            id UUID NOT NULL DEFAULT gen_random_uuid() PRIMARY KEY,
            settled_order_id UUID CONSTRAINT fk_books_settled_order_id REFERENCES settled_orders(id) ON UPDATE CASCADE ON DELETE SET NULL,
            book_code VARCHAR(16) NOT NULL CONSTRAINT uk_books_book_code UNIQUE,
            status_id UUID NOT NULL DEFAULT 'ab1dd42cc5714e43826f9dc782c25067',
            created_at TIMESTAMPTZ NOT NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMPTZ NOT NULL DEFAULT CURRENT_TIMESTAMP
        )");
        DB::statement("CREATE INDEX IF NOT EXISTS idx_timetables_time ON timetables(time)");
    }

    private function mysqlUp(): void
    {
        DB::statement("CREATE TABLE IF NOT EXISTS timetables (
            id CHAR(36) NOT NULL PRIMARY KEY,
            product_id CHAR(36) NOT NULL,
            time TIMESTAMP NOT NULL
        );");
        DB::statement("CREATE TABLE IF NOT EXISTS settled_orders (
            id CHAR(36) NOT NULL PRIMARY KEY,
            customers_phone VARCHAR(20) NOT NULL,
            product_id CHAR(36) NOT NULL,
            start_time TIMESTAMP NOT NULL,
            end_time TIMESTAMP NOT NULL,
            created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        )");
        DB::statement("CREATE TABLE IF NOT EXISTS books (
            id CHAR(36) NOT NULL PRIMARY KEY,
            settled_order_id CHAR(36),
            book_code VARCHAR(16) NOT NULL,
            status_id CHAR(36) NOT NULL DEFAULT 'ab1dd42cc5714e43826f9dc782c25067',
            created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            CONSTRAINT fk_books_settled_order_id FOREIGN KEY (settled_order_id) REFERENCES settled_orders(id) ON UPDATE CASCADE ON DELETE SET NULL,
            CONSTRAINT uk_books_book_code UNIQUE (book_code)
        )");
        DB::statement("CREATE INDEX idx_timetables_time ON timetables(time)");
    }
};
