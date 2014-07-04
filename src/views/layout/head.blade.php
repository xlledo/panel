<title>{{ $title }}</title>
<meta name="author" content="Ximo Lledó" />
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<meta name='robots' content='noindex,nofollow' />

@section('js_assets')
    <script type="text/javascript" src="{{ asset('packages/ttt/panel/components/bootstrap/js/jquery-1.10.2.min.js') }}"></script>
    @foreach($assets['js'] as $js_ass)
        <script type="text/javascript" src="{{ $js_ass }}"></script>
    @endforeach
@show
@section('css_assets')
    @foreach($assets['css'] as $css_ass)
        <link href="{{ $css_ass }}" type="text/css" rel="stylesheet" />
    @endforeach
    <link href="{{ asset('packages/ttt/panel/components/bootstrap/css/bootstrap.min.css') }}" type="text/css" rel="stylesheet" />
    <link href="{{ asset('packages/ttt/panel/components/bootstrap/css/font-awesome.min.css') }}" type="text/css" rel="stylesheet" />
    <link href="{{ asset('packages/ttt/panel/components/bootstrap/css/ace-fonts.css') }}" type="text/css" rel="stylesheet" />
    <link href="{{ asset('packages/ttt/panel/components/bootstrap/css/ace.min.css') }}" type="text/css" rel="stylesheet" />
    <link href="{{ asset('packages/ttt/panel/css/estilos.css') }}" type="text/css" rel="stylesheet" />
@show

@yield('custom_js_files')

<script type="text/javascript">
    @section('js_init')
        var SITE_URL  = "{{ url('') }}/",
            BASE_URL  = "{{ url('admin') }}/";
    @show
</script>
