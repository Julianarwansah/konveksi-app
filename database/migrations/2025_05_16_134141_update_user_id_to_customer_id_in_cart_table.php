<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateUserIdToCustomerIdInCartTable extends Migration
{
    public function up()
    {
        Schema::table('cart', function (Blueprint $table) {
            // Hapus foreign key dan kolom user_id
            $table->dropForeign(['user_id']);
            $table->dropColumn('user_id');

            // Tambahkan customer_id sebagai foreign key
            $table->foreignId('customer_id')->after('id')->constrained('customer')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::table('cart', function (Blueprint $table) {
            // Kembalikan user_id jika diperlukan
            $table->dropForeign(['customer_id']);
            $table->dropColumn('customer_id');

            $table->foreignId('user_id')->after('id')->constrained()->onDelete('cascade');
        });
    }
}
