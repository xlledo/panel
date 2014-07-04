<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateModulos extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		//

		Schema::create('modulos', function(Blueprint $table)
		{
			$table->increments('id');
			$table->string('nombre', 255);
			$table->boolean('visible');

			$table->string('slug', 255);
			$table->integer('creado_por')->unsigned();
			$table->foreign('creado_por')->references('id')->on('users');
			$table->integer('actualizado_por')->unsigned();
			$table->foreign('actualizado_por')->references('id')->on('users');

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
		Schema::drop('modulos');
	}

}
