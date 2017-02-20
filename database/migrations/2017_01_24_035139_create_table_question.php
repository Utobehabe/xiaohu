<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableQuestion extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('questions', function (Blueprint $table) {
            $table->increments('id');
            $table->string('title', 64);    // 指定长度是64
            $table->text('desc')->nullable()->comment('description'); //问题的描述，并添加注释为description
            $table->unsignedInteger('user_id');     // 用户的id
            $table->string('status')->default('ok');    // 状态
            $table->timestamps();   // 这个是设置创建时间和修改时间的函数

            $table->foreign('user_id')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('questions');
    }
}
