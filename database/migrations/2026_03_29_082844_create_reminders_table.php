<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reminders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_id')->constrained()->cascadeOnDelete();
            $table->string('related_type')->nullable();        // polymorphic
            $table->unsignedBigInteger('related_id')->nullable();
            $table->enum('type', ['appointment', 'vaccination', 'follow_up', 'reorder']);
            $table->enum('channel', ['sms', 'email']);
            $table->text('message');
            $table->dateTime('scheduled_at');
            $table->dateTime('sent_at')->nullable();
            $table->enum('status', ['pending', 'sent', 'failed'])->default('pending');
            $table->text('failure_reason')->nullable();
            $table->timestamps();

            $table->index(['related_type', 'related_id']);
            $table->index('scheduled_at');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reminders');
    }
};