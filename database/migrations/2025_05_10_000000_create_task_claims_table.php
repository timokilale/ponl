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
        Schema::create('task_claims', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('task_id')->constrained()->onDelete('cascade');
            $table->decimal('reward', 10, 2);
            $table->timestamp('claimed_at')->useCurrent();
            $table->timestamp('expires_at')->nullable(false)->useCurrent();
            $table->timestamps();

            // Add indexes
            $table->index('user_id', 'idx_task_claims_user');
            $table->index('task_id', 'idx_task_claims_task');
            $table->index('expires_at', 'idx_task_claims_expires');

            // Add unique constraint to prevent multiple active claims for the same task by the same user
            $table->unique(['user_id', 'task_id'], 'uk_user_task_claim');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('task_claims');
    }
};
