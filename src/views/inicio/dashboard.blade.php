@extends('packages/ttt/panel/layout/panel_layout')
@section('page_header')
	<h1>Panel de inicio <small><i class="icon-double-angle-right"></i> Vista general &amp; estadísticas</small></h1>
@stop
@section('content')
	<div class="row">
	    <div class="col-md-9">
	        <div class="well">
	            <h2 class="green smaller lighter">Bienvenido!</h4>
	                <p> Le damos la bienvenida a la intranet de gestión de Laravel 4.
	                    Seleccione alguna opción del menú lateral o haga click en alguno de los accesos
	                    rápidos de la derecha.</p>
	        </div>
	    </div>
	    <div class="col-md-3">
	        <div class="widget-box transparent">
	            <div class="widget-header widget-header-flat">
	                <h2 class="lighter smaller">
	                    <i class="icon-star"></i>
	                    Accesos rápidos
	                </h2>
	            </div>

	            <div class="widget-body">
	                <div class="widget-main no-padding">
	                    <ul class="item-list">
	                        <li class="clearfix item-default">
	                            <a href="#">
	                                <span class="badge pull-right">40</span>
	                                Pedidos
	                            </a>
	                        </li>
	                    </ul>
	                </div><!-- /widget-main -->
	            </div><!-- /widget-body -->
	        </div>

	    </div>
	</div>
@stop
