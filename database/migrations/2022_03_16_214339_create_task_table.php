<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTaskTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::create('task', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('user_id')->nullable(false)->unsigned();
            $table->string('title')->nullable(true);
            $table->string('description', 3000)->nullable(true);
            $table->boolean('expired')->nullable(false)->default(false);
            $table->dateTime('date_of_the_task')->nullable(false);
            $table->dateTime('conclusion_date')->nullable(true);
            $table->boolean('is_done')->nullable(false)->default(false);
            $table->integer('qty_repeats')->nullable(false)->default(0);
            $table->boolean('repeat_forever')->nullable(false)->default(false);
            $table->bigInteger('task_type_id')->nullable(false)->unsigned();
            $table->bigInteger('original_task_id')->nullable(true)->unsigned();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('original_task_id')->references('id')->on('task');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists('task');
    }
}
