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
        Schema::create('task_completions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('task_id')->constrained()->onDelete('cascade');
            $table->string('status', 20)->default('pending')->comment('pending, approved, rejected');
            $table->text('proof')->nullable()->comment('URL or text proof of completion');
            $table->decimal('reward', 10, 2);
            $table->text('admin_notes')->nullable();
            $table->timestamp('completed_at')->useCurrent();
            $table->timestamp('reviewed_at')->nullable();
            $table->timestamps();

            $table->index('user_id', 'idx_task_completions_user');
            $table->index('task_id', 'idx_task_completions_task');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('task_completions');
    }
};
