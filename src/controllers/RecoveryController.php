<?php
namespace Ttt\Panel;

use \Input;
use \Mail;
use \Sentry;
use \Validator;
use \View;
use Ttt\Panel\Core\PanelController;

class RecoveryController extends PanelController{

	protected $_views_dir = 'recovery';
	protected $whitelist = array('index', 'comprobar', 'cambiar', 'validar');

	public function __construct()
	{
		$this->beforeFilter( 'logged');
		parent::__construct();
	}

	public function index()
	{
		View::share('title', 'Recuperación de contraseña');
		return View::make('panel::' . $this->_views_dir . '.index');
	}

	/**
	* Solicita la recuperación de contraseña de un usuario
	*/
	public function comprobar()
	{
		try
		{
			// Comprobamos que exista el usuario
			$user = Sentry::findUserByLogin(Input::get('email'));

			$resetCode = $user->getResetPasswordCode();

			//enviamos el código por email
			$this->_sendMail('Recuperación de contraseña', 'panel::emails.admin.recuperar_clave', array(
				'params' => array(
					'resetCode'  => $resetCode,
					'email'      => $user->email
				)
			), array(
					'address' => $user->email,
					'name'  => $user->first_name . ' ' . $user->last_name
			));

			\Session::flash('messages', array(
				array(
					'class' => 'alert-success',
					'msg'   => 'Le hemos enviado un correo electrónico para finalizar el proceso de recuperación de contraseña.'
				)
			));
			return \Redirect::action('Ttt\Panel\LoginController@index');
		}
		catch (\Cartalyst\Sentry\Users\UserNotFoundException $e)
		{
			$msg = 'USUARIO no existente.';
		}

		//error al intentar loguearse
		\Session::flash('messages', array(
			array(
				'class' => 'alert-danger',
				'msg'   => $msg
			)
		));
		return \Redirect::action('Ttt\Panel\RecoveryController@index');
	}

	/**
	* Muestra el formulario de resetear contraseña para el usuario
	*/
	public function cambiar($resetCode, $email)
	{

		$msg = '';
		try
		{
		    // Recuperamos el usuario por el email
		    $user = Sentry::findUserByLogin($email);

		    // Comprobamos que el código es válido
		    if ($user->checkResetPasswordCode($resetCode))
		    {
				//código válido, por lo tanto mostramos la vista
				View::share('title', 'Cambio de contraseña');
				return View::make('panel::' . $this->_views_dir . '.cambiar', array(
					'user'       => $user,
					'reset_code' => $resetCode
				));
		    }
		    else
		    {
		        // El código proporcionado no es válido
				$msg = 'El código proporcionado no es válido.';
		    }
		}
		catch (\Cartalyst\Sentry\Users\UserNotFoundException $e)
		{
			$msg = 'USUARIO no existente.';
		}

		//error al intentar loguearse
		\Session::flash('messages', array(
			array(
				'class' => 'alert-danger',
				'msg'   => $msg
			)
		));
		return \Redirect::action('Ttt\Panel\LoginController@index');
	}

	public function validar()
	{
		$msg = '';
		//validamos los datos que vienen por el formulario
		$rules = array(
			'password' => 'required|confirmed|min:6'
		);
		$messages = array(
			'required' => 'El campo Password es obligatorio',
			'confirmed'  => 'El campo Password debe coincidir con el campo Repetir Password',
			'min'      => 'El campo :attribute debe tener una longitud mínima de :min caracteres'
		);
		$validator = Validator::make(Input::all(), $rules, $messages);

		try
		{

			if(! $validator->passes())
			{
				throw new \Exception('Revise los datos introducidos.');
			}

			// Recuperamos el usuario por el email
			$user = Sentry::findUserByLogin(Input::get('email'));

			// Comprobamos que el código es válido
			if ($user->checkResetPasswordCode(Input::get('reset_code')))
			{

				// Intento de cambio de contraseña
		        if ($user->attemptResetPassword(Input::get('reset_code'), Input::get('password')))
		        {
		            // Restablecida la contraseña
					$msg = 'La contraseña ha sido reestablecida correctamente, puede acceder con los datos indicados.';
					\Session::flash('messages', array(
						array(
							'class' => 'alert-success',
							'msg'   => $msg
						)
					));
					return \Redirect::action('Ttt\Panel\LoginController@index');

		        }
		        else
		        {
		            // fallo en el restablecimiento de la contraseña
					$msg = 'No ha sido posible reestablecer la contraseña, por favor, vuelva a intentarlo.';

		        }
			}
			else
			{
				// El código proporcionado no es válido
				$msg = 'El código proporcionado no es válido.';
			}
		}
		catch (\Cartalyst\Sentry\Users\UserNotFoundException $e)
		{
			$msg = 'USUARIO no existente.';
			return \Redirect::action('Ttt\Panel\LoginController@index');
		}
		catch (\Exception $e)
		{
			$msg = $e->getMessage();
		}


		\Session::flash('messages', array(
			array(
				'class' => 'alert-danger',
				'msg'   => $msg
			)
		));
		return \Redirect::to('admin/cambiar-clave/' . Input::get('reset_code') . '/' . Input::get('email') . '/')
					->withErrors($validator);
	}
}
