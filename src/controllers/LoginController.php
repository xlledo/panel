<?php
namespace Ttt\Panel;

use \Input;
use \Sentry;
use \View;
use Ttt\Panel\Core\PanelController;

class LoginController extends PanelController{

	protected $_views_dir = 'login';
	protected $whitelist = array('index', 'login', 'logout');

	public function __construct()
	{
		$this->beforeFilter( 'logged', array('except' => 'logout'));
		parent::__construct();
	}

	public function index()
	{
		/*echo '<pre>';
		var_dump(\Config::get('panel::mail.from'));
		echo '</pre>';exit;*/
		View::share('title', \Config::get('panel::app.dashboardTitle', 'Tres Tristes Tigres'));
		return View::make('panel::' . $this->_views_dir . '.login');
	}

	/**
	* Método que trata por POST el intento de logueo en el panel
	*/
	public function login()
	{
		try
		{
		    // Login credentials
		    $credentials = array(
		        'email'    => Input::get('email'),
		        'password' => Input::get('password'),
		    );

		    // Authenticate the user
		    $user = Sentry::authenticate($credentials, false);

			Sentry::loginAndRemember($user);

			\Session::flash('messages', array(
				array(
					'class' => 'alert-success',
					'msg'   => 'Bienvenido a la Aplicación de Gestión ' . \Config::get('panel::app.dashboardTitle', 'Tres Tristes Tigres')
				)
			));
			return \Redirect::action('Ttt\Panel\DashboardController@index');
		}
		catch (\Cartalyst\Sentry\Users\LoginRequiredException $e)
		{
		    $msg = 'El campo EMAIL es obligatorio.';
		}
		catch (\Cartalyst\Sentry\Users\PasswordRequiredException $e)
		{
		    $msg = 'El campo CONTRASEÑA es obligatorio.';
		}
		catch (\Cartalyst\Sentry\Users\WrongPasswordException $e)
		{
		    $msg = 'CONTRASEÑA incorrecta, vuelva a intentarlo.';
		}
		catch (\Cartalyst\Sentry\Users\UserNotFoundException $e)
		{
		    $msg = 'USUARIO no existente.';
		}
		catch (\Cartalyst\Sentry\Users\UserNotActivatedException $e)
		{
		    $msg = 'USUARIO no activo.';
		}

		//error al intentar loguearse
		\Session::flash('messages', array(
			array(
				'class' => 'alert-danger',
				'msg'   => $msg
			)
		));
		return \Redirect::action('Ttt\Panel\LoginController@index')->withInput(Input::only('email'));
	}

	/**
	* Método que realiza el logout
	*/
	public function logout()
	{
		Sentry::logout();

		\Session::flash('messages', array(
			array(
				'class' => 'alert-success',
				'msg'   => 'Ha cerrado la sesión correctamente'
			)
		));

		//redirigimos a la página de login
		return \Redirect::action('Ttt\Panel\LoginController@index');
	}
}
