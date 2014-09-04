<?php
namespace Ttt\Panel\Core;

use \Mail;
use \Sentry;

class PanelController extends \BaseController {
	protected $_views_dir = null;//indica el directorio de vistas

	public static $moduleSlug = null;

	public $menuNavegacion;

	/**
	* Lista de métodos que no ejecutan el filtro que comprueba si se está logueado para acceder, por defecto lo ejecutan todos
	* @var array
	*/
	protected $whitelist = array();


	public function __construct()
	{
		/*
		echo '<pre>';
		print_r (\Route::current()->getActionName());//
		echo '</pre>';
		echo \Route::currentRouteAction();exit;//el método invocado Ttt\Panel\LoginController@index
		*/

		//la única manera de poder establecer el parámetro página en la url
		\App::make('paginator')->setPageName(\Config::get('ttt.pageName', 'pg'));


		$this->beforeFilter( 'notLogged' , array('except' => $this->whitelist));

		$this->beforeFilter( 'hasPermission');

		$this->_setDefaultAssets();

		$this->_setMenu();

		//cualquier controlador del panel ejecuta filtro para ver si está logueado


		//posibles acciones a ejecutar con el controlador genérico de la aplicación
		//$this->_checkLogged();
		/*
		try
		{
			$adminGroup = Sentry::findGroupByName('superadmin');
			echo '<pre>';
			print_r($adminGroup->toJson());
			echo '</pre>';
			echo '<pre>';
			print_r($adminGroup->toArray());
			echo '</pre>';
			echo '<pre>';
			print_r($adminGroup->permissions);
			echo '</pre>';
		}
		catch (Cartalyst\Sentry\Groups\GroupNotFoundException $e)
		{
			echo 'Group not found';
		}
		exit;
		*/
	}

	protected function _setMenu()
	{
		$root = \App::make('Ttt\Panel\Repo\Menu\MenuInterface')->byId(1);//recuperamos el menú
		$this->menuNavegacion = $root->getDescendants()->toHierarchy();

		\View::share('menu', $this->menuNavegacion);
	}

	protected function _sendMail($subject, $file, $data, $to, $bcc = null, $cc = null, $attachment = FALSE)
	{

		$notifier = \App::make('ttt.notifier');

		$notifier->notifierTemplate($file)
					->to($to);

		if(! is_null($bcc))
		{
			$notifier->bcc($bcc);
		}

		if(! is_null($cc))
		{
			$notifier->cc($cc);
		}

		$notifier->notify($subject, $data, $attachment);


	}

	protected function _setDefaultAssets()
	{
		$components_assets = array();
		$css_assets = array();

		$components_assets[] = asset('packages/ttt/panel/components/bootstrap/js/bootstrap.min.js');
		$components_assets[] = asset('packages/ttt/panel/components/bootstrap/js/ace-extra.min.js');
		$components_assets[] = asset('packages/ttt/panel/components/bootstrap/js/typeahead-bs2.min.js');
		$components_assets[] = asset('packages/ttt/panel/components/bootstrap/js/jquery-ui-1.10.3.full.min.js');
		$components_assets[] = asset('packages/ttt/panel/components/bootstrap/js/ace-elements.min.js');
		$components_assets[] = asset('packages/ttt/panel/components/bootstrap/js/ace.min.js');
		$components_assets[] = asset('packages/ttt/panel/components/bootstrap/js/bootbox.min.js');
		$components_assets[] = asset('packages/ttt/panel/components/autonumeric.js');
		$components_assets[] = asset('packages/ttt/panel/components/jquery/timepicker/jquery-ui-timepicker-addon.js');
		$components_assets[] = asset('packages/ttt/panel/components/jquery/timepicker/jquery-ui-timepicker-es.js');
		$components_assets[] = asset('packages/ttt/panel/js/base.js');
		$components_assets[] = asset('packages/ttt/panel/js/autonumericExtended.js');
                $components_assets[] = asset('packages/ttt/panel/components/tiny_mce/tinymce.min.js');
                $components_assets[] = asset('packages/ttt/panel/js/initTiny.js');

		$css_assets[]        = asset('packages/ttt/panel/components/bootstrap/css/jquery-ui-1.10.3.full.min.css');
		$css_assets[]        = asset('packages/ttt/panel/components/jquery/timepicker/jquery-ui-timepicker-addon.css');
		//$css_assets[]        = asset('packages/ttt/panel/components/bootstrap/css/ace.min.css');

		\View::share('assets', array(
			'js'  => $components_assets,
			'css' => $css_assets
		));
	}
}
