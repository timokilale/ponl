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
        Schema::create('vip_levels', function (Blueprint $table) {
            $table->id();
            $table->string('name', 50);
            $table->integer('points_required');
            $table->decimal('reward_multiplier', 5, 2)->default(1.00);
            $table->integer('daily_tasks_limit')->default(10);
            $table->decimal('withdrawal_fee_discount', 5, 2)->default(0.00)->comment('Percentage discount on withdrawal fees');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vip_levels');
    }
};
