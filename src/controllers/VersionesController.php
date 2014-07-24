<?php

namespace Ttt\Panel;

use \Config;
use \Input;
use \Paginator;
use \View;

use Ttt\Panel\Core\AbstractCrudController;


class VersionesController extends AbstractCrudController
{
    
    function __construct() {
        parent::__construct();
    }

    
    public function getVersion($id = null)
    {
        if($id)
        {
            $revision = \Ttt\Panel\Repo\Revisiones\Revision::find($id);
            
            return $revision->toArray();
        }
    }
    
    
}