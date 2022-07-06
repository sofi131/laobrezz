<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddIsadminToUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            // engadidos a táboa users
            $table->boolean('is_admin')->after('email_verified_at')->nullable();
            $table->boolean('photo')->after('is_admin')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            // eliminar a columna.
            // para que funcione dropColumn debemos dispor de ``composer require doctrine/dbal``
            $table->dropColumn('is_admin');
            $table->dropColumn('photo');
        });
    }
}
