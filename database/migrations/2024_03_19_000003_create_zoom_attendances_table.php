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
            $table->foreignId('zoom_online_class_id')->constrained('zoom_online_classes')->onDelete('cascade');
            $table->foreignId('student_id')->constrained('users')->onDelete('cascade');
            $table->enum('status', ['present', 'absent', 'late'])->default('absent');
            $table->dateTime('join_time')->nullable();
            $table->dateTime('leave_time')->nullable();
            $table->integer('duration')->default(0); // Duration in minutes
            $table->text('remarks')->nullable();
            $table->timestamps();
            $table->softDeletes();
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