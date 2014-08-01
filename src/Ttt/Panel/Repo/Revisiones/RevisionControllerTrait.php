<?php

namespace Ttt\Panel\Repo\Revisiones;

trait RevisionControllerTrait {
    
    public function getVersion($id)
    {
        if($id)
            {
                $revision = \Ttt\Panel\Repo\Revisiones\Revision::find($id);
                
                return $revision->toArray();
            }
    }
    
}