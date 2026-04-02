<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Schema::create('activity_logs', function (Blueprint $table) {
        //     $table->id();
        //     $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
        //     $table->string('subject_type');
        //     $table->unsignedBigInteger('subject_id');
        //     $table->string('action', 64);
        //     $table->json('properties')->nullable();
        //     $table->string('ip_address', 45)->nullable();
        //     $table->timestamps();

        //     $table->index(['subject_type', 'subject_id']);
        // });

        Schema::create('activity_logs', function (Blueprint $table) {
            $table->id();
        
            $table->foreignId('workspace_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
        
            $table->string('action');
            $table->string('entity_type');
            $table->unsignedBigInteger('entity_id');
        
            $table->json('metadata')->nullable();
        
            $table->timestamps();
        
            $table->index(['workspace_id']);
            $table->index(['entity_type', 'entity_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('activity_logs');
    }
};
