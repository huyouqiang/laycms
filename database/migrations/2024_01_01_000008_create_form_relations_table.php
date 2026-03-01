<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cms_form_relations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('form_id')->constrained('cms_forms')->cascadeOnDelete();
            $table->foreignId('form_field_id')->constrained('cms_form_fields')->cascadeOnDelete();
            $table->foreignId('related_form_id')->constrained('cms_forms')->cascadeOnDelete();
            $table->string('related_field_name', 64)->default('id');
            $table->timestamps();

            $table->unique('form_field_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cms_form_relations');
    }
};
