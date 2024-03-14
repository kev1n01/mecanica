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
        Schema::create('items', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('code')->unique()->nullable();
            $table->integer('stock')->default(0)->nullable();
            $table->enum('type', ['product', 'service']);
            $table->decimal('sale_price', 10, 2)->nullable()->default(0);
            $table->decimal('purchase_price', 10, 2)->nullable()->default(0);
            $table->foreignId('unit_item_id')->nullable()->constrained()->cascadeOnDelete();
            $table->foreignId('brand_item_id')->nullable()->constrained()->cascadeOnDelete();
            $table->foreignId('category_item_id')->nullable()->constrained()->cascadeOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('items');
    }
};
