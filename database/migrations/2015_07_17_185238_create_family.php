<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFamily extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('families', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->string('name',255);
            $table->string('surname',255)->default('');
            $table->string('second_surname',255)->default('');
            $table->boolean('active')->default(0);
            $table->timestamps();
                       $table->string('image_profile',255)->nullable();
        });
        Schema::table('families',function(Blueprint $table){
              $table->integer('user_id')->unsigned()->index();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('families', function (Blueprint $table) {
            //
        });
    }
}
