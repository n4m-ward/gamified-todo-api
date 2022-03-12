<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTodoCheckboxTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::create('todo_checkbox', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('todo_id')->nullable(false)->unsigned();
            $table->string('description')->nullable(false);
            $table->boolean('is_done')->nullable(false)->default(false);
            $table->timestamps();

            $table->foreign('todo_id')->references('id')->on('todo');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists('todo_checkbox');
    }
}
