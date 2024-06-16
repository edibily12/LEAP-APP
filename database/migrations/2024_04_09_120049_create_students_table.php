<?php

use App\Enums\StudentLevel;
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
        Schema::create('students', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(\App\Models\User::class)->constrained()->cascadeOnDelete()->cascadeOnUpdate();
            $table->string('gender')->nullable();
            $table->date('dob')->nullable();
            $table->string('location')->nullable();
            $table->string('Language')->nullable();
            $table->text('about')->nullable();
            $table->string('level')->default(StudentLevel::HIGHER->value);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('students');
    }
};
