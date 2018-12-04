<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateStackLinkTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('stack_link', function (Blueprint $table) {
            $table->increments('id');
            $table->string('site_key');
            $table->text('url');
            $table->string('md5_url')->nullable();
            $table->string('path_run')->nullable();
            $table->integer('state');
            $table->integer('parent')->nullable();
            $table->text('data');
            $table->integer('attempts')->default(0);
            $table->timestamps();

            $table->unique(['site_key', 'md5_url']);
            $table->index('md5_url');

            $table->index(['site_key']);
            $table->index(['state']);
            $table->index(['parent']);
            $table->index(['path_run']);



        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('stack_link');
    }
}
