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
				<a href="{{ action('Ttt\Panel\CategoriaController@index') }}" title="Volver al listado de árboles">Árboles de categorías</a>
			</li>
			@if(! $item->isRoot())
				<li>
					{{ link_to('admin/categorias/ver-arbol/' . $item->getRoot()->id, $item->getRoot()->nombre, array('title' => $item->getRoot()->nombre)) }}
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
		@if(Sentry::getUser()->hasAccess('categorias::crear'))
			<a href="{{ action('Ttt\Panel\CategoriaController@nuevo', $item->getRoot()->id) }}" title="Nueva subcategoría en {{ $item->getRoot()->nombre }}" class="btn btn-sm btn-primary no-border"><i class="icon-file"></i> Nueva subcategoría</a>
		@endif
	@endif

@stop
@section('page_header')
	@if($action == 'create')
		<h1><small><a href="{{ action('Ttt\Panel\CategoriaController@index') }}" title="Volver al listado">Categorías</a> <i class="icon-double-angle-right"></i></small>Nueva categoría en {{ link_to('admin/categorias/ver-arbol/' . $item->getRoot()->id, $item->getRoot()->nombre, array('title' => $item->getRoot()->nombre)) }}</h1>
	@elseif($action == 'createArbol')
		<h1>Nuevo árbol de <a href="{{ action('Ttt\Panel\CategoriaController@index') }}" title="Volver al listado">Categorías</a></h1>
	@elseif($action == 'edit')
		<h1><small><a href="{{ action('Ttt\Panel\CategoriaController@index') }}" title="Volver al listado">Categorías</a> <i class="icon-double-angle-right"></i></small> {{ $item->nombre }} ubicado en {{ link_to('admin/categorias/ver-arbol/' . $item->getRoot()->id, $item->getRoot()->nombre, array('title' => $item->getRoot()->nombre)) }}</h1>
	@else
		<h1><small><a href="{{ action('Ttt\Panel\CategoriaController@index') }}" title="Volver al listado">Árbol de categorías</a> <i class="icon-double-angle-right"></i></small> {{ $item->nombre }}</h1>
	@endif
@stop
@section('content')
	<div class="row">
	    <div class="col-xs-12">
			<div id="tabs">
				<ul id="aux">
				     <li><a href="#datos" title="datos"><i class="icon-list"></i>  Datos</a></li>
				</ul>

				<div id="datos">
					@if($item->isRoot())
						<form class="clearfix" action="<?php echo ($action == 'createArbol') ? action('Ttt\Panel\CategoriaController@crearArbol') : action('Ttt\Panel\CategoriaController@actualizarRaiz') ; ?>" method="post">
					@else
						<form class="clearfix" action="<?php echo ($action == 'create') ? action('Ttt\Panel\CategoriaController@crear') : action('Ttt\Panel\CategoriaController@actualizar') ; ?>" method="post">
					@endif
						@if($action != 'create' && $action != 'createArbol')
							<input type="hidden" name="id" id="id" value="{{ $item->id }}" />
						@endif

						@if($action == 'create')
							<input type="hidden" name="parent_id" id="parent_id" value="{{ $item->getRoot()->id }}" />
						@endif
					    <div class="acciones pull-right">
							@if($action != 'createArbol' && Sentry::getUser()->hasAccess('categorias::verArbol'))
								<a href="{{ action('Ttt\Panel\CategoriaController@verArbol', $item->isRoot() ? $item->id : $item->getRoot()->id) }}" title="Volver al árbol {{ $item->isRoot() ? $item->nombre : $item->getRoot()->nombre }}" class="btn btn-sm btn-primary no-border"><i class="icon-double-angle-left"></i> Volver al árbol</a>
							@endif
					        <input type="submit" value="Guardar" name="guardar" class="btn btn-sm btn-success no-border">
					    </div>
					    <div class="row">
					        <div class="col-xs-12">
					            <div class="widget-box transparent">
					                <div class="widget-header widget-header-small">
					                    <h4 class="smaller lighter">Datos</h4>
					                </div>
					                <div class="widget-body">
					                    <div class="widget-main row">
											<div class="col-md-3">
												<div class="checkbox">
													<label for="visible">
														<input type="checkbox" class="ace ace-checkbox-2" name="visible" id="visible" value="1"<?php if($item->visible): ?> checked="checked" <?php endif; ?>/>
														<span class="lbl"> Visible</span>
													</label>
												</div>
												@if($item->isRoot())
													<div class="checkbox">
														<label for="protegida">
															<input type="checkbox" class="ace ace-checkbox-2" name="protegida" id="protegida" value="1"<?php if($item->protegida): ?> checked="checked" <?php endif; ?>/>
															<span class="lbl"> Protegida</span>
														</label>
													</div>
												@endif
											</div>
					                        <div class="col-md-3">
					                            <div class="form-group @if ($errors->first('nombre')) has-error @endif">
					                                <label for="nombre">Nombre *</label>
					                                <input type="text" class="form-control" name="nombre" id="nombre" value="{{ $item->nombre }}" size="20" />
													@if ($errors->first('nombre'))
														@foreach($errors->get('nombre') as $err)
															<span class="help-block">{{ $err }}</span>
														@endforeach
													@endif
					                            </div>
												@if(Sentry::getUser()->isSuperUser() && ! in_array($action, array('create', 'createArbol')))
													<div class="form-group">
														<label for="slug">Slug</label>
														<input type="text" class="form-control" name="slug" id="slug" value="{{ $item->slug }}" readonly="readonly" size="20" />
													</div>
												@endif
					                        </div>
											@if(! $item->isRoot())
												<div class="col-md-3">
													<div class="form-group">
														<label for="valor">Valor</label>
														<input type="text" class="form-control" name="valor" id="valor" value="{{ $item->valor }}" size="20" />
													</div>
												</div>
											@endif
					                    </div>
					                </div>
					            </div>
					        </div>
					    </div>
						<div class="acciones pull-right">

							<input type="submit" value="Guardar" class="boton btn btn-sm btn-success no-border" name="guardar"></li>

						</div>
					</form>
				</div>
			</div>
		</div>
	</div>
	@if($item->isRoot() && $action == 'editArbol' && Sentry::getUser()->hasAccess('categorias::borrar-arbol'))
		<div class="space-6"></div>
		<div class="acciones">
			<a class="btn btn-minier btn-danger no-border" title="Eliminar ?" href="{{ action('Ttt\Panel\CategoriaController@borrarArbol', $item->id) }}"><i class="icon-trash"></i>Borrar árbol de categorías</a>
		</div>
	@endif
	@if(! $item->isRoot() && $action == 'edit' && Sentry::getUser()->hasAccess('categorias::borrar'))
		<div class="space-6"></div>
		<div class="acciones">
			<a class="btn btn-minier btn-danger no-border" title="Eliminar ?" href="{{ action('Ttt\Panel\CategoriaController@borrar', $item->id) }}"><i class="icon-trash"></i>Borrar</a>
		</div>
	@endif
@stop
@section('inline_js')
	@parent
@stop
