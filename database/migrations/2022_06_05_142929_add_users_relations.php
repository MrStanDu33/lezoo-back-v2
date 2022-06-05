<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddUsersRelations extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('artists', function (Blueprint $table) {
            $table->unsignedBigInteger('user_id');
        });
        DB::update('update artists set user_id = 1 where 1 = 1');
        Schema::table('artists', function (Blueprint $table) {
            $table
                ->foreign('user_id')
                ->references('id')
                ->on('users')
                ->onUpdate('cascade')
                ->onDelete('cascade');
        });

        Schema::table('albums', function (Blueprint $table) {
            $table->unsignedBigInteger('user_id');
        });
        DB::update('update albums set user_id = 1 where 1 = 1');
        Schema::table('albums', function (Blueprint $table) {
            $table
                ->foreign('user_id')
                ->references('id')
                ->on('users')
                ->onUpdate('cascade')
                ->onDelete('cascade');
        });

        Schema::table('events', function (Blueprint $table) {
            $table->unsignedBigInteger('user_id');
        });
        DB::update('update events set user_id = 1 where 1 = 1');
        Schema::table('events', function (Blueprint $table) {
            $table
                ->foreign('user_id')
                ->references('id')
                ->on('users')
                ->onUpdate('cascade')
                ->onDelete('cascade');
        });

        Schema::table('messages', function (Blueprint $table) {
            $table->unsignedBigInteger('user_id');
        });
        DB::update('update messages set user_id = 1 where 1 = 1');
        Schema::table('messages', function (Blueprint $table) {
            $table
                ->foreign('user_id')
                ->references('id')
                ->on('users')
                ->onUpdate('cascade')
                ->onDelete('cascade');
        });

        Schema::table('photos', function (Blueprint $table) {
            $table->unsignedBigInteger('user_id');
        });
        DB::update('update photos set user_id = 1 where 1 = 1');
        Schema::table('photos', function (Blueprint $table) {
            $table
                ->foreign('user_id')
                ->references('id')
                ->on('users')
                ->onUpdate('cascade')
                ->onDelete('cascade');
        });

        Schema::table('residents', function (Blueprint $table) {
            $table->unsignedBigInteger('user_id');
        });
        DB::update('update residents set user_id = 1 where 1 = 1');
        Schema::table('residents', function (Blueprint $table) {
            $table
                ->foreign('user_id')
                ->references('id')
                ->on('users')
                ->onUpdate('cascade')
                ->onDelete('cascade');
        });

        Schema::table('styles', function (Blueprint $table) {
            $table->unsignedBigInteger('user_id');
        });
        DB::update('update styles set user_id = 1 where 1 = 1');
        Schema::table('styles', function (Blueprint $table) {
            $table
                ->foreign('user_id')
                ->references('id')
                ->on('users')
                ->onUpdate('cascade')
                ->onDelete('cascade');
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
            $table->dropForeign('artists_user_id_foreign');
            $table->dropColumn('user_id');
        });
        Schema::table('albums', function (Blueprint $table) {
            $table->dropForeign('albums_user_id_foreign');
            $table->dropColumn('user_id');
        });
        Schema::table('events', function (Blueprint $table) {
            $table->dropForeign('events_user_id_foreign');
            $table->dropColumn('user_id');
        });
        Schema::table('messages', function (Blueprint $table) {
            $table->dropForeign('messages_user_id_foreign');
            $table->dropColumn('user_id');
        });
        Schema::table('photos', function (Blueprint $table) {
            $table->dropForeign('photos_user_id_foreign');
            $table->dropColumn('user_id');
        });
        Schema::table('residents', function (Blueprint $table) {
            $table->dropForeign('residents_user_id_foreign');
            $table->dropColumn('user_id');
        });
        Schema::table('styles', function (Blueprint $table) {
            $table->dropForeign('styles_user_id_foreign');
            $table->dropColumn('user_id');
        });
    }
}
