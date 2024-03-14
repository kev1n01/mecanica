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
        Schema::create('vehicles', function (Blueprint $table) {
            $table->id();
            $table->string('plate')->unique();
            $table->foreignId('customer_id')->nullable()->constrained('customers');
            $table->foreignId('type_vehicle_id')->nullable()->constrained('type_vehicles')->nullOnDelete();
            $table->foreignId('brand_vehicle_id')->nullable()->constrained('brand_vehicles')->nullOnDelete();
            $table->foreignId('model_vehicle_id')->nullable()->constrained('model_vehicles')->nullOnDelete();
            $table->foreignId('color_vehicle_id')->nullable()->constrained('color_vehicles')->nullOnDelete();
            $table->string('year')->nullable();
            $table->string('odo')->nullable();
            $table->string('note')->nullable();
            $table->json('images')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vehicles');
    }
};
