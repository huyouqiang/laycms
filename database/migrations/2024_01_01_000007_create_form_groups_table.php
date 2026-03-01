<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cms_form_groups', function (Blueprint $table) {
            $table->id();
            $table->string('name', 50);
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->timestamps();
        });

        $defaultId = \DB::table('cms_form_groups')->insertGetId([
            'name' => '默认分组',
            'sort_order' => 0,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        Schema::table('cms_forms', function (Blueprint $table) use ($defaultId) {
            $table->unsignedBigInteger('form_group_id')->default($defaultId)->after('id');
        });
        Schema::table('cms_forms', function (Blueprint $table) {
            $table->foreign('form_group_id')->references('id')->on('cms_form_groups')->onDelete('restrict');
        });
    }

    public function down(): void
    {
        Schema::table('cms_forms', function (Blueprint $table) {
            $table->dropForeign(['form_group_id']);
        });
        Schema::dropIfExists('cms_form_groups');
    }
};
