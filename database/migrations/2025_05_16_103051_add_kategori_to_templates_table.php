<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('template', function (Blueprint $table) {
            $table->string('kategori')->after('deskripsi')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::table('template', function (Blueprint $table) {
            $table->dropColumn('kategori');
        });
    }
};