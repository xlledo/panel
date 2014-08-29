<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePaginasTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('paginas', function(Blueprint $table)
		{
                        $table->increments('id');
                        $table->softDeletes();
                        
                        $table->integer('creado_por')->unsigned();
                        $table->integer('actualizado_por')->unsigned();
                        
                        $table->foreign('creado_por')->references('id')->on('users');
                        $table->foreign('actualizado_por')->references('id')->on('users');
                        
			$table->timestamps();
		});
                
                Schema::create('paginas_i18n',function(Blueprint $table){

                        $table->increments('id');
                        
                        $table->integer('item_id')->unsigned();
                        $table->string('idioma',4);
                        
                        $table->string('titulo',255);
                        $table->text('texto');
                        
                        $table->timestamps();
                        
                });
                
                Schema::create('paginas_ficheros', function(Blueprint $table){

                        $table->increments('id');
                        
                        $table->integer('pagina_id');
                        $table->integer('fichero_id');
                        
                        $table->string('idioma',4);
                        
                        $table->string('titulo', 255);
                        $table->string('alt', 255);
                        $table->text('descripcion');
                        $table->string('enlace',255);
                        
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
		Schema::drop('paginas');
                Schema::drop('paginas_i18n');
                Schema::drop('paginas_ficheros');
	}

}
