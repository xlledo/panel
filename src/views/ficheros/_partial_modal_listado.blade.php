<table class="table table-striped table-bordered table-hover" id="tablaModalFichero" summary="Listado de Ficheros" border="0" cellpadding="0" cellspacing="1">
    <thead>
        <tr>
            <th>Preview</th>
            <th>Nombre</th>
            <th>Subido por</th>
            <th>Fecha</th>
            <th>Acciones</th>
        </tr>
    </thead>
    <tbody>
    @foreach($ficheros_todos as $index => $item)
    <tr class="@if($index % 2 == 0) par @else impar @endif">
        <td>
            @if($item->esImagen())
                 <img src='{{URL::to('/') . '/' .$item->ruta . $item->fichero }}' width="50" />
            @else
                 No disponible
            @endif
        </td>
        <td>
            {{ $item->nombre }}
        </td>
        <td>
            {{ ucfirst($item->maker->first_name . ' ' .$item->maker->last_name) }}
        </td>
        <td>
            {{ $item->updated_at }}
        </td>
        <td>
            @if($action_fichero!='edit')
                <a href="{{ URL::to('admin/' . $modulo .   '/asociar_fichero/' . $item->id . '?from=' . $item_id) }}" class="btn btn-xs btn-primary">Seleccionar</a>
            @else
                <a href='#' class='btn btn-xs btn-info btn_asociar_fichero' 
                        data-id-fichero="{{$item->id}}" 
                        data-fichero='{{$item->nombre}}'>Asociar y cerrar</a>
            @endif
        </td>
    </tr>
    @endforeach
    </tbody>
</table>

<script type='text/javascript'>
    $(document).ready(function()
    {
        $('#tablaModalFichero').dataTable({
                        "lengthChange": false,
                        "pageLength": 6,
                        "paging": true,
                        "language":{
                            "info":      "Mostrando _START_ to _END_ de _TOTAL_ elementos",
                            "paginate": {
                                "first":      "Primero",
                                "last":       "Último",
                                "next":       "Próximo",
                                "previous":   "Anterior",
                            }}
                });
    });
</script>
