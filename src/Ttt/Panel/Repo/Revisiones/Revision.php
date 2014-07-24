<?php

namespace Ttt\Panel\Repo\Revisiones;

class Revision extends \Eloquent
{
    
    public $table = 'revisiones';
    
    public function versionable()
    {
        return $this->morphTo();
    }
    
}
