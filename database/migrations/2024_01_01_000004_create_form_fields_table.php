<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cms_form_fields', function (Blueprint $table) {
            $table->id();
            $table->foreignId('form_id')->constrained('cms_forms')->cascadeOnDelete();
            $table->string('field_name', 64);
            $table->string('label', 100);
            $table->string('form_control', 50)->default('input');
            $table->text('options')->nullable();
            $table->text('attributes')->nullable();
            $table->string('validation_rules')->nullable();
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->boolean('is_required')->default(false);
            $table->boolean('is_list_visible')->default(true);
            $table->timestamps();

            $table->unique(['form_id', 'field_name']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cms_form_fields');
    }
};
