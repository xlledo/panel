<?php
namespace Ttt\Panel\Repo\Idioma;

class Idioma extends \Ttt\Panel\Core\Database\Extensions\Model{

	protected $fillable = array('principal', 'visible', 'nombre', 'codigo_iso_2', 'codigo_iso_3');

	public $validator = null;


        /**
         * Devuelve un idioma desde su codigo Iso 2
         *
         * @param String $codigo_iso_2
         * @return mixed Resultado
         */


        public static function getByCodigoIso2($codigo_iso_2)
        {
            $idioma = self::where('codigo_iso_2', $codigo_iso_2)->get();

            if($idioma){
                return $idioma->first();
            }else{
                return FALSE;
            }

        }
}
