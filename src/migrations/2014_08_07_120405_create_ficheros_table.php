<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFicherosTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
    
	public function up()
	{
		Schema::create('ficheros', function(Blueprint $table)
                {
                    $table->increments('id');
                    
                    $table->string('nombre', 255);
                    $table->string('fichero', 255);
                    
                    $table->string('titulo_defecto', 255)->nullable();
                    $table->string('alt_defecto', 255)->nullable();
                    $table->text('descripcion_defecto')->nullable();
                    $table->string('enlace_defecto', 255)->nullable();
                    
                    $table->string('tipo',255);
                    $table->string('ruta', 255);
                    $table->string('mime', 255);
                    
                    $table->float('peso')->nullable();
                    $table->integer('ancho')->nullable();
                    $table->integer('alto')->nullable();
                    
                    $table->integer('creado_por')->unsigned();
                    $table->integer('actualizado_por')->unsigned();
                    
                    $table->foreign('creado_por')->references('id')->on('users');
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
		Schema::drop('ficheros');
	}

}
