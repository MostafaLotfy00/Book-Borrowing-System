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
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->unsignedBigInteger('bookable_id'); // Polymorphic ID
            $table->string('bookable_type'); // Polymorphic type
            $table->date('start_date');
            $table->date('end_date');
            $table->timestamps();
            
            // Indexes for polymorphic relationship
            $table->index(['bookable_id', 'bookable_type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bookings');
    }
};
