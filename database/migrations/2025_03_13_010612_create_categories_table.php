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
    Schema::create('categories', function (Blueprint $table) {
        $table->id();
        
        // Category name and unique slug
        $table->string('name');
        $table->string('slug')->unique();
        
        // Parent category (nullable for top-level categories)
        $table->foreignId('parent_id')
            ->nullable()
            ->constrained('categories')
            ->cascadeOnDelete();
        
        // Visibility status
        $table->boolean('is_visible')->default(false);
        
        // Category description (optional)
        $table->longText('description')->nullable();
        
        // Timestamps (created_at, updated_at)
        $table->timestamps();
    });
}


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('categories');
    }
};
