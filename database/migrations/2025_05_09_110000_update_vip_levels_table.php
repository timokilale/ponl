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
        Schema::table('vip_levels', function (Blueprint $table) {
            // Add the new deposit_required column
            $table->decimal('deposit_required', 16, 2)->after('name')->default(0);
            
            // Copy data from points_required to deposit_required if needed
            // This is done in a separate step after migration
        });
        
        // Update the data
        DB::statement('UPDATE vip_levels SET deposit_required = points_required');
        
        Schema::table('vip_levels', function (Blueprint $table) {
            // Remove the old points_required column
            $table->dropColumn('points_required');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('vip_levels', function (Blueprint $table) {
            // Add back the points_required column
            $table->integer('points_required')->after('name')->default(0);
            
            // Copy data from deposit_required to points_required
            DB::statement('UPDATE vip_levels SET points_required = deposit_required');
            
            // Remove the deposit_required column
            $table->dropColumn('deposit_required');
        });
    }
};
