<?php
namespace Ttt\Panel\Core\Database\Extensions;

abstract class TranslatableNestableModel extends \Baum\Node{

    public $paramsForLog = array('nombre');

    public static $table_i18n     = 'categorias_traducibles_i18n';

    protected $modelI18n    = 'CategoriatraducibleI18n';

    protected $idiomaCol    = 'idioma';

    //Atributos Traducibles
    public $atributosTraducibles = array('nombre');

    /**
    * Recoge un atributo del modelo propio o de la traducción
    * @return mixed
    */
    public function getAttribute($key, $idioma = null)
    {
        if ($this->isTranslatableAttribute($key))
        {
            $traduccion = $this->traduccion($idioma);

            if(! $traduccion)
            {
                return 'No existe la traducción [' . $key . '] en el idioma';
            }

            return $traduccion->$key;
        }
        return parent::getAttribute($key);
    }

    public function hasTranslation($idioma)
    {

        foreach ($this->traducciones as $translation)
        {
            if ($translation->getAttribute('idioma') == $idioma)
            {
                return true;
            }
        }

        return false;
    }

    /**
    * Establece un atributo o bien en el campo común o bien en la traducción del idioma correspondiente
    *
    * @return void
    */
    public function setAttributeForIdioma($key, $value, $idioma = null)
    {
        if ($this->isTranslatableAttribute($key))
        {
            $this->traduccion($idioma)->$key = $value;
        }
        else
        {
            parent::setAttribute($key, $value);
        }
    }

    /**
    * Recoge una traducción concreta del elemento
    * @param string $idioma
    * @return \Ttt\Panel\Repo\Categoriatraducible\Categoria|FALSE
    */
    public function traduccion($idioma = null)
    {
        $idioma = is_null($idioma) ? \App::make('Ttt\Panel\Repo\Idioma\IdiomaInterface')->idiomaPrincipal()->codigo_iso_2 : $idioma;
        foreach($this->traducciones as $traduccion)
        {
            if($traduccion->idioma == $idioma)
            {
                return $traduccion;
            }
        }

        //debemos devolver una nueva traducción en el idioma por defecto porque no existe
        $nuevaTraduccion = $this->getNewTranslationInstance($idioma);
        $this->traducciones->add($nuevaTraduccion);

        return $nuevaTraduccion;
    }

    /**
    * Comprueba si un atributo es traducible
    * @param string $key
    * @return bool
    */
    public function isTranslatableAttribute($key)
    {
        return in_array($key, $this->atributosTraducibles);
    }

    /**
    * Nos devuelve una nueva instancia preparada para trabajar con ella
    * @param string $idioma
    * @return static
    */
    public function emptyInstance($idioma = null)
    {
        $instance = new static;

        $traduccion = $this->getNewTranslationInstance($idioma);

        $instance->traducciones->add($traduccion);

        return $instance;
    }

    /**
    * Rellena los diferentes valores en el objeto y sus traducciones
    * @param $attributes array
    * @param $idioma string
    */
    public function fill(array $attributes)
    {
        $totallyGuarded = $this->totallyGuarded();

        foreach ($attributes as $key => $values)
        {
            //si la clave es un idioma se debe recoger la traducción
            if (preg_match('/^[a-z]{2}$/', $key))
            {
                $translation = $this->traduccion($key);

                foreach ($values as $translationAttribute => $translationValue)
                {
                    if ($this->isFillable($translationAttribute))
                    {
                        $translation->$translationAttribute = $translationValue;
                    }
                    elseif ($totallyGuarded)
                    {
                        throw new MassAssignmentException($key);
                    }
                }
                unset($attributes[$key]);
            }
        }

        return parent::fill($attributes);
    }

    public function save(array $options = array())
    {
        $translations = $this->traducciones;
        if ($this->exists)
        {
            if (count($this->getDirty()) > 0)
            {
                // If $this->exists and dirty, parent::save() has to return true. If not,
                // an error has occurred. Therefore we shouldn't save the translations.
                if (parent::save($options))
                {
                    $this->addTranslations($translations);
                    return $this->saveTranslations();
                }
                return false;
            }
            else
            {
                // If $this->exists and not dirty, parent::save() skips saving and returns
                // false. So we have to save the translations
                $this->addTranslations($translations);
                return $this->saveTranslations();
            }
        }
        elseif (parent::save($options))
        {
            // We save the translations only if the instance is saved in the database.
            $this->addTranslations($translations);
            return $this->saveTranslations();
        }
        return false;
    }

    public function delete()
    {
        // delete all related photos
        $this->traducciones()->delete();
        // as suggested by Dirk in comment,
        // it's an uglier alternative, but faster
        // Photo::where("user_id", $this->id)->delete()

        // delete the user
        return parent::delete();
    }

    protected function addTranslations($traducciones)
    {
        foreach($traducciones as $tra)
        {
            $this->traducciones->add($tra);
        }
    }

    protected function saveTranslations()
    {
        $saved = true;
        foreach ($this->traducciones as $translation)
        {
            if ($saved && $this->isTranslationDirty($translation))
            {
                $translation->setAttribute('item_id', $this->getKey());
                $saved = $translation->save();
            }
        }
        return $saved;
    }

    protected function isTranslationDirty($translation)
    {
        $dirtyAttributes = $translation->getDirty();
        unset($dirtyAttributes[$this->idiomaCol]);
        return count($dirtyAttributes) > 0;
    }

    /*Crea una nueva traducción solo con el idioma en un elemento
    * @param string $idioma
    */
    protected function getNewTranslationInstance($idioma = null)
    {
        $idioma = is_null($idioma) ? \App::make('Ttt\Panel\Repo\Idioma\IdiomaInterface')->idiomaPrincipal()->codigo_iso_2 : $idioma;
        $translation = new $this->modelI18n;
        $translation->setAttribute($this->idiomaCol, $idioma);
        return $translation;
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
