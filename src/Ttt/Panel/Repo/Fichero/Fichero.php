<?php
namespace Ttt\Panel\Repo\Fichero;

class Fichero extends \Eloquent{

        protected $table = 'ficheros';
    
	protected $fillable = array('nombre',
                                    'fichero', 
                                    'titulo_defecto',
                                    'alt_defecto',
                                    'descripcion_defecto',
                                    'enlace_defecto',
                                    'tipo',
                                    'ruta',
                                    'mime',
                                    'peso',
                                    'dimensiones',
                                    'creado_por',
                                    'actualizado_por',
                                    'categoria_id'
                                    );

	public $validator = null;
        
        protected static function boot() {
            parent::boot();
            
            static::deleting(function($element) {
                
                //Borramos el fichero fisicamente y los thumbnails si tiene    
                $ruta_fichero_original = public_path() . '/'. $element->ruta . $element->fichero;
                @unlink($ruta_fichero_original);

                //Si no es imagen no tendrá thumbnails
                if($element->esImagen())
                {
                    $ruta_resized = public_path() . '/' . $element->ruta . 'resized/';
                
                    foreach(glob($ruta_resized . '*' . $element->fichero) as $f)
                    {
                        @unlink( $f);
                    }
                }
                
                
                
          });
      
        }
        
        /**
                *  Borra las miniaturas de un item determinado
                */
        
        public function limpiarCacheMiniaturas()
        {
                //Si no es imagen no tendrá thumbnails
                if($this->esImagen())
                {
                    $ruta_resized = public_path() . '/' . $this->ruta . 'resized/';
                
                    foreach(glob($ruta_resized . '*' . $this->fichero) as $f)
                    {
                        @unlink( $f);
                    }
                }   
        }
        
        //Relaciones Many to Many
        public function paginas()
        {
           return $this->belongsToMany('Ttt\Panel\Repo\Paginas\Pagina', 'paginas_ficheros', 'fichero_id', 'pagina_id');
        }
        
	public function maker()
	{
		return $this->belongsTo('Cartalyst\Sentry\Users\Eloquent\User', 'creado_por');
	}

	public function updater()
	{
		return $this->belongsTo('Cartalyst\Sentry\Users\Eloquent\User', 'actualizado_por');
	}                
        
        public function categoria()
        {
                return $this->belongsTo('Ttt\Panel\Repo\Categoria\Categoria', 'categoria_id');
        }
        
        public function esImagen()
        {
                if(strpos($this->mime, 'image') === FALSE ){
                    return FALSE;
                }else{
                    return TRUE;
                }
        }
        
                
        public function getStreamBase64($path = null)
        {
            
            if(!$path)
            {
                $fichero_path_completo = public_path() . '/' . $this->ruta . $this->fichero;
            }else{
                $fichero_path_completo = public_path() . '/' . $path;
            }
            
            if(is_file($fichero_path_completo))
            {
            
                $fichero_stream = file_get_contents($fichero_path_completo);
                $fichero_base64 = base64_encode($fichero_stream);
                $str = "data:" . $this->mime . ";base64," .  $fichero_base64;
            
            
                return $str;
            }else{
                return FALSE;
            }
        }

        
        
        public function getSize($width = null, $height = null)
        {
            if(!$width)
            {
                throw  new \Ttt\Panel\Exception\TttException('Necesita proporcionar las medidas de la imagen');
            }
            
            $height = $height?: $width;
            
            if($this->esImagen())
            {
                //primero chequeamos si el fichero ya existe
                $ruta_fisica_esperada = public_path() . '/' . $this->ruta . 'resized/' . $width . '_' . $height . $this->fichero;
                $str_imagen = $this->ruta . 'resized/'. $width . '_' . $height . $this->fichero;
                
                if( !file_exists($ruta_fisica_esperada) or !is_file($ruta_fisica_esperada) )
                { //Si no existe creamos el thumb con las medidas
                
                    //creamos el path
                    @mkdir(public_path() . '/' . $this->ruta . 'resized/');
                    
                    $img = \Imagecow\Image::create( public_path() . '/' . $this->ruta  . $this->fichero );
                    $img->resize($width, $height);
                    $img->save( public_path() . '/' . $this->ruta . 'resized/' . $width . '_' . $height .$this->fichero );
                }
                return $str_imagen;
                
            }else{
                throw new \Ttt\Panel\Exception\TttException('No se puede hacer resize de un fichero que no es una imagen');
            }
        }
        
        
}