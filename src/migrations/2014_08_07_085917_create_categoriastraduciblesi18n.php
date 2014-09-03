<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCategoriastraduciblesi18n extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('categorias_traducibles_i18n', function(Blueprint $table)
		{
			$table->increments('id');

			$table->integer('item_id')->unsigned();

			$table->string('idioma', 4);
			$table->text('nombre');

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
		Schema::drop('categorias_traducibles_i18n');
	}

}
