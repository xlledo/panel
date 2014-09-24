<?php namespace Ttt\Panel\Repo\Usuario;

class User extends \Cartalyst\Sentry\Users\Eloquent\User{

	//Atributos que se usarán para loguear la acción
	public $paramsForLog = array('full_name');

	public function getFullNameAttribute()
    {
        return $this->attributes['first_name'] . ' ' . $this->attributes['last_name'];
    }

	public function cleanGroups()
	{
		return $this->groups()->detach();
	}

	/**
	* El "booting" method de nuestros modelos. Se encargará de automatizar el logueo de actividad.
	*
	* Registraremos escuchadores de eventos en los métodos definidos a continuación
	*
	* Events:
	*
	*    1. "created": Before creating a new Node we'll assign a default value
	*    for the left and right indexes.
	*
	*    2. "updated": Before saving, we'll perform a check to see if we have to
	*    move to another parent.
	*
	*    3. "deleted": Move to the new parent after saving if needed and re-set
	*    depth.
	*
	*    4. (opcional) "restored": Before delete we should prune all children and update
	*    the left and right indexes for the remaining nodes.
	*
	* @return void
	*/
	protected static function boot() {
	parent::boot();

	//solo queremos que se registren estos eventos en el panel
	if( \Route::getCurrentRoute()->getPrefix() === 'admin' )
	{
		static::created(function($element) {
			$element->log('created');
		});

		static::updated(function($element) {
			$element->log('updated');
		});

		static::deleted(function($element) {
			$element->log('deleted');
		});

		/*if ( static::softDeletesEnabled() ) {
			static::restored(function($element) {
				$element->log('restored');
			});
		}*/
	}
	}

	public function log($type = NULL)
	{
		$sentryUser = \Sentry::getUser();
		$usuario = \App::make('Ttt\Panel\Repo\Usuario\UsuarioInterface')->findById($sentryUser->id);
		$class   = get_called_class();
		$keyName = $this->getKeyName();
		$keyVal  = $this->getKey();

		$texto   = $this->getDescriptionFromFields($type, $class, $usuario);

		$log = \App::make('Ttt\Panel\Repo\Log\LogInterface')
			->create(array(
				'class'   => $class,
				'keyName' => $keyName,
				'keyVal'  => $keyVal,
				'texto'   => $texto
			), $usuario);

		return $log;
	}

	private function getDescriptionFromFields($type = NULL, $class, $usuario)
	{
		$texto = '';

		switch($type)
		{
			case 'created':
				$texto = 'Creado ';
				break;
			case 'updated':
				$texto = 'Actualizado ';
				break;
			case 'deleted':
				$texto = 'Borrado ';
				break;
			case 'restored':
				$texto = 'Restaurado ';
				break;
			default:
				break;
		}

		foreach($this->paramsForLog as $pfl)
		{
			$texto .= $this->getAttribute($pfl) . ' ';
		}

		$texto .= 'en ' . $class . ' por ' . $usuario->full_name;

		return $texto;
	}

	/**
	* Devuelve si está habilitado el borrado blando
	*
	* @return boolean
	*/
	public function areSoftDeletesEnabled() {
		return static::hasGlobalScope(new \Illuminate\Database\Eloquent\SoftDeletingScope);
	}

	/**
	* Static method which returns wether soft delete functionality is enabled
	* on the model.
	*
	* @return boolean
	*/
	public static function softDeletesEnabled() {
		return with(new static)->areSoftDeletesEnabled();
	}
}
