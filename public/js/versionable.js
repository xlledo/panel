
function initVersionable()
{
    $(".selector_versiones").click(function()
    {
       var id_version       = $(this).data("version");
       var form_element     = $(this).data("formelement");
       
       
       if(id_version != -1)
       {
            $.getJSON(BASE_URL + 'version/' + id_version,
                         function(data) {
                            if(data){
                                $("#" + form_element).val(data.valor_nuevo);
                            }
                         });
        }else{
            $("#" + form_element).val($(this).data("content"));
        }
    });
}