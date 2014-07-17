<?php
namespace Ttt\Panel;

use \Config;
use \Input;
use \Sentry;
use \View;
use Ttt\Panel\Repo\Grupo\GrupoInterface;
use Ttt\Panel\Service\Form\Modulo\ModuloForm;
use Ttt\Panel\Core\AbstractCrudController;

class GrupoController extends AbstractCrudController{

	protected $_views_dir = 'grupos';
	protected $_titulo = 'Grupos';

	protected $grupo;

	//protected $grupoForm;

	//public function __construct(GrupoInterface $grupo, GrupoForm $grupo)
	public function __construct(GrupoInterface $grupo)
	{
		parent::__construct();

		$this->grupo     = $grupo;
		//$this->grupoForm = $grupoForm;
	}

	public function index()
	{
		View::share('title', 'Listado de Grupos');

		$input = array_merge(Input::only(Config::get('panel::app.orderBy'), Config::get('panel::app.orderDir')));

		$order[Config::get('panel::app.orderBy')] = ! is_null($input[Config::get('panel::app.orderBy')]) ? $input[Config::get('panel::app.orderBy')] : 'name';
		$order[Config::get('panel::app.orderDir')] = ! is_null($input[Config::get('panel::app.orderDir')]) ? $input[Config::get('panel::app.orderDir')] : 'asc';

		//recogemos los grupos, aquí no paginaremos, ya que no va a ser algo corriente tener 200 grupos
		$items = $this->grupo->findAllBy($order);

		View::share('items', $items);
		return View::make('panel::grupos.index')
									->with('currentUrl', \URL::current())
									->with('params', $order);
	}


}
