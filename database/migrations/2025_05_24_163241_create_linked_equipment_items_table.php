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
        Schema::create('linked_equipment_items', function (Blueprint $table) {
            $table->id();
            
            $table->foreignId('fund_id')->constrained('funds')->onDelete('cascade');
            
            $table->string('original_property_no');
            $table->string('reference_mmdd', 5);
            $table->string('new_property_no', 4); // Made it more specific since it's always 4 digits
            $table->string('year', 4);
            $table->string('location', 2)->default('00');
            $table->timestamps();
            
            // Indexes for performance
            $table->index(['reference_mmdd']);
            $table->index(['fund_id', 'reference_mmdd']);
            $table->index(['original_property_no']);
            
            // Composite unique constraint: each fund/reference combination can have its own sequence
            $table->unique(['fund_id', 'reference_mmdd', 'new_property_no'], 'unique_property_per_fund_ref');
         
        });
        
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('linked_equipment_items');
    }
};
