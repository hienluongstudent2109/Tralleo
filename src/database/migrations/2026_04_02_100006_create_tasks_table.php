<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Schema::create('tasks', function (Blueprint $table) {
        //     $table->id();
        //     $table->foreignId('project_id')->constrained()->cascadeOnDelete();
        //     $table->foreignId('column_id')->constrained()->cascadeOnDelete();
        //     $table->string('title');
        //     $table->text('description')->nullable();
        //     $table->unsignedInteger('position')->default(0);
        //     $table->string('status', 32)->nullable();
        //     $table->timestamp('due_at')->nullable();
        //     $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
        //     $table->timestamps();
        //     $table->softDeletes();
        // });

        Schema::create('tasks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained()->cascadeOnDelete();
            $table->foreignId('column_id')->constrained()->cascadeOnDelete();
        
            $table->string('title');
            $table->text('description')->nullable();
        
            $table->enum('priority', ['low', 'medium', 'high'])->default('medium');
            $table->dateTime('due_date')->nullable();
        
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
        
            $table->timestamps();
        
            $table->index(['project_id']);
            $table->index(['column_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tasks');
    }
};
