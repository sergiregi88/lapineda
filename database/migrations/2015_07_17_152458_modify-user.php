<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ModifyUser extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            if(config('auth.confirmation.use_confirmation'))
            {
                $table->boolean('activated');
                $table->dateTime('activation_date')->nullable();
                $table->string('activation_code')->nullable()->default('');
            }
            $table->boolean('active')->default(0);
            $table->boolean('banned')->default(0);

            $table->softDeletes();

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
            //
        });
    }
}
