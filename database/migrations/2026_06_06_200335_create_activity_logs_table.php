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
        Schema::create('activity_logs', function (Blueprint $table): void {
            $table->id();
            // Who performed the action (null = system/scheduler)
            $table->foreignId('causer_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('causer_name')->nullable(); // snapshot nama saat aksi terjadi
            // What was affected
            $table->string('subject_type')->nullable(); // e.g. App\Models\Billing
            $table->unsignedBigInteger('subject_id')->nullable();
            // The action
            $table->string('event');          // e.g. billing.paid, juragan.suspended
            $table->text('description');      // Human-readable log message
            $table->json('properties')->nullable(); // extra context (amounts, statuses, etc.)
            $table->timestamp('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('activity_logs');
    }
};
