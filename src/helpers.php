<?php
function url_title($str, $separator = 'dash', $lowercase = FALSE)
{
    $foreign_characters = array(
        '/ä|æ|ǽ/' => 'ae',
        '/ö|œ/' => 'oe',
        '/ü/' => 'ue',
        '/Ä/' => 'Ae',
        '/Ü/' => 'Ue',
        '/Ö/' => 'Oe',
        '/À|Á|Â|Ã|Ä|Å|Ǻ|Ā|Ă|Ą|Ǎ|А/' => 'A',
        '/à|á|â|ã|å|ǻ|ā|ă|ą|ǎ|ª|а/' => 'a',
        '/Б/' => 'B',
        '/б/' => 'b',
        '/Ç|Ć|Ĉ|Ċ|Č|Ц/' => 'C',
        '/ç|ć|ĉ|ċ|č|ц/' => 'c',
        '/Ð|Ď|Đ|Д/' => 'D',
        '/ð|ď|đ|д/' => 'd',
        '/È|É|Ê|Ë|Ē|Ĕ|Ė|Ę|Ě|Е|Ё|Э/' => 'E',
        '/è|é|ê|ë|ē|ĕ|ė|ę|ě|е|ё|э/' => 'e',
        '/Ф/' => 'F',
        '/ф/' => 'f',
        '/Ĝ|Ğ|Ġ|Ģ|Г/' => 'G',
        '/ĝ|ğ|ġ|ģ|г/' => 'g',
        '/Ĥ|Ħ|Х/' => 'H',
        '/ĥ|ħ|х/' => 'h',
        '/Ì|Í|Î|Ï|Ĩ|Ī|Ĭ|Ǐ|Į|İ|И/' => 'I',
        '/ì|í|î|ï|ĩ|ī|ĭ|ǐ|į|ı|и/' => 'i',
        '/Ĵ|Й/' => 'J',
        '/ĵ|й/' => 'j',
        '/Ķ|К/' => 'K',
        '/ķ|к/' => 'k',
        '/Ĺ|Ļ|Ľ|Ŀ|Ł|Л/' => 'L',
        '/ĺ|ļ|ľ|ŀ|ł|л/' => 'l',
        '/М/' => 'M',
        '/м/' => 'm',
        '/Ñ|Ń|Ņ|Ň|Н/' => 'N',
        '/ñ|ń|ņ|ň|ŉ|н/' => 'n',
        '/Ò|Ó|Ô|Õ|Ō|Ŏ|Ǒ|Ő|Ơ|Ø|Ǿ|О/' => 'O',
        '/ò|ó|ô|õ|ō|ŏ|ǒ|ő|ơ|ø|ǿ|º|о/' => 'o',
        '/П/' => 'P',
        '/п/' => 'p',
        '/Ŕ|Ŗ|Ř|Р/' => 'R',
        '/ŕ|ŗ|ř|р/' => 'r',
        '/Ś|Ŝ|Ş|Š|С/' => 'S',
        '/ś|ŝ|ş|š|ſ|с/' => 's',
        '/Ţ|Ť|Ŧ|Т/' => 'T',
        '/ţ|ť|ŧ|т/' => 't',
        '/Ù|Ú|Û|Ũ|Ū|Ŭ|Ů|Ű|Ų|Ư|Ǔ|Ǖ|Ǘ|Ǚ|Ǜ|У/' => 'U',
        '/ù|ú|û|ũ|ū|ŭ|ů|ű|ų|ư|ǔ|ǖ|ǘ|ǚ|ǜ|у/' => 'u',
        '/В/' => 'V',
        '/в/' => 'v',
        '/Ý|Ÿ|Ŷ|Ы/' => 'Y',
        '/ý|ÿ|ŷ|ы/' => 'y',
        '/Ŵ/' => 'W',
        '/ŵ/' => 'w',
        '/Ź|Ż|Ž|З/' => 'Z',
        '/ź|ż|ž|з/' => 'z',
        '/Æ|Ǽ/' => 'AE',
        '/ß/'=> 'ss',
        '/Ĳ/' => 'IJ',
        '/ĳ/' => 'ij',
        '/Œ/' => 'OE',
        '/ƒ/' => 'f',
        '/Ч/' => 'Ch',
        '/ч/' => 'ch',
        '/Ю/' => 'Ju',
        '/ю/' => 'ju',
        '/Я/' => 'Ja',
        '/я/' => 'ja',
        '/Ш/' => 'Sh',
        '/ш/' => 'sh',
        '/Щ/' => 'Shch',
        '/щ/' => 'shch',
        '/Ж/' => 'Zh',
        '/ж/' => 'zh',
    );

    $str = preg_replace(array_keys($foreign_characters), array_values($foreign_characters), $str);

    $replace = ($separator == 'dash') ? '-' : '_';

    $trans = array(
        '&\#\d+?;'                => '',
        '&\S+?;'                => '',
        '\s+'                    => $replace,
        '[^a-z0-9\-\._]' => '',
        $replace.'+'            => $replace,
        $replace.'$'            => $replace,
        '^'.$replace            => $replace,
        '\.+$'                    => ''
    );

    $str = strip_tags($str);

    foreach ($trans as $key => $val)
    {
        $str = preg_replace("#".$key."#i", $val, $str);
    }

    if ($lowercase === TRUE)
    {
        if( function_exists('mb_convert_case') )
        {
            $str = mb_convert_case($str, MB_CASE_LOWER, "UTF-8");
        }
        else
        {
            $str = strtolower($str);
        }
    }
	$permitted_uri_chars = 'a-z 0-9~%.:_\-áéíóúüñàèìòùãêôõçÇÁÉÍÓÚÀÈÌÒÙÑ+';
    $str = preg_replace('#[^'.$permitted_uri_chars.']#i', '', $str);

    return trim(stripslashes($str));
}

function ordenable_link($baseUrl, $key, $name, $params = array(), $currentOrderDir = 'asc')
{

	/*
	<a href="{{ $currentUrl }}?{{ Config::get('ttt.orderBy') }}=nombre&{{ Config::get('ttt.orderDir') }}=<?php if($params[Config::get('ttt.orderDir')] == 'desc'): ?>asc<?php else: ?>desc<?php endif; ?>">Nombre</a>
	*/

	$nextOrder = $currentOrderDir == 'asc' ? 'desc' : 'asc';

	$class = '';
	if($params['ordenPor'] == $key)
	{
		$class = $currentOrderDir;
	}

	$params['ordenPor'] = $key;
	$params['ordenDir'] = $nextOrder;

	$url = $baseUrl . '?' . http_build_query($params);

	return link_to($url, $name, $attributes = array(
		'class' => $class
	));
}

function toNestable($items, $slug)
{
    $out="";
    foreach ($items as $item) {

        $anchorContent = $item->nombre;
        if($item->isRoot())
        {
            if(Sentry::getUser()->hasAccess($slug . '::editarArbol'))
            {
                $anchorContent = link_to('admin/' . $slug . '/ver-raiz/' . $item->id, $item->nombre, array('title' => $item->nombre, 'id' => 'root_id', 'data-id' => $item->id));
            }
        }else
        {
            if(Sentry::getUser()->hasAccess($slug . '::editar'))
            {
                $anchorContent = link_to('admin/' . $slug . '/ver/' . $item->id, $item->nombre, array('title' => $item->nombre));
            }
        }

        $out .= '<li class="dd-item dd2-item" data-id="'.$item["id"].'">';
        $out .= '<div class="dd-handle dd2-handle">
                                                    <i class="normal-icon icon-comments blue bigger-130"></i>

                                                    <i class="drag-icon icon-move bigger-125"></i>
                                                </div>
                                                <div class="dd2-content">' . $anchorContent . '</div>';

        if ($item->children->count()) {

            $res = toNestable($item->children, $slug);
            if ($res) {
                $out .= '
    			<ol class="dd-list">' . $res . '</ol>';
            }
        }
        $out .= '</li>';
    }
    return $out;
}

function render_menu($menu, &$output = '')
{
    $url_solicitada = \Request::segment(2);
    $valores_vacios = array('', '#');
    foreach ($menu as $item) {

        $renderable = FALSE;
        if(in_array($item->ruta, $valores_vacios))
        {
            //si la ruta de la opción está vacía vemos si tenemos permiso sobre cada uno de sus hijos
            foreach($item->children as $chld)
            {
                $renderableChild = FALSE;
                if($chld->modulo()->count())
                {
                    $renderableChild = \Sentry::getUser()->hasAccess($chld->modulo->slug . '::listar');
                }else{
                    if(! in_array($chld->ruta, $valores_vacios))
                    {
                        $renderableChild = \Sentry::getUser()->hasAccess($chld->ruta . '::listar');
                    }
                }
                if($renderableChild)
                {
                    $renderable = TRUE;
                    break;
                }
            }
        }else{
            //no está vacío por lo que debe tener permiso sobre la ruta
            if($item->modulo()->count())
            {
                $renderable = \Sentry::getUser()->hasAccess($item->modulo->slug . '::listar');
            }else{
                if(! in_array($item->ruta, $valores_vacios))
                {
                    $renderable = \Sentry::getUser()->hasAccess($item->ruta . '::listar');
                }
            }
        }

        if(! $renderable && $item->ruta != 'dashboard')
        {
            continue;
        }

        if(strpos($url_solicitada, ! in_array($item->ruta, $valores_vacios) ? $item->ruta : 'vacio') === 0){
            $output .= '<li class="active">';
        }else{
            $output .= '<li>';
        }

        $tmp_ruta = ! in_array($item->ruta, $valores_vacios) ? url('admin/' . $item->ruta . '/') : '#';

        if ($item->parent->id == $item->getRoot()->id) {
            if($item->children->count()){
                $output .= '<a class="dropdown-toggle" href="' . $tmp_ruta . '" title="' . $item->nombre . '"><i class="'.$item->icono.'"></i>';
            }else{
                $output .= '<a href="' . $tmp_ruta . '" title="' . $item->nombre . '"><i class="'.$item->icono.'"></i>';
            }
        }else{
            if($item->children->count()){
                $output .= '<a class="dropdown-toggle" href="' . $tmp_ruta . '" title="' . $item->nombre . '"><i class="icon-double-angle-right"></i>';
            }else{
                $output .= '<a href="' . $tmp_ruta . '" title="' . $item->nombre . '"><i class="icon-double-angle-right"></i>';
            }
        }

        $output .= '<span class="menu-text">'.$item->nombre.'</span>';
        if($item->children->count()){
            $output .='<b class="arrow icon-angle-down"></b>';
        }
        $output .= '</a>';

        if($item->children->count()){
            $output .= '<ul class="submenu">';
            render_menu($item->children, $output);
            $output .= '</ul>';
        }

        $output .= '</li>';

    }
//        echo 'return<br />';
    return $output;
}
