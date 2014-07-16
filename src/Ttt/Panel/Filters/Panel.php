<?php
namespace Ttt\Panel\Filters;


use \Sentry;

class Panel{

	public function logged($route, $request)
	{
		if(Sentry::check())
		{
			return \Redirect::guest(\Config::get('panel::app.access_url') . '/dashboard');
		}
	}


	public function notLogged()
	{
		if(! Sentry::check())
		{
			\Session::flash('messages', array(
				array(
					'class' => 'alert-danger',
					'msg'   => 'Debe estar registrado para poder acceder a la aplicación'
				)
			));
			return \Redirect::guest(\Config::get('panel::app.access_url'));
		}
	}

	public function hasPermission($route, $request)
	{
		//el slug del módulo
		$modulo = $request->segment(2);
		//si el módulo no es dashboard debe tener permiso para la acción dentro del módulo
		if($modulo !== 'dashboard')
		{
			$user = \Sentry::getUser();
			if(! $user->hasAccess($modulo))
			{
				\Session::flash('messages', array(
					array(
						'class' => 'alert-danger',
						'msg'   => 'No tiene permiso para acceder a este módulo ó acción'
					)
				));
				return \Redirect::guest(\Config::get('panel::app.access_url') . '/dashboard');
			}
		}
	}
}
