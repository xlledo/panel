<?php
namespace Ttt\Panel\Repo\Categoriatraducible;

use Illuminate\Validation\Factory as Validator;

class CategoriaI18n extends \Eloquent{

    protected $table = 'categorias_traducibles_i18n';

    //Atributos
	protected $fillable = array('item_id', 'idioma', 'texto');

    public function traduccion()
    {
        return $this->belongsTo('Ttt\Panel\Repo\Categoriatraducible\Categoria', 'id');
    }
}
