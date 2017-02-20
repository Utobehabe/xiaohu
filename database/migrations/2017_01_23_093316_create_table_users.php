<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableUsers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->increments('id');
            $table->string('username')->unique();   // 用户名不唯一
            $table->string('password');     // 密码，默认不为空
            $table->string('email')->unique()->nullable();  // 邮箱也不唯一，可以为空
            $table->string('avator_url')->nullable();   // 头像地址可以为空
            $table->string('phone')->unique()->nullable(); //电话不唯一，可以为空
            $table->text('intro')->nullable();  //个人介绍信息，可以为空
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }
}
