<?php


use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

use Illuminate\Support\Facades\DB;

class CreatePerfectlyCacheTestPostsTable extends Migration{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('posts', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedInteger('user_id');
            $table->string('name');

            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onUpdate('CASCADE')->onDelete('CASCADE');
        });

        $now = now();

        $user = DB::table('users')->first();


        for ($i = 0; $i < 20; $i++) {
            DB::table('posts')->insert([
                'user_id' => $user->id,
                'name' => str_random(20),
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }


    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('perfectly_cache_test_posts');
    }
}
