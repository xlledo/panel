<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
        <title>{{ $message->getHeaders()->get('Subject') }}</title>
        <style type="text/css">
            a:hover {text-decoration: underline !important; }
        </style>
    </head>
    <body style="margin: 0px; background-color: #ebebeb;" marginheight="0" topmargin="0" marginwidth="0" leftmargin="0" bgcolor="#ebebeb">
        <!--100% body table-->
        <table width="100%" border="0" cellspacing="0" cellpadding="0" style="background-color:#ebebeb;">
			<tr>
				<td height="20">&nbsp;</td>
			</tr>
            <tr>
                <td>
                    @include('packages/ttt/panel/emails/cabecera')

                    <table width="620" border="0" align="center" cellpadding="0" cellspacing="0" style="background:#fff;">

                        <tr>
                            <td height="30" >&nbsp;</td>
                            <td height="30">&nbsp;</td>
                            <td height="30">&nbsp;</td>
                        </tr>
                        <tr>
                            <td width="20">&nbsp;</td>
                            <td>
								@yield('content')
                            </td>
                            <td width="20">&nbsp;</td>
                        </tr>

                    </table>
                    <table width="620" border="0" align="center" cellpadding="0" cellspacing="0">
						<tr><td height="10" style="background-color:#ffffff;">&nbsp;</td></tr>
                        <tr>
                            <td>
                                <img border="0" alt="" src="{{ asset('packages/ttt/panel/images/emails/asientos.jpg') }}" style="border:0;margin:0;padding:0;" align="left">
                            </td>
                        </tr>

                        <tr>
                            <td height="27">
                                <img border="0" alt="" src="{{ asset('packages/ttt/panel/images/emails/sombra.png') }}" style="border:0;margin:0;padding:0;">
                            </td>
                        </tr>
                    </table>
                    <!--/contenido-->

                    @include('packages/ttt/panel/emails/pie')

                </td>
            </tr>
        </table>

    </body>
</html>
