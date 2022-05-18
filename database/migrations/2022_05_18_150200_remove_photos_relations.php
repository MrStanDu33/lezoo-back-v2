<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RemovePhotosRelations extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('artists', function (Blueprint $table) {
            $table->renameColumn('photo', 'avatar');
        });

        Schema::table('events', function (Blueprint $table) {
            $table->string('media');
        });

        Schema::table('residents', function (Blueprint $table) {
            $table->renameColumn('photo', 'avatar');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('artists', function (Blueprint $table) {
            $table->renameColumn('avatar', 'photo');
        });

        Schema::table('artists', function (Blueprint $table) {
            $table->dropColumn('media');
        });

        Schema::table('residents', function (Blueprint $table) {
            $table->renameColumn('avatar', 'photo');
        });
    }
}
