<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Add superadmin to role check constraint
        DB::statement('ALTER TABLE users DROP CONSTRAINT users_role_check');
        DB::statement("ALTER TABLE users ADD CONSTRAINT users_role_check
            CHECK (role IN ('superadmin', 'admin', 'manager', 'cashier'))");

        // Superadmin belongs to no shop
        DB::statement('ALTER TABLE users ALTER COLUMN shop_id DROP NOT NULL');
    }

    public function down(): void
    {
        DB::statement('ALTER TABLE users DROP CONSTRAINT users_role_check');
        DB::statement("ALTER TABLE users ADD CONSTRAINT users_role_check
            CHECK (role IN ('admin', 'manager', 'cashier'))");

        DB::statement('ALTER TABLE users ALTER COLUMN shop_id SET NOT NULL');
    }
};
