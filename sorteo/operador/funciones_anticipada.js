function animarMensajes() {
    $("#error_juego.alert").slideUp("slow");
    $("#warning_juego.alert").slideUp("slow");
    $("#success_juego.alert").slideUp("slow");
}

function preparaAnticipada() {
    var semana = loteria_anticipada.semana;
    for (var i = 0; i < loteria_anticipada.premios.length; i++) {

        var orden = loteria_anticipada.premios[i].orden;
/*        console.log("#fanticipada"+$('#semana_' + loteria_anticipada.semana + '_' + orden).attr("id").substring(6,12));*/
        $('#semana_' + loteria_anticipada.semana + '_' + orden).click(function() {

            $.post('sorteo/operador/loteria_anticipada_ajax.php', $("#fanticipada"+$(this).attr("id").substring(6,12)).serialize(), function(data) { mostrarMensaje(data) });

        });


    }

}

/*
function habilitarAnticipada(semana){
  
  switch (semana){
  
    case ('1'): $("#semana_1").prop('disabled', false); break;
    case ('2'): $("#semana_2").prop('disabled', false); break;
    case ('3'): $("#semana_3").prop('disabled', false); break;
    case ('4'): $("#semana_4").prop('disabled', false); break;
    case ('5'): $("#semana_5").prop('disabled', false); break;
    case ('6'): $("#semana_6").prop('disabled', false); break;
    case ('7'): $("#semana_7").prop('disabled', false); break;
    case ('8'): $("#semana_8").prop('disabled', false); break;
    case ('9'): $("#semana_9").prop('disabled', false); break;
  
    }
}

function deshabilitarAnticipada(semana){
  
  switch (semana){
    
    case ('1'): $("#semana_1").prop('disabled', true); break;
    case ('2'): $("#semana_2").prop('disabled', true); break;
    case ('3'): $("#semana_3").prop('disabled', true); break;
    case ('4'): $("#semana_4").prop('disabled', true); break;
    case ('5'): $("#semana_5").prop('disabled', true); break;
    case ('6'): $("#semana_6").prop('disabled', true); break;
    case ('7'): $("#semana_7").prop('disabled', true); break;
    case ('8'): $("#semana_8").prop('disabled', true); break;
    case ('9'): $("#semana_9").prop('disabled', true); break;
  
  }
}


function habilitarIncentivo(id_incentivo){
  
  switch (id_incentivo){
  
  
  case ('63'): $("#semana_2").prop('disabled', false); break;
  

    }
}

function deshabilitarIncentivo(id_incentivo){
  switch (id_incentivo){
    
  case ('34'): $("#semana_2").prop('disabled', true); break;
  
  }
}*/

function mostrarMensaje(json_mensaje) {
    if (json_mensaje.tipo == 'error') {
        animarMensajes();
        $("#error_juego.alert").slideDown("slow");
        $('#error_juego > .contenido_error').html(json_mensaje.mensaje);
    } else if (json_mensaje.tipo == 'success') {
        animarMensajes();
        $("#success_juego.alert").slideDown("slow");
        $('#success_juego > .contenido_error').html(json_mensaje.mensaje);
    } else if (json_mensaje.tipo == 'info') {
        $("#warning_juego.alert").slideDown("slow");
        $('#warning_juego > .contenido_error').html(json_mensaje.mensaje);
    }
    //$("#fraccion_div, .subtitulo_juego, #entero_div").css("display","none");
    $("#entero, #fraccion").val('');
    $("#posicion").focus();

}


function cargarConfiguracion(param) {
    $.post(
        'sorteo/operador/loteria_anticipada_ajax.php',
        param,
        function(data) {
            try {
                loteria_anticipada = data;

                preparaAnticipada();

                clearInterval(intervalo1);
                clearInterval(intervalo2);
                if (Ajax1 != null)
                    Ajax1.abort();
                if (Ajax2 != null)
                    Ajax2.abort();


            } catch (err) {
                $("#error_juego.alert").slideDown("slow");
                $('#error_juego.alert > .contenido_error').html('Error ' + err.message);
                $("#fraccion_div, .subtitulo_juego, #entero_div").css("display", "none");
                $("#entero, #fraccion").val('');
                $("#posicion").focus();
            }
        }
    ).error(
        function(jqXHR, textStatus, errorThrown) {
            $("#error_juego.alert").slideDown("slow");
            $('#error_juego.alert > .contenido_error').html('Se ha producido un error ("' + errorThrown + '").<br>' + jqXHR.statusText + '-' + jqXHR.status);
        }
    );
}
