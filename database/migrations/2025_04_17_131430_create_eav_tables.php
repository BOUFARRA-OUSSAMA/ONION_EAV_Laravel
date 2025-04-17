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
        // Entity Types
        Schema::create('entity_types', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->string('name');
            $table->text('description')->nullable();
            $table->timestamps();
        });

        // Attributes
        Schema::create('attributes', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->string('name');
            $table->string('type');
            $table->text('description')->nullable();
            $table->boolean('is_required')->default(false);
            $table->foreignId('entity_type_id')->nullable()->constrained()->onDelete('cascade');
            $table->timestamps();
        });

        // Attribute Values
        Schema::create('attribute_values', function (Blueprint $table) {
            $table->id();
            $table->foreignId('attribute_id')->constrained()->onDelete('cascade');
            $table->string('entity_type');
            $table->unsignedBigInteger('entity_id');
            $table->text('value');
            $table->timestamps();

            $table->index(['entity_type', 'entity_id']);
            $table->unique(['attribute_id', 'entity_type', 'entity_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attribute_values');
        Schema::dropIfExists('attributes');
        Schema::dropIfExists('entity_types');
    }
};
