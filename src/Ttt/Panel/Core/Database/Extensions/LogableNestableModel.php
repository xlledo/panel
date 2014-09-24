<?php
namespace Ttt\Panel\Core\Database\Extensions;

abstract class LogableNestableModel extends \Baum\Node{

    public $paramsForLog = array('nombre');

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

            static::moved(function($element) {
                $element->log('moved');
            });

            if ( static::softDeletesEnabled() ) {
                static::restored(function($element) {
                $element->log('restored');
                });
            }
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
            case 'moved':
                $texto = 'Movido ';
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
}
