<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('people', function (Blueprint $table) {
            $table->id();
            $table->string('name', 64);
            $table->boolean('is_man')->nullable();
            $table->timestamp('birthday')->nullable();
            $table->unsignedInteger('weight_gram');
            $table->unsignedBigInteger('category_id')->nullable();
            $table->unsignedBigInteger('skin_id')->nullable();
            $table->unsignedBigInteger('location_id')->nullable();
            $table->string('description', 64000)->nullable();
            $table->unsignedDecimal('rental_rate');
            $table->unsignedDecimal('cost');
            $table->timestamps();

            $table->index(['category_id']);
            $table->index(['weight_gram']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('people');
    }
};
