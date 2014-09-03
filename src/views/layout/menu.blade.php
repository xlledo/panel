<div class="sidebar" id="sidebar">
    <script type="text/javascript">
        try{ace.settings.check('sidebar' , 'fixed')}catch(e){}
    </script>

    <div class="sidebar-shortcuts" id="sidebar-shortcuts">
        <div class="sidebar-shortcuts-large" id="sidebar-shortcuts-large">
            <button class="btn btn-success">
                <i class="icon-bar-chart"></i>
            </button>

            <button class="btn btn-info">
                <i class="icon-gift"></i>
            </button>

            <button class="btn btn-warning">
                <i class="icon-flag"></i>
            </button>

            <button class="btn btn-danger">
                <i class="icon-cogs"></i>
            </button>
        </div>

        <div class="sidebar-shortcuts-mini" id="sidebar-shortcuts-mini">
            <span class="btn btn-success"></span>

            <span class="btn btn-info"></span>

            <span class="btn btn-warning"></span>

            <span class="btn btn-danger"></span>
        </div>
    </div><!-- #sidebar-shortcuts -->
    <ul class="nav nav-list">
        <!-- De momento lo construimos a mano -->
        <li class="active">
            <a href="{{ action('Ttt\Panel\DashboardController@index') }}" title="Dashboard">
                <i class="icon-bar-chart"></i>
                <span class="menu-text">Dashboard</span>
            </a>
        </li>
        @if(Sentry::getUser()->hasAccess('modulos::listar'))
            <li>
                <a href="{{ action('Ttt\Panel\ModuloController@index') }}" title="Módulos">
                    <i class="icon-cogs"></i>
                    <span class="menu-text">Módulos</span>
                </a>
            </li>
        @endif
        @if(Sentry::getUser()->hasAccess('variables-globales::listar'))
            <li>
                <a href="{{ action('Ttt\Panel\VariablesglobalesController@index') }}" title="Variables Globales">
                    <i class="icon-cogs"></i>
                    <span class="menu-text">Variables Globales</span>
                </a>
            </li>
        @endif
        @if(Sentry::getUser()->hasAccess('grupos::listar'))
            <li>
                <a href="{{ action('Ttt\Panel\GrupoController@index') }}" title="Grupos">
                    <i class="icon-group"></i>
                    <span class="menu-text">Grupos</span>
                </a>
            </li>
        @endif
        @if(Sentry::getUser()->hasAccess('usuarios::listar'))
            <li>
                <a href="{{ action('Ttt\Panel\UsuarioController@index') }}" title="Usuarios">
                    <i class="icon-user"></i>
                    <span class="menu-text">Usuarios</span>
                </a>
            </li>
        @endif

        @if(Sentry::getUser()->hasAccess('traducciones::listar'))
        <li>
            <a href="{{action('Ttt\Panel\TraduccionesController@index') }}" title="Traducciones">
                <i class="icon-globe"></i>
                <span class="menu-text">Traducciones</span>
            </a>
        </li>
        @endif
        @if(Sentry::getUser()->hasAccess('categorias::listar'))
            <li>
                <a href="{{ action('Ttt\Panel\CategoriaController@index') }}" title="Categorías">
                    <i class="icon-flag"></i>
                    <span class="menu-text">Categorías</span>
                </a>
            </li>
        @endif
        @if(Sentry::getUser()->hasAccess('categorias-traducibles::listar'))
            <li>
                <a href="{{ action('Ttt\Panel\CategoriaTraducibleController@index') }}" title="Categorías traducibles">
                    <i class="icon-flag"></i>
                    <span class="menu-text">Cat Traducibles</span>
                </a>
            </li>
        @endif
        @if(Sentry::getUser()->hasAccess('idiomas::listar'))
            <li>
                <a href="{{ action('Ttt\Panel\IdiomaController@index') }}" title="Idiomas">
                    <i class="icon-flag"></i>
                    <span class="menu-text">Idiomas</span>
                </a>
            </li>
        @endif
        @if(Sentry::getUser()->hasAccess('ficheros:listar'))
            <li>
                <a href="{{ action('Ttt\Panel\FicherosController@index') }}" title="Ficheros">
                    <i class="icon-file"></i>
                    <span class="menu-text">Ficheros</span>
                </a>
            </li>        
        @endif
        @if(Sentry::getUser()->hasAccess('paginas::listar'))
            <li>
                <a href="{{ action('Ttt\Panel\PaginasController@index') }}" title="Paginas">
                    <i class="icon-file"></i>
                    <span class="menu-text">Páginas</span>
                </a>
            </li>                
        @endif
    </ul>

    <div class="sidebar-collapse" id="sidebar-collapse">
        <i class="icon-double-angle-left" data-icon1="icon-double-angle-left" data-icon2="icon-double-angle-right"></i>
    </div>
    <script type="text/javascript">
        try{ace.settings.check('sidebar' , 'collapsed')}catch(e){}
    </script>
</div>
