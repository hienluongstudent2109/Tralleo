<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Schema::create('attachments', function (Blueprint $table) {
        //     $table->id();
        //     $table->morphs('attachable');
        //     $table->string('original_name');
        //     $table->string('path');
        //     $table->string('disk', 32)->default('local');
        //     $table->unsignedBigInteger('size')->nullable();
        //     $table->string('mime_type')->nullable();
        //     $table->foreignId('uploaded_by')->nullable()->constrained('users')->nullOnDelete();
        //     $table->timestamps();
        // });

        Schema::create('attachments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('task_id')->constrained()->cascadeOnDelete();
            $table->string('file_path');
            $table->foreignId('uploaded_by')->constrained('users')->cascadeOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('attachments');
    }
};
