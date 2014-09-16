<table class="table table-striped table-bordered table-hover" summary="Listado de Ficheros" border="0" cellpadding="0" cellspacing="1">
    
    <thead>
        <tr>
            <th>Preview</th>
            <th>Nombre</th>
            <th>Acciones</th>
        </tr>
    </thead>
    
    @foreach($ficheros_todos as $index => $item)
    <tr class="@if($index % 2 == 0) par @else impar @endif">
        
        <td>
            Preview
        </td>
        
        <td>
            {{ $item->nombre }}
        </td>
        <td>
            @if($action_fichero!='edit')
                <a href="{{ URL::to('admin/paginas/asociar_fichero/' . $item->id . '?from=' . $item_id) }}" class="btn btn-xs btn-primary">Seleccionar</a>
            @else
                <a href='#' class='btn btn-xs btn-info btn_asociar_fichero' 
                        data-id-fichero="{{$item->id}}" 
                        data-fichero='{{$item->nombre}}'>Asociar y cerrar</a>
            @endif
        </td>
        
    <tr/>
    @endforeach
</table>
