<?php

namespace Ttt\Panel\Repo\Paginas;



//use Illuminate\Database\Eloquent\Model;
use Ttt\Panel\Repo\Fichero\Extensions\FicheroPivotInterface;



class EloquentPaginasFicheros implements FicheroPivotInterface{
    
    protected $ficheroPivot;

    
    public function __construct(\Illuminate\Database\Eloquent\Model $Fichero) {
        $this->ficheroPivot = $Fichero;
    }

    public function byId($id) {
        return $this->ficheroPivot->findOrFail($id);
    }

}