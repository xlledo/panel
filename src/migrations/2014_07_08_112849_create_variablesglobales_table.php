<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateVariablesglobalesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('variablesglobales', function(Blueprint $table)
		{
			$table->increments('id');
			$table->string('clave',255);
			$table->string('valor',255);

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
		
		Schema::drop('variablesglobales');
	}

}
