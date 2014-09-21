<?php

namespace Ttt\Panel\Repo\Paginas;

use Illuminate\Database\Eloquent\Model;



class PaginasFicheros extends \Eloquent  {
    
      protected $table = 'paginas_ficheros';
      
        //Atributos
      protected $fillable = array('idioma',
                                  'titulo',
                                  'alt',
                                  'descripcion',
                                  'enlace',
                                  'pagina_id',
                                  'fichero_id');
      
      
      public function pagina(){
          return $this->belongsTo('Ttt\Panel\Repo\Paginas\Pagina','pagina_id');
      }
      
      public function fichero(){
          return $this->belongsTo('Ttt\Panel\Repo\Fichero\Fichero','fichero_id');
      }



}