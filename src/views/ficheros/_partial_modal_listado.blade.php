<table class="table table-striped table-bordered table-hover listado" summary="Listado de Ficheros" border="0" cellpadding="0" cellspacing="1">    
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
            <button class='btn btn-primary btn-xs'>Seleccionar</button>
        </td>
    <tr/>
    @endforeach
</table>