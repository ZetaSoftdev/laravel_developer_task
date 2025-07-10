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
        Schema::create('zoom_online_classes', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('school_id');
            $table->unsignedBigInteger('teacher_id'); // User ID of the teacher
            $table->unsignedBigInteger('class_section_id')->nullable(); // Optional - can be for a specific class
            $table->unsignedBigInteger('subject_id')->nullable(); // Optional - can be for a specific subject
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('meeting_id'); // Zoom meeting ID
            $table->string('password')->nullable(); // Zoom meeting password
            $table->string('join_url'); // URL for joining the meeting
            $table->string('start_url')->nullable(); // URL for starting the meeting (teacher only)
            $table->dateTime('start_time');
            $table->dateTime('end_time');
            $table->integer('duration')->default(40); // Duration in minutes
            $table->boolean('is_recurring')->default(false);
            $table->string('recurrence_type')->nullable(); // daily, weekly, monthly
            $table->integer('recurring_interval')->nullable(); // every X days/weeks/months
            $table->string('status')->default('scheduled'); // scheduled, started, completed, cancelled
            $table->unsignedBigInteger('session_year_id');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('zoom_online_classes');
    }
};
