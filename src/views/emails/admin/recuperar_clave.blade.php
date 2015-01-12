@extends('packages/ttt/panel/emails/layout')
@section('content')
    <p>Has pedido una nueva clave para acceder al panel</pa>
    <p style="line-height: 120%;">Para recuperar tu password, por favor haz click <a href="{{ url('admin/cambiar-clave', $params) }}">aquí</a> o usa este enlace: <br/>
        <small><a href="{{ url('admin/cambiar-clave', $params) }}">{{ url('admin/cambiar-clave', $params) }}</a></small>
    </p>
@stop
