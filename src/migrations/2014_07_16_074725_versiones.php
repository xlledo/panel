<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class Versiones extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('revisiones', function($table){

			$table->increments('id');
			$table->string('revisionable_type');    // Modelo que "tira" de la revision (Modelo paginas por ejemplo)
			$table->integer('revisionable_id');     // ID del elemento de la revision (paginaid=1 por ejemplo)
			
                        $table->integer('modificado_por')->unsigned();
                        $table->foreign('modificado_por')->references('id')->on('users');
                        
			$table->string('clave');
			$table->text('valor_viejo')->nullable();
			$table->text('valor_nuevo')->nullable();
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
		Schema::drop('revisiones');
	}

}
