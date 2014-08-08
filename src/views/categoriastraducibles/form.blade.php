@extends('packages/ttt/panel/layout/panel_layout')
@section('migas')
<div class="breadcrumbs" id="breadcrumbs">
		<script type="text/javascript">
			try{ace.settings.check('breadcrumbs' , 'fixed')}catch(e){}
		</script>
		<ul class="breadcrumb">
			<li>
				<i class="icon-home home-icon"></i>
				<a href="{{ action('Ttt\Panel\DashboardController@index') }}">Inicio</a>
			</li>
			<li>
				<a href="{{ action('Ttt\Panel\CategoriaTraducibleController@index') }}" title="Volver al listado de árboles">Árboles de categorías</a>
			</li>
			@if(! $item->isRoot())
				<li>
					{{ link_to('admin/categorias-traducibles/ver-arbol/' . $item->getRoot()->id, $item->getRoot()->nombre, array('title' => $item->getRoot()->nombre)) }}
				</li>
			@endif
			<li>
				@if(! $item->isRoot())
					<?php if ($action == 'create'): ?>
						Nueva categoría en {{ $item->getRoot()->nombre }}
					<? else: ?>
						Editar <?php echo $item->nombre; ?>
					<?php endif; ?>
				@else
					<?php if ($action == 'createArbol'): ?>
						Nuevo árbol de categorías
					<? else: ?>
						Editar <?php echo $item->nombre; ?>
					<?php endif; ?>
				@endif
			</li>
		</ul>
</div>
@stop

@section('tools')
	@if($action == 'edit' || $action == 'editArbol')
		@if(Sentry::getUser()->hasAccess('categorias-traducibles::crear'))
			<a href="{{ action('Ttt\Panel\CategoriaTraducibleController@nuevo', $item->getRoot()->id) }}" title="Nueva subcategoría en {{ $item->getRoot()->nombre }}" class="btn btn-sm btn-primary no-border"><i class="icon-file"></i> Nueva subcategoría</a>
		@endif
	@endif

@stop
@section('page_header')
	@if($action == 'create')
		<h1><small><a href="{{ action('Ttt\Panel\CategoriaTraducibleController@index') }}" title="Volver al listado">Categorías</a> <i class="icon-double-angle-right"></i></small>Nueva categoría en {{ link_to('admin/categorias-traducibles/ver-arbol/' . $item->getRoot()->id, $item->getRoot()->nombre, array('title' => $item->getRoot()->nombre)) }}</h1>
	@elseif($action == 'createArbol')
		<h1>Nuevo árbol de <a href="{{ action('Ttt\Panel\CategoriaTraducibleController@index') }}" title="Volver al listado">Categorías</a></h1>
	@elseif($action == 'edit')
		<h1><small><a href="{{ action('Ttt\Panel\CategoriaTraducibleController@index') }}" title="Volver al listado">Categorías</a> <i class="icon-double-angle-right"></i></small> {{ $item->nombre }} ubicado en {{ link_to('admin/categorias-traducibles/ver-arbol/' . $item->getRoot()->id, $item->getRoot()->nombre, array('title' => $item->getRoot()->nombre)) }}</h1>
	@else
		<h1><small><a href="{{ action('Ttt\Panel\CategoriaTraducibleController@index') }}" title="Volver al listado">Árbol de categorías</a> <i class="icon-double-angle-right"></i></small> {{ $item->nombre }}</h1>
	@endif
@stop
@section('content')
	<div class="row">
	    <div class="col-xs-12">
			<div id="tabs">
				<ul id="aux" class="mi">
				     <li><a href="#datos" title="datos"><i class="icon-list"></i>  Datos</a></li>
				</ul>

				<div id="datos" class="no-padding">
					<div id="tabsI" class="clearfix">
						<ul class="pestanias">
							@if($action != 'create' && $action != 'createArbol')
								@foreach($item->traducciones as $trad)
									@if($trad->idioma != 'new')
										<li>
											<a href="#datos-{{ $trad->idioma }}">
												{{ Ttt\Panel\Repo\Idioma\Idioma::getByCodigoIso2($trad->idioma)->nombre }}
											</a>
										</li>
									@endif
								@endforeach
								@if($item->hasTranslation('new') || $item->traducciones->count() != count($todos_idiomas))
									<li><a href="#datos-new"> Nueva Traducción </a></li>
								@endif
							@else
								<li><a href="#datos-{{ $idioma_predeterminado->codigo_iso_2 }}">{{ ucfirst($idioma_predeterminado->nombre) }}</a></li>
							@endif
						</ul>
						{{-- Formularios --}}
						@if($action == 'create' || $action == 'createArbol')
							{{-- Form elemento nuevo --}}
							@include('packages/ttt/panel/categoriastraducibles/partialform', array('action'=>$action, 'nueva_traduccion'=>false, 'clave_idioma' => $idioma_predeterminado->codigo_iso_2, 'trad' => $item->traduccion($idioma_predeterminado->codigo_iso_2)))
						@else

							{{-- Creamos los forms de idiomas --}}
							<?php $idioma_error =  (\Session::has('idioma_error')) ? \Session::get('idioma_error') : FALSE ?>
							@foreach($item->traducciones as $trad)
								@if($trad->idioma != 'new')
									@include('packages/ttt/panel/categoriastraducibles/partialform', array('trad'=>$trad,'idioma_error'=>$idioma_error, 'action'=>$action, 'nueva_traduccion' => false, 'clave_idioma' => $trad->idioma))
								@endif
							@endforeach

							{{-- Form Nueva traduccion si es necesario --}}
							@if($item->hasTranslation('new') || $item->traducciones->count() != count($todos_idiomas))
								@include('packages/ttt/panel/categoriastraducibles/partialform', array('action'=>$action, 'nueva_traduccion' => true, 'clave_idioma' => 'new', 'trad' => $item->traduccion('new')))
							@endif
						@endif
					</div>
				</div>
			</div>
		</div>
	</div>
	@if($item->isRoot() && $action == 'editArbol' && Sentry::getUser()->hasAccess('categorias-traducibles::borrar-arbol'))
		<div class="space-6"></div>
		<div class="acciones">
			<a class="btn btn-minier btn-danger no-border" title="Eliminar ?" href="{{ action('Ttt\Panel\CategoriaTraducibleController@borrarArbol', $item->id) }}"><i class="icon-trash"></i>Borrar árbol de categorías</a>
		</div>
	@endif
	@if(! $item->isRoot() && $action == 'edit' && Sentry::getUser()->hasAccess('categorias-traducibles::borrar'))
		<div class="space-6"></div>
		<div class="acciones">
			<a class="btn btn-minier btn-danger no-border" title="Eliminar ?" href="{{ action('Ttt\Panel\CategoriaTraducibleController@borrar', $item->id) }}"><i class="icon-trash"></i>Borrar</a>
		</div>
	@endif
@stop
@section('inline_js')
	@parent
@stop
