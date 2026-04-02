<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Schema::create('projects', function (Blueprint $table) {
        //     $table->id();
        //     $table->foreignId('workspace_id')->constrained()->cascadeOnDelete();
        //     $table->string('name');
        //     $table->string('slug')->nullable();
        //     $table->text('description')->nullable();
        //     $table->string('color', 32)->nullable();
        //     $table->unsignedInteger('position')->default(0);
        //     $table->timestamps();
        //     $table->softDeletes();

        //     $table->unique(['workspace_id', 'slug']);
        // });

        Schema::create('projects', function (Blueprint $table) {
            $table->id();
            $table->foreignId('workspace_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->text('description')->nullable();
            $table->foreignId('created_by')->constrained('users')->nullOnDelete();
            $table->timestamps();
        
            $table->index('workspace_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('projects');
    }
};
