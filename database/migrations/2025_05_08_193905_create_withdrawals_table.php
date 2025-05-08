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
        Schema::create('withdrawals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->decimal('amount', 16, 6);
            $table->decimal('fee', 16, 6)->default(0.000000);
            $table->string('method', 50);
            $table->string('status', 20)->default('pending')->comment('pending, approved, rejected, processing, completed');
            $table->text('payment_details')->nullable();
            $table->string('reference', 100)->nullable();
            $table->string('wallet_address', 255)->nullable();
            $table->string('network', 50)->nullable();
            $table->string('transaction_id', 255)->nullable();
            $table->string('blockchain_txid', 255)->nullable();
            $table->text('notes')->nullable();
            $table->timestamp('processed_at')->nullable();
            $table->timestamps();

            $table->index('user_id', 'idx_withdrawals_user');
            $table->index('status', 'idx_withdrawals_status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('withdrawals');
    }
};
