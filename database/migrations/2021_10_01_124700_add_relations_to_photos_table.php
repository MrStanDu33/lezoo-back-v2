<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddRelationsToPhotosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('photos', function (Blueprint $table) {
            $table->unsignedBigInteger('artist_id')->nullable();
            $table->unsignedBigInteger('event_id')->nullable();
            $table->unsignedBigInteger('resident_id')->nullable();
            $table->dropForeign('album_id');
            $table->unsignedBigInteger('album_id')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('photos', function (Blueprint $table) {
            $table->dropColumn('artist_id');
            $table->dropColumn('event_id');
            $table->dropColumn('resident_id');
            $table->unsignedBigInteger('album_id')->change();
            $table->foreign('album_id')->references('id')->on('albums');
        });
    }
}
