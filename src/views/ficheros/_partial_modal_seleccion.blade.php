<!-- Modal -->
<div class="modal fade" id="modal_select_fichero" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" style="width: 80%;">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
        <h4 class="modal-title" id="myModalLabel">Gestión de Ficheros Adjuntos</h4>
      </div>
      <div class="modal-body">
          <div class="tabbable">

                                        <ul class="nav nav-tabs" id="myTab">
                                                <li class="active">
                                                        <a data-toggle="tab" href="#agrega_fichero">
                                                                <i class="green ace-icon fa fa-home bigger-120"></i>
                                                                Agregar
                                                        </a>
                                                </li>
                                                <li>
                                                        <a data-toggle="tab" href="#selecciona_fichero">
                                                                Seleccionar
                                                        </a>
                                                </li>
                                        </ul>

                                        <div class="tab-content">
                                                <div id="agrega_fichero" class="tab-pane fade active in">
                                                        <?php $add_form = (isset($add) && $add) ? '_add' : '' ;?>
                                                        <?php $package  = (isset($package) && $package ) ? $package : 'panel'; ?>
                                                        @include('packages/ttt/' . $package . '/' . $viewsDir . '/ficheros/_partial_form' . $add_form, array('action_fichero'=>'create'))
                                                </div>
                                                <div id="selecciona_fichero" class="tab-pane fade" >
                                                        @include('packages/ttt/panel/ficheros/_partial_modal_listado')
                                                </div>
                                        </div>

                                </div>
      </div>
    </div>
  </div>
</div>
