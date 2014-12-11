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
        
                
        public function getStreamBase64()
        {
            
            $fichero_path_completo = public_path() . '/' . $this->ruta . $this->fichero;
            $fichero_stream = file_get_contents($fichero_path_completo);
            $fichero_base64 = base64_encode($fichero_stream);
            $str = "data:" . $this->mime . ";base64," .  $fichero_base64;
            
            return $str;
            
        }
}