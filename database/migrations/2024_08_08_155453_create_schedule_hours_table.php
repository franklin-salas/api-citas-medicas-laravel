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
        Schema::create('schedule_hours', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('schedule_day_id');
            $table->foreign('schedule_day_id')->references('id')->on('schedule_days')->onDelete('cascade');
            $table->unsignedBigInteger('doctor_schedule_id');
            $table->foreign('doctor_schedule_id')->references('id')->on('doctor_schedules');
         

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('schedule_hours');
    }
};
