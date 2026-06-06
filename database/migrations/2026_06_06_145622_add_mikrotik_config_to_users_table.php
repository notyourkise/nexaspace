<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table): void {
            // Per-juragan MikroTik router config; null = use global .env fallback
            $table->string('mikrotik_host')->nullable()->after('suspended_at');
            $table->integer('mikrotik_port')->nullable()->after('mikrotik_host');
            $table->string('mikrotik_user')->nullable()->after('mikrotik_port');
            $table->string('mikrotik_pass')->nullable()->after('mikrotik_user');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table): void {
            $table->dropColumn(['mikrotik_host', 'mikrotik_port', 'mikrotik_user', 'mikrotik_pass']);
        });
    }
};
