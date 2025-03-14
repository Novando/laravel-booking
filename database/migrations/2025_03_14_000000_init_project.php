<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::raw(`CREATE TABLE IF NOT EXISTS timetables (
            id UUID NOT NULL DEFAULT gen_random_uuid() PRIMARY KEY,
            product_id UUID NOT NULL,
            time TIMESTAMPZ NOT NULL,
        )`);
        DB::raw(`CREATE TABLE IF NOT EXISTS settled_orders (
            id UUID NOT NULL DEFAULT gen_random_uuid() PRIMARY KEY,
            customers_phone VARCHAR(20) NOT NULL,
            product_id UUID NOT NULL,
            start_time TIMESTAMPTZ NOT NULL,
            end_time TIMESTAMPTZ NOT NULL,
            created_at TIMESTAMPTZ NOT NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMPTZ NOT NULL DEFAULT CURRENT_TIMESTAMP
        )`);
        DB::raw(`CREATE TABLE IF NOT EXISTS books (
            id UUID NOT NULL DEFAULT gen_random_uuid() PRIMARY KEY,
            settled_order_id UUID CONSTRAINT fk_books_settled_order_id REFERENCES settled_orders(id) ON UPDATE CASCADE ON DELETE SET NULL,
            book_code VARCHAR(16) NOT NULL CONSTRAINT uk_books_book_code UNIQUE,
            status_id UUID NOT NULL DEFAULT 'ab1dd42cc5714e43826f9dc782c25067',
            created_at TIMESTAMPTZ NOT NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMPTZ NOT NULL DEFAULT CURRENT_TIMESTAMP
        )`);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::raw('DROP TABLE IF EXISTS books');
        DB::raw('DROP TABLE IF EXISTS settled_orders');
        DB::raw('DROP TABLE IF EXISTS timetables');
    }
};
