<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tasks', function (Blueprint $table) {
            $table->id();

            $table->string('title', 255);
            $table->longText('description')->nullable();

            $table->string('category')->nullable();

            $table->string('status')->default('pending');
            $table->string('priority')->default('medium');

            $table->timestamp('create_date');
            $table->timestamp('due_date');

            $table->timestamps();

            $table->index(['status', 'due_date'], 'idx_status_due_date');
            $table->index(['status', 'priority'], 'idx_status_priority');
            $table->index(['category', 'status'], 'idx_category_status');
            $table->index('due_date', 'idx_due_date');
            $table->index('created_at', 'idx_created_at');
            $table->index('priority', 'idx_priority');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tasks');
    }
};
