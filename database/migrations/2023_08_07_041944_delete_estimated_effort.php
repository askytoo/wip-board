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
        Schema::table('tasks', function (Blueprint $table) {
            // 一度estimated_effortのカラムを削除してから、unsignedTinyIntegerで再作成する(別ファイル)
            $table->dropColumn('estimated_effort');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tasks', function (Blueprint $table) {
            //
            $table->smallInteger('estimated_effort');
        });
    }
};
