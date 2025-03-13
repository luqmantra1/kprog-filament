<?php

use App\Models\Brand;
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
    Schema::create('products', function (Blueprint $table) {
        $table->id();
        
        // Foreign key reference to 'brands' table
        $table->foreignId('brand_id')
            ->constrained('brands')
            ->cascadeOnDelete();
        
        // Product information
        $table->string('name');
        $table->string('slug')->unique();
        $table->string('sku')->unique();
        $table->string('image');
        $table->longText('description')->nullable();
        
        // Quantity and price
        $table->unsignedBigInteger('quantity');
        $table->decimal('price', 10, 2);
        
        // Visibility and featured flags
        $table->boolean('is_visible')->default(false);
        $table->boolean('is_featured')->default(false);
        
        // Product type (enum) with a default value
        $table->enum('type', ['deliverable', 'downloadable'])
            ->default('deliverable');
        
        // Publishing date
        $table->date('published_at');
        
        // Timestamps (created_at, updated_at)
        $table->timestamps();
    });
}


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
