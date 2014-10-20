<?php
namespace Ttt\Panel\Service\Form\Usuario;


class UserValidator extends \Illuminate\Validation\Validator {

	public function validateExiste($attribute, $value, $parameters = null)
	{
		$posibleUsuario = \App::make('Ttt\Panel\Repo\Usuario\UsuarioInterface')->newQuery()->where('email', '=', $value)->first();

		if(! $posibleUsuario)
		{
			return TRUE;//no existe, luego podemos continuar sin problemas
		}

		if(! count($parameters))
		{
			//estamos creando
			return FALSE;
		}

		//estamos editando
		if($posibleUsuario->id != $parameters[0])
		{
			return FALSE;
		}

		return TRUE;

	}
}
