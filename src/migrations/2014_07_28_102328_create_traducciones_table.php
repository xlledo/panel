<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTraduccionesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		
            Schema::create('traducciones', function(Blueprint $table)
            {
                $table->increments('id');
                $table->string('clave', 255);
                
                $table->integer('creado_por')->unsigned();
                $table->foreign('creado_por')->references('id')->on('users');
                
                $table->integer('actualizado_por')->unsigned();
                $table->foreign('actualizado_por')->references('id')->on('users');
                
                $table->softDeletes(); // Mecanismo de borrado blando
                
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
		Schema::drop('traducciones');
                
	}

}
