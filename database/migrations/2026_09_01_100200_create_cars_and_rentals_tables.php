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
        Schema::create('cars', function (Blueprint $table) {
            $table->id();
            $table->string('license_plate')->unique();
            $table->foreignId('car_model_id')->constrained()->restrictOnDelete();
            $table->foreignId('car_category_id')->constrained()->restrictOnDelete();
            $table->foreignId('fuel_type_id')->constrained()->restrictOnDelete();
            $table->foreignId('transmission_type_id')->constrained()->restrictOnDelete();
            $table->foreignId('car_status_id')->constrained()->restrictOnDelete();
            $table->foreignId('location_id')->constrained()->restrictOnDelete();
            $table->unsignedSmallInteger('year');
            $table->string('color');
            $table->unsignedTinyInteger('seats');
            $table->unsignedTinyInteger('doors')->default(4);
            $table->decimal('daily_rate', 10, 2);
            $table->string('image_url')->nullable();
            $table->text('description')->nullable();
            $table->timestamps();
        });

        Schema::create('rentals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('car_id')->constrained()->restrictOnDelete();
            $table->foreignId('pickup_location_id')->constrained('locations')->restrictOnDelete();
            $table->foreignId('dropoff_location_id')->constrained('locations')->restrictOnDelete();
            $table->foreignId('rental_status_id')->constrained()->restrictOnDelete();
            $table->date('starts_on');
            $table->date('ends_on');
            $table->unsignedInteger('days');
            $table->decimal('daily_rate', 10, 2);
            $table->decimal('total_amount', 10, 2);
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['car_id', 'starts_on', 'ends_on']);
            $table->index(['user_id', 'starts_on']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rentals');
        Schema::dropIfExists('cars');
    }
};
