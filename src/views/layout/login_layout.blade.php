<!doctype html>
<html xmlns="http://www.w3.org/1999/xhtml" dir="ltr" lang="es-ES">
    <head>
        @include('packages/ttt/panel/layout/head')
    </head>
    <body class="login-layout">
		<div class="main-container">
			<div class="main-content">
				<div class="row">
                    @yield('content')
                </div>
            </div>
        </div>
        <script type="text/javascript">
            @yield('inline_js')
        </script>
    </body>
</html>
