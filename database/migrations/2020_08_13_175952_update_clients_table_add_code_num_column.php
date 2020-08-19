<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateClientsTableAddCodeNumColumn extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasColumn('clients', 'code')) {
            Schema::table('clients', function (Blueprint $table) {
                $table->integer('code')->virtualAs('code');
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (Schema::hasColumn('clients', 'code')) {
            Schema::table('clients', function (Blueprint $table) {
                $table->dropColumn('code');
            });
        }
    }
}
