<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::rename('inquiries', 'registrations');

        Schema::table('registrations', function (Blueprint $table) {
            $table->string('kos_name')->nullable()->after('name');
            $table->string('email')->nullable()->after('kos_name');
        });

        // Migrate the lead status workflow into the registration/approval workflow:
        // new/contacted → pending, active → active, closed → rejected.
        DB::statement("ALTER TABLE registrations MODIFY COLUMN status ENUM('new','contacted','active','closed','pending','approved','rejected') NOT NULL DEFAULT 'pending'");
        DB::table('registrations')->whereIn('status', ['new', 'contacted'])->update(['status' => 'pending']);
        DB::table('registrations')->where('status', 'closed')->update(['status' => 'rejected']);
        DB::statement("ALTER TABLE registrations MODIFY COLUMN status ENUM('pending','approved','active','rejected') NOT NULL DEFAULT 'pending'");
    }

    public function down(): void
    {
        // Reverse the status enum back to the original lead workflow.
        DB::statement("ALTER TABLE registrations MODIFY COLUMN status ENUM('new','contacted','active','closed','pending','approved','rejected') NOT NULL DEFAULT 'new'");
        DB::table('registrations')->where('status', 'pending')->update(['status' => 'new']);
        DB::table('registrations')->where('status', 'rejected')->update(['status' => 'closed']);
        DB::table('registrations')->where('status', 'approved')->update(['status' => 'active']);
        DB::statement("ALTER TABLE registrations MODIFY COLUMN status ENUM('new','contacted','active','closed') NOT NULL DEFAULT 'new'");

        Schema::table('registrations', function (Blueprint $table) {
            $table->dropColumn(['kos_name', 'email']);
        });

        Schema::rename('registrations', 'inquiries');
    }
};
