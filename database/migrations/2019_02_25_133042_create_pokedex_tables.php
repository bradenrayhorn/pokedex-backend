<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePokedexTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pokemon', function (Blueprint $table) {
            $table->integer('id');
            $table->primary('id');
            $table->text('name');
            $table->decimal('height', 10, 2);
            $table->decimal('weight', 10, 2);
            $table->unsignedTinyInteger('hp');
            $table->unsignedTinyInteger('speed');
            $table->unsignedTinyInteger('attack');
            $table->unsignedTinyInteger('defense');
            $table->unsignedTinyInteger('special-attack');
            $table->unsignedTinyInteger('special-defense');
            $table->text('genus');
            $table->text('description');
        });

        Schema::create('pokemon_types', function (Blueprint $table) {
            $table->integer('id');
            $table->index('id');
            $table->text('type');
        });

        Schema::create('pokemon_abilities', function (Blueprint $table) {
            $table->integer('id');
            $table->index('id');
            $table->text('ability');
        });

        Schema::create('pokemon_egg_groups', function (Blueprint $table) {
            $table->integer('id');
            $table->index('id');
            $table->text('egg_group');
        });

        Schema::create('pokemon_captured', function (Blueprint $table) {
            $table->integer('id');
            $table->index('id');
            $table->integer('pokemon_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('pokemon');
        Schema::dropIfExists('pokemon_types');
        Schema::dropIfExists('pokemon_abilities');
        Schema::dropIfExists('pokemon_egg_groups');
        Schema::dropIfExists('pokemon_captured');
    }
}
