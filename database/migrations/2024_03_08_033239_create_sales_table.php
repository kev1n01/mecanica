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
        Schema::create('sales', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->constrained('customers')->cascadeOnDelete();
            $table->string('payment_type')->nullable();
            $table->string('payment_method')->nullable();
            $table->string('type')->nullable();
            $table->decimal('total', 10, 2)->nullable();
            $table->decimal('cash_paid', 10, 2)->nullable()->default(0.00);
            $table->enum('status', ['pagado', 'no pagado'])->nullable();
            $table->date('date')->nullable();
            $table->text('observation')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sales');
    }
};
