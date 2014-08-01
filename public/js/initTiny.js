/**
 * Script que se encarga de la configuracion e inicializacion de los editores tinyMCE
 */
$(document).ready(function() {

    // Editor tiny simple
    tinyMCE.init({
        // opciones generales
        theme       : 'modern',
        language    : 'es',
        mode        : 'specific_textareas',
        editor_selector : 'mceEditorSimple',
        skin        : "light",
        width       : '100%',
        height      : '100',
		menubar: false,

        plugins     : "pagebreak,layer,table,save,insertdatetime,preview,media,searchreplace,print,contextmenu,paste,directionality,fullscreen,noneditable,visualchars,nonbreaking,template,wordcount,advlist,autosave,anchor, charmap,hr, image, link, emoticons, code,textcolor",
		extended_valid_elements: "span[*]",
        //No debe convertir los enlaces introducidos
        relative_urls : false,
        remove_script_host : false,

        // No convertir caracteres en entidades html, sÃ³lo & < > y "
        entity_encoding : "raw",

        // Opciones del tema y distribucion de los botones
    toolbar1: "insertfile undo redo | formatselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image media code",
    image_advtab: true

    });
    
     // Editor tiny simple sin <p> </p>
    tinyMCE.init({
        // opciones generales
        theme       : 'modern',
        language    : 'es',
        mode        : 'specific_textareas',
        editor_selector : 'mceEditorSimpleSinP',
        skin        : "light",
        width       : '100%',
        height      : '100',
		menubar: false,

        plugins     : "pagebreak,layer,table,save,insertdatetime,preview,media,searchreplace,print,contextmenu,paste,directionality,fullscreen,noneditable,visualchars,nonbreaking,template,wordcount,advlist,autosave,anchor, charmap,hr, image, link, emoticons, code,textcolor",

        //No debe convertir los enlaces introducidos
        relative_urls : false,
        remove_script_host : false,

        // No convertir caracteres en entidades html, sÃ³lo & < > y "
        entity_encoding : "raw",
        
        // No crea <p> </p>
        forced_root_block : '',
        force_p_newlines : false,


        // Opciones del tema y distribucion de los botones
    toolbar1: "insertfile undo redo | formatselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image media code",
    image_advtab: true

    });


    // Editor tiny completo
    tinyMCE.init({
        // opciones generales
        theme       : 'modern',
        language    : 'es',
        mode        : 'specific_textareas',
        editor_selector : 'mceEditor',
        skin        : "light",
        width       : '100%',
        height      : '300',
		menubar: false,
        plugins     : "pagebreak,layer,table,save,insertdatetime,preview,media,searchreplace,print,contextmenu,paste,directionality,fullscreen,noneditable,visualchars,nonbreaking,template,wordcount,advlist,autosave,anchor, charmap,hr, image, link, emoticons, code,textcolor",
		extended_valid_elements: "span[*]",
        //No debe convertir los enlaces introducidos
        relative_urls : false,
        remove_script_host : false,

        // No convertir caracteres en entidades html, sÃ³lo & < > y "
        entity_encoding : "raw",

        // Opciones del tema y distribucion de los botones
    toolbar1: "insertfile undo redo | formatselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image media anchor code",
    image_advtab: true,
autosave_restore_when_empty: false,

// Ver si se pueden forzar los tamaÃ±os
//        font_size_style_values : "10px,12px,14px,16px,18px,20px,22px",

        // TODO Revisar las listas externas de elementos
        //
//        content_css : "http://www.raspeig.es/raspeig_2011/assets/public/css/tiny.css",

        //
        // Drop lists for link/image/media/template dialogs
        template_external_list_url : "lists/template_list.js",
        external_link_list_url : "tinyconector/descargas/",
        external_image_list_url : "tinyconector/imagenes/",
        media_external_list_url : "lists/media_list.js"
/*
        // Style formats
        style_formats : [
                { title : 'Bold text', inline : 'b' },
                { title : 'Red text', inline : 'span', styles : { color : '#ff0000' } },
                { title : 'Red header', block : 'h1', styles : { color : '#ff0000' } },
                { title : 'Example 1', inline : 'span', classes : 'example1' },
                { title : 'Example 2', inline : 'span', classes : 'example2' },
                { title : 'Table styles'},
                { title : 'Table row 1', selector : 'tr', classes : 'tablerow1' }
        ],

        // Replace values for the template plugin
        template_replace_values : {
                username : "Some User",
                staffid : "991234"
        }
*/
    });
});