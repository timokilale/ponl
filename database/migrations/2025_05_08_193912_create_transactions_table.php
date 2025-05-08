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
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->decimal('amount', 16, 6);
            $table->string('type', 10)->comment('credit or debit');
            $table->text('description')->nullable();
            $table->integer('reference_id')->nullable()->comment('ID of related entity (task, withdrawal, etc)');
            $table->string('reference_type', 50)->nullable()->comment('task_completion, withdrawal, reward, referral, withdrawal_refund, etc.');
            $table->string('status', 20)->default('completed');
            $table->decimal('balance_after', 16, 6)->nullable()->comment('User balance after this transaction');
            $table->string('wallet_address', 255)->nullable();
            $table->string('network', 50)->nullable();
            $table->string('blockchain_txid', 255)->nullable();
            $table->timestamp('created_at')->useCurrent();

            $table->index('user_id', 'idx_transactions_user');
            $table->index('type', 'idx_transactions_type');
            $table->index(['reference_type', 'reference_id'], 'idx_transactions_reference');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
