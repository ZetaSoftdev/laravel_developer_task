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
        Schema::create('zoom_attendances', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('zoom_online_class_id');
            $table->unsignedBigInteger('student_id');
            $table->dateTime('join_time')->nullable();
            $table->dateTime('leave_time')->nullable();
            $table->integer('duration')->default(0); // Duration in minutes
            $table->string('status')->default('absent'); // present, absent, late
            $table->text('remarks')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('zoom_attendances');
    }
};
