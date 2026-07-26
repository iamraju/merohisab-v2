<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('titles', function (Blueprint $table): void {
            $table->id();
            $table->string('name', 255);
            // Portable equivalent of unique lower(name)+type across MySQL and PostgreSQL.
            $table->string('name_normalized', 255);
            $table->string('type', 20);
            $table->foreignId('created_by_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->unique(['name_normalized', 'type']);
            $table->index('type');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('titles');
    }
};
