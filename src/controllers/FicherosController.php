<?php

namespace Ttt\Panel;

use \Config;
use \Input;
use \Paginator;
use \view;

use Ttt\Panel\Repo\Fichero\FicheroInterface;
use Ttt\Panel\Service\Form\Fichero\FicheroForm;

use Ttt\Panel\Core\AbstractCrudController;

class FicherosController extends AbstractCrudController
{
    
    protected $_views_dir = 'ficheros';
    protected $_titulo = 'Ficheros';
    
    public static $moduleSlug = 'ficheros';
    
    protected $fichero;
    protected $ficheroForm;
    
    protected $allowed_url_params = array(
            'nombre','ordenPor','ordenDir','creado_por'
    );
    
    protected $acciones_por_lote = array(
        'delete' => 'Borrar'
    );
    
    
    public function __construct(FicheroInterface $fichero, FicheroForm $ficheroForm)
    {
        parent::__construct();
        
        $this->fichero = $fichero;
        $this->ficheroForm = $ficheroForm;
        
        if(! \Sentry::getUser()->hasAccess('ficheros::borrar'))
            {
                unset($this->acciones_por_lote['delete']);
            }        

    }
    
    public function index()
    {
      
		View::share('title', 'Listado de ' . $this->_titulo);

		//recogemos la página
		$pagina  = Input::get('pagina', 1);
		$perPage = Config::get('panel::app.perPage', 1);

		$params = $this->getParams();

		//recogemos la paginación
		$pageData = $this->fichero->byPage($pagina, $perPage, $params);
                
		$ficheros = Paginator::make(
                                        $pageData->items,
                                        $pageData->totalItems,
                                        $perPage
                                    );

		$ficheros->appends($params);

		View::share('items', $ficheros);

		return View::make('panel::ficheros.index')
                                        ->with('params', $params)
					->with('currentUrl', \URL::current())
					->with('accionesPorLote', $this->acciones_por_lote);

  
    }
    
    	protected function getParams()
	{
		$input = array_merge(Input::only($this->allowed_url_params));

		$input[Config::get('panel::app.orderBy')]  = !is_null($input[Config::get('panel::app.orderBy')]) ? $input[Config::get('panel::app.orderBy')] : 'nombre';
		$input[Config::get('panel::app.orderDir')] = !is_null($input[Config::get('panel::app.orderDir')]) ? $input[Config::get('panel::app.orderDir')] : 'asc';

		return $input;
	}
}