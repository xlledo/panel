<?php

namespace Ttt\Panel\Repo\Revisiones;

class RevisionesHelper {
    
    
    public static function dropdownRevisiones($item, $campo, $modulo, $idioma, $tinyMCE = FALSE)
    {
        $html = "<div class='input-group-btn'>";
        $html.= "<button type='button' class='btn boton dropdown-toggle' data-toggle='dropdown'> ";
        $html.= "Versiones <span class='caret'></span>
                </button>";
        
        $html.= "<ul class='dropdown-menu' role='menu'>";
        $html.="<li><span  class='selector_versiones'  data-tinymce='true' data-version='-1' data-formelement='texto_" .$item->idioma . "' data-content='" . $item->{$campo} . "'>Version Actual</a></li>" ;

        foreach($item->versionesByClave($campo) as $version)
        {
                $html.="<li>
                        <span class='selector_versiones' style='cursor: pointer;'   data-module='paginas'
                                                                data-version='". $version->id ."' 
                                                                data-tinymce='" . (($tinyMCE) ? 'true' : 'false') ."'
                                                                data-formelement='" . $campo . '_' .$item->idioma ."'>[" . $version->id . "] Revision " . $version->created_at . "</a>
                    </li>";
        }
        
        $html.='</ul>';
        $html.='</div>';
        
        return $html;
        
        
    }
    
}