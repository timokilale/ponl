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
        Schema::create('payment_intents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->decimal('amount', 16, 6)->comment('Amount in USDT with 6 decimal places');
            $table->string('reference_id', 100);
            $table->string('reference_type', 50);
            $table->string('status', 20)->default('pending');
            $table->text('metadata')->nullable();
            $table->timestamps();
            
            // Add index for faster lookups
            $table->index(['reference_id', 'reference_type']);
            $table->index(['user_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payment_intents');
    }
};
