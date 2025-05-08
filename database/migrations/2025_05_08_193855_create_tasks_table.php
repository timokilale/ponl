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
        Schema::create('tasks', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('platform', 50);
            $table->decimal('reward', 10, 2);
            $table->string('time_required', 50)->nullable();
            $table->string('difficulty', 20)->nullable();
            $table->integer('vip_level_required')->default(1);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index('vip_level_required', 'idx_tasks_vip_level');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tasks');
    }
};
