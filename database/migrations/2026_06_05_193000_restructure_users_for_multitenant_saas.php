<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Widen the role enum to a superset so existing 'admin' rows stay valid
        //    while we migrate them to the new 'developer' role.
        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('admin','tenant','developer','juragan') NOT NULL DEFAULT 'tenant'");

        // 2. Migrate the existing single admin into the new super-admin role.
        DB::table('users')->where('role', 'admin')->update(['role' => 'developer']);

        // 3. Lock the enum down to the final three-tier role set.
        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('developer','juragan','tenant') NOT NULL DEFAULT 'tenant'");

        // 4. Multi-tenant columns.
        Schema::table('users', function (Blueprint $table) {
            // Anak kos point to their juragan; juragan & developer keep this null.
            $table->foreignId('juragan_id')
                ->nullable()
                ->after('role')
                ->constrained('users')
                ->nullOnDelete();

            // Juragan-only identity (kos name + login namespace slug, e.g. "mutiara").
            $table->string('kos_name')->nullable()->after('juragan_id');
            $table->string('kos_slug')->nullable()->unique()->after('kos_name');

            // Juragan-only subscription package + anak-kos account capacity.
            $table->enum('plan', ['lite', 'pro', 'custom'])->nullable()->after('kos_slug');
            $table->unsignedSmallInteger('room_quota')->default(0)->after('plan');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropConstrainedForeignId('juragan_id');
            $table->dropColumn(['kos_name', 'kos_slug', 'plan', 'room_quota']);
        });

        // Restore the original two-role enum and map developer back to admin.
        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('admin','tenant','developer','juragan') NOT NULL DEFAULT 'tenant'");
        DB::table('users')->where('role', 'developer')->update(['role' => 'admin']);
        DB::table('users')->where('role', 'juragan')->update(['role' => 'admin']);
        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('admin','tenant') NOT NULL DEFAULT 'tenant'");
    }
};
