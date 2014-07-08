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
}
