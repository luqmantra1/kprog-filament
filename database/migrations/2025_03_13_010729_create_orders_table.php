<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Ramsey\Uuid\Type\Decimal;

return new class extends Migration
{
    /**
 * Run the migrations.
 */
public function up(): void
{
    Schema::create('orders', function (Blueprint $table) {
        $table->id();
        
        // Foreign key reference to 'customers' table
        $table->foreignId('customer_id')
              ->constrained('customers')
              ->cascadeOnDelete();
        
        // Unique order number
        $table->string('number')->unique();
        
        // Total price with 10 digits and 2 decimal places
        $table->decimal('total_price', 10, 2);
        
        // Correct enum syntax
        $table->enum('status', ['pending', 'processing', 'completed', 'declined'])
              ->default('pending');
        
        // Optional shipping price
        $table->decimal('shipping_price', 10, 2)->nullable();
        
        // Notes (can be a large text block)
        $table->longText('notes');
        
        // Soft deletes and timestamps
        $table->softDeletes();
        $table->timestamps();
    });
}


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
