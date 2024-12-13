<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('progress', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('goal_id');
            $table->foreign('goal_id')->references('id')->on('goals')->onDelete('cascade');
            $table->date('progress_date');
            $table->text('progress_description')->nullable();
            $table->integer('progress_value')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('progress');
    }
};
