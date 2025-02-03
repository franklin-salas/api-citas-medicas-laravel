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
        Schema::create('appointments', function (Blueprint $table) {
            $table->id();
            $table->date('date_appointment');
            $table->string('hour',20);
            $table->enum('status', ['ACTIVO', 'INACTIVO','PAGADO'])->default('ACTIVO');
            $table->unsignedBigInteger('specialty_id',)->nullable();
            $table->unsignedBigInteger('doctor_id',)->nullable();
            $table->unsignedBigInteger('patient_id',)->nullable();
            $table->unsignedBigInteger('schedule_hour_id',)->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->unsignedBigInteger('deleted_by')->nullable();
            $table->softDeletes();
            $table->timestamps();
            $table->foreign('specialty_id')
            ->references('id') 
            ->on('specialties')
            ->onUpdate('cascade');
            $table->foreign('patient_id')
            ->references('id') 
            ->on('patients')
            ->onUpdate('cascade');
            $table->foreign('doctor_id')
            ->references('id') 
            ->on('users')
            ->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('appointments');
    }
};
