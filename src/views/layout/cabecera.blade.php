<div class="navbar navbar-default" id="navbar">
    <script type="text/javascript">
        try{ace.settings.check('navbar' , 'fixed')}catch(e){}
    </script>

    <div class="navbar-container" id="navbar-container">
        <div class="navbar-header pull-left">
            <a href="#" class="navbar-brand">
                <small>
                    <i class="icon-globe"></i>
                    CRM FacePhi
                </small>
            </a><!-- /.brand -->
        </div><!-- /.navbar-header -->

        <div class="navbar-header pull-right" role="navigation">
            <ul class="nav ace-nav">


                <li class="light-blue">
                    <a data-toggle="dropdown" href="#" class="dropdown-toggle">
                        <span class="user-info">
                            <small>Hola,</small>
                            {{ \Sentry::getUser()->first_name }}&nbsp;{{ \Sentry::getUser()->last_name }}
                        </span>

                        <i class="icon-caret-down"></i>
                    </a>

                    <ul class="user-menu pull-right dropdown-menu dropdown-yellow dropdown-caret dropdown-close">
                        @if(Sentry::getUser()->hasAccess('usuarios::editar-preferencias'))
                            <li>
                                <a href="{{ action('Ttt\Panel\UsuarioController@verPreferencias') }}"  title="Preferencias de usuario">
                                    <i class="icon-cog"></i>
                                    Preferencias
                                </a>
                            </li>
                        @endif

                        <li class="divider"></li>

                        <li>
                            <a href="{{ action('Ttt\Panel\LoginController@logout') }}" title="Cerrar sesión ['{{ \Sentry::getUser()->first_name }}&nbsp;{{ \Sentry::getUser()->last_name }}']">
                                <i class="icon-off"></i>
                                Cerrar sesión
                            </a>
                        </li>
                    </ul>
                </li>
            </ul><!-- /.ace-nav -->
        </div><!-- /.navbar-header -->
    </div><!-- /.container -->
</div>
