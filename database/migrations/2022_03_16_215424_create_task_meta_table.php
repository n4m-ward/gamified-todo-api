<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTaskMetaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::create('task_meta', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('task_id')->unsigned()->nullable(false);
            $table->bigInteger('hit_target')->nullable(false)->default(0);
            $table->bigInteger('final_meta')->nullable(false);
            $table->string('title')->nullable(false);
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('task_id')->references('id')->on('task');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists('task_meta');
    }
}
