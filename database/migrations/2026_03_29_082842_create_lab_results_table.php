<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lab_results', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pet_id')->constrained()->cascadeOnDelete();
            $table->foreignId('medical_record_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete(); // requested by
            $table->string('test_name');
            $table->date('test_date');
            $table->text('result_summary')->nullable();
            $table->string('file_path')->nullable(); // uploaded PDF/file
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index('test_date');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lab_results');
    }
};