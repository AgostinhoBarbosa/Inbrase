loading = true;

Application = {};
Application.translation = {
    'en' : {
        'loading' : 'Loading'
    },
    'pt' : {
        'loading' : 'Carregando'
    },
    'es' : {
        'loading' : 'Cargando'
    }
};

Adianti.onClearDOM = function(){
	/* $(".select2-hidden-accessible").remove(); */
	$(".colorpicker-hidden").remove();
	$(".select2-display-none").remove();
	$(".tooltip.fade").remove();
	$(".select2-drop-mask").remove();
	/* $(".autocomplete-suggestions").remove(); */
	$(".datetimepicker").remove();
	$(".note-popover").remove();
	$(".dtp").remove();
	$("#window-resizer-tooltip").remove();
};


function showLoading() 
{ 
    if(loading)
    {
        __adianti_block_ui(Application.translation[Adianti.language]['loading']);
    }
}

Adianti.onBeforeLoad = function(url) 
{ 
    loading = true; 
    setTimeout(function(){showLoading()}, 400);
    if (url.indexOf('&static=1') == -1) {
        $("html, body").animate({ scrollTop: 0 }, "fast");
    }
};

Adianti.onAfterLoad = function(url, data)
{ 
    loading = false; 
    __adianti_unblock_ui( true );
    
    // Fill page tab title with breadcrumb
    // window.document.title  = $('#div_breadcrumbs').text();
};

// set select2 language
$.fn.select2.defaults.set('language', $.fn.select2.amd.require("select2/i18n/pt"));

FormatarCpfCnpj = function(e, label_tipo, label_nome = null, label_rg = null, label_data = null) {
    var s = "";

    if( e )
    {
        s = e.value;
    }
    else
    {
        s = value;
    }
    s = s.replace(/[^0-9]/g,"");
    tam =  s.length;
    if(tam < 12)
    {
        r = s.substring(0,3) + "." + s.substring(3,6) + "." + s.substring(6,9);
        r += "-" + s.substring(9,11);
        if ( tam < 4 )
            s = r.substring(0,tam);
        else if ( tam < 7 )
            s = r.substring(0,tam+1);
        else if ( tam < 10 )
            s = r.substring(0,tam+2);
        else
            s = r.substring(0,tam+3);
        $('#'+label_tipo).html('CPF');
        if (label_nome.length > 0){
            $('#'+label_nome).html('Nome');
        }
        if (label_rg.length > 0){
            $('#'+label_rg).html('RG/Identidade');
        }
        if (label_data.length > 0){
            $('#'+label_data).html('Data Nascimento');
        }
    }else{
        r = s.substring(0,2) + "." + s.substring(2,5) + "." + s.substring(5,8);
        r += "/" + s.substring(8,12) + "-" + s.substring(12,14);
        if ( tam < 3 )
            s = r.substring(0,tam);
        else if ( tam < 6 )
            s = r.substring(0,tam+1);
        else if ( tam < 9 )
            s = r.substring(0,tam+2);
        else if ( tam < 13 )
            s = r.substring(0,tam+3);
        else
            s = r.substring(0,tam+4);
        $('#'+label_tipo).html('CNPJ');
        if (label_nome.length > 0){
            $('#'+label_nome).html('Razão Social');
        }
        if (label_rg.length > 0){
            $('#'+label_rg).html('Insc. Estadual');
        }
        if (label_data.length > 0){
            $('#'+label_data).html('Data Fundação');
        }
    }
    if( e )
    {
        e.value = s;
        return true;
    }
    return s;
};