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
    Schema::create('brands', function (Blueprint $table) {
        $table->id();

        // Brand information
        $table->string('name');
        $table->string('slug')->unique();
        $table->string('url')->nullable();
        $table->string('primary_hex')->nullable();

        // Visibility status
        $table->boolean('is_visible')->default(false);

        // Optional brand description
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
        Schema::dropIfExists('brands');
    }
};
