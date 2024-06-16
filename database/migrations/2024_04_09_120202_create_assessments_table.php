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
        Schema::create('assessments', function (Blueprint $table) {
            $table->id();
            $table->string('type')->index();
            $table->string('tag')->nullable();
            $table->integer('status')->default('0')->index(); // 0 for pending, 1 for active and 2 for expired,
            $table->integer('total_questions')->nullable();
            $table->double('total_marks')->nullable();
            $table->text('instructions')->nullable();
            $table->boolean('shuffled')->default('0');
            $table->date('start_date')->nullable();
            $table->time('start_time')->nullable();
            $table->date('expire_date')->nullable();
            $table->time('expire_time')->nullable();
            $table->string('created_by')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('assessments');
    }
};
