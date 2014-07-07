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