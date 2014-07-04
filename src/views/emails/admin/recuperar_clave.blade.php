@extends('packages/ttt/panel/emails/layout')
@section('content')
    <p>Tu clave para recuperar la contraseña es <strong>{{ $params['resetCode'] }}</strong></p>
    <p>URL: {{ url('admin/cambiar-clave', $params) }}</p>
    <p>O pinche <a href="{{ url('admin/cambiar-clave', $params) }}">aquí</a></p>
@stop
