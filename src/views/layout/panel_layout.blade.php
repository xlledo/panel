<!doctype html>
<html xmlns="http://www.w3.org/1999/xhtml" dir="ltr" lang="es-ES">
    <head>
        @include('packages/ttt/panel/layout/head')
    </head>
    <body>
        @include('packages/ttt/panel/layout/cabecera')
        <div class="main-container" id="main-container">
            <div class="main-container-inner">
                <a class="menu-toggler" id="menu-toggler" href="#">
                    <span class="menu-text"></span>
                </a>
                @include('packages/ttt/panel/layout/menu')
                <div class="main-content">

                    <div class="breadcrumbs" id="breadcrumbs">
                            <script type="text/javascript">
                                try{ace.settings.check('breadcrumbs' , 'fixed')}catch(e){}
                            </script>
                            <ul class="breadcrumb">
                                @section('migas')
                                    {{ \Pila::render() }}
                                @show
                            </ul>
                    </div>

                    <div class="page-content">
                        @include('packages/ttt/panel/layout/flash_messages')
                        <div id="tools" class="acciones pull-right">
                            @yield('tools')
                        </div>
                        <div class="page-header">
                            @yield('page_header')
                        </div>
                        @yield('content')
                    </div>
                </div>
                @include('packages/ttt/panel/layout/settings')
            </div>
			<a href="#" id="btn-scroll-up" class="btn-scroll-up btn btn-sm btn-inverse">
				<i class="icon-double-angle-up icon-only bigger-110"></i>
			</a>
        </div>
        <script type="text/javascript">
            @section('inline_js')
                try{ace.settings.check('main-container' , 'fixed')}catch(e){}
                $(document).ready(function()
                {
                   $(".btn_confirmacion").click(function()
                   {
                        action_url     = $(this).data('action');
                        bootbox.confirm("¿Estas seguro de borrar este item?", function(result) {
                            if(result){
                                window.location.href = action_url;
                            }
                        });
                   });
                });
            @show
        </script>
    </body>
</html>
