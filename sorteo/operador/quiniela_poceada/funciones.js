configuracion = null;

function buscar_valor_por_campo(kcampo, nposicion, devolver) {
    var existe = '';
    obj = configuracion.premios;
    for (var i = 0; i < obj.length; i++) {
        if (obj[i][kcampo] == nposicion) {
            existe = obj[i][devolver];
            return existe;
        }
    };
}

function marcar_extraccion_sorteada(nposicion, sorteado) {
    var existe = '';
    obj = configuracion.premios;
    for (var i = 0; i < obj.length; i++) {
        if (obj[i]['posicion'] == nposicion) {
            obj[i]['sorteado'] = sorteado;
        }
    };
}


function cantidad_premios(afecta) {
    var contador = 0;

    obj = configuracion.premios;

    for (var i = 0; i < obj.length; i++) {
        if (obj[i]['afecta'] == afecta && obj[i]['sorteado'] == false)
            contador++;
    }
    return contador;
}

function animarMensajes() {
    $("#error_juego.alert").slideUp("slow");
    $("#warning_juego.alert").slideUp("slow");
    $("#success_juego.alert").slideUp("slow");
}

function validar_configuracion(elemento, tipo) {
    if (tipo == 'posicion') {
        var success = false;
        if (isNaN(elemento.val()) || elemento.val().indexOf('.') != -1) {
            $("#fraccion_div, .subtitulo_juego").css("display", "none");
            $("#entero, #fraccion").val('');
            $("#error_juego").slideDown("slow");
            $('#error_juego.alert > .contenido_error').html('<strong>Posicion: </strong>Solo se aceptan numeros');
            elemento.select();
            elemento.val('');
        } else if (elemento.val().length == 0 || buscar_valor_por_campo('posicion', elemento.val(), 'posicion') == undefined) {
            $("#fraccion_div, .subtitulo_juego").css("display", "none");
            $("#entero, #fraccion").val('');
            $("#error_juego").slideDown("slow");

            if (configuracion.premios !== undefined)
                $('#error_juego.alert > .contenido_error').html('<strong>Posicion: </strong>Es Necesario Ver el Programa de Premios');

            $('#error_juego.alert > .contenido_error').html('<strong>Posicion: </strong>Es necesario Ingresar una posicion (' + configuracion.premios[0].posicion + '-' + configuracion.premios[configuracion.premios.length - 1].posicion + ')');
            elemento.select();
            elemento.val('');
        } else
            success = true;

    } else if (tipo == 'entero') {
        if (isNaN(elemento.val()) || elemento.val().indexOf('.') != -1) {
            $("#error_juego").slideDown("slow");
            $('#error_juego.alert > .contenido_error').html('<strong>Entero: </strong>Solo se aceptan numeros');
            elemento.select();
            elemento.val('');
        } else if (elemento.val().length == 0 || (parseInt(elemento.val()) < parseInt(configuracion.min_billete) || parseInt(elemento.val()) > parseInt(configuracion.max_billete))) {
            $("#error_juego").slideDown("slow");
            $('#error_juego.alert > .contenido_error').html('<strong>Entero: </strong>Es necesario Ingresar un entero (' + configuracion.min_billete + ' - ' + configuracion.max_billete + ')');
            elemento.select();
        } else
            success = true;
    } else if (tipo == 'fraccion') {
        if (isNaN(elemento.val()) || elemento.val().indexOf('.') != -1) {
            $("#error_juego").slideDown("slow");
            $('#error_juego.alert > .contenido_error').html('<strong>Fraccion: </strong>Solo se aceptan numeros');
            elemento.select();
            elemento.val('');
        } else if (elemento.val().length == 0 || (parseInt(elemento.val()) < parseInt(configuracion.min_fraccion) || parseInt(elemento.val()) > parseInt(configuracion.max_fraccion))) {
            $("#error_juego").slideDown("slow");
            $('#error_juego.alert > .contenido_error').html('<strong>Fraccion: </strong>Es necesario Ingresar una fraccion (' + configuracion.min_fraccion + ' - ' + configuracion.max_fraccion + ')');
            elemento.select();
        } else
            success = true;
    }
    return success;
}


function habilitarAfecta() {
    if (buscar_valor_por_campo('posicion', $("#posicion").val(), 'afecta') == 'fraccion') {
        $("#entero_div").css("display", "inline");
        $("#fraccion_div").css("display", "inline");
    } else if (buscar_valor_por_campo('posicion', $("#posicion").val(), 'afecta') == 'entero')
        $("#entero_div").css("display", "inline");
}

function configuracionJuego() {
    $('#posicion').attr('maxlength', 2);

    if (configuracion.max_billete != null) {
        $('#entero').attr('maxlength', configuracion.max_billete.toString().length);
    }
}

function iniciar_juego(juego) {
    configuracionJuego();
    $("#posicion").unbind("keypress");
    $("#entero").unbind("keypress");
    $("#fraccion").unbind("keypress");
    $('#entero').focus();
    $("#posicion").keypress(
        function(e) {
            if (e.which == 13) {
                $("#entero, #fraccion").val('');
                $("#fraccion_div").css("display", "none");
                //animarMensajes();
                if (validar_configuracion($(this), 'posicion')) {
                    $(".subtitulo_juego").css("display", "block");
                    var premio = '';
                    $(".subtitulo_juego").html(buscar_valor_por_campo('posicion', $("#posicion").val(), 'descripcion') + '<strong>' + premio + '</strong>');
                    habilitarAfecta();
                    $("#entero").focus();
                }
            }
        }
    ).blur(
        function() {
            if (isNaN($("#posicion").val()) || $("#posicion").val().indexOf('.') != -1) {
                animarMensajes();
                if (validar_configuracion($(this), 'posicion')) {
                    $(".subtitulo_juego").css("display", "block");
                    var premio = '';
                    if (buscar_valor_por_campo('posicion', $("#posicion").val(), 'afecta') == 'fraccion')
                        premio = 'Premio Extraordinario';
                    $(".subtitulo_juego").html(buscar_valor_por_campo('posicion', $("#posicion").val(), 'descripcion') + ' (' + buscar_valor_por_campo('posicion', $("#posicion").val(), 'afecta') + ') <strong>' + premio + '</strong>');
                    habilitarAfecta();
                    $("#entero").focus();
                }
            }

        }
    );

    $("#entero").keypress(
        function(e) {
            if (e.which == 13) {
                animarMensajes();
                if (validar_configuracion($(this), 'entero')) {
                    if (buscar_valor_por_campo('posicion', $("#posicion").val(), 'afecta') == 'entero')
                        guardar_extraccion(juego);
                    else
                        $("#fraccion").focus();
                }
            }
        }
    ).blur(
        function() {}
    );

    $("#fraccion").keypress(
        function(e) {
            if (e.which == 13) {
                animarMensajes();
                if (validar_configuracion($(this), 'fraccion'))
                    guardar_extraccion(juego);
            }
        }
    ).blur(
        function() {
            $('#error_juego').slideUp("slow");
        }
    );
}


function guardar_extraccion(nombre_juego) {
    var afecta = buscar_valor_por_campo('posicion', $('#posicion').val(), 'afecta');
    $.post(
        'sorteo/operador/quiniela_poceada/quiniela_poceada_sorteador_ajax.php', {
            juego: nombre_juego,
            entero: $('#entero').val(),
            posicion: $('#posicion').val(),
            afecta: afecta,
            accion: 'control_ingreso'
        },
        function(data) {
            mostrarMensaje(data);
            if (data.tipo == 'success') {
                if (afecta == 'entero') {
                    if (data.valida == false) {
                        $.when(cargarConfiguracion({ accion: "configuracion", juego: "primer_juego" })).then(() => {
                            configuracion.cantidad_premios_tradicional = parseInt(configuracion.cantidad_premios_tradicional) - 1;
                            if (buscar_valor_por_campo('sorteado', false, 'posicion') !== undefined) {
                                $("#posicion").val('');
                                
                                $("#posicion").val(buscar_valor_por_campo('sorteado', false, 'posicion'));
                                var e = $.Event("keypress", { which: 13 });
                                $("#posicion").trigger(e);
                            }else{
                                $("#posicion").val('');
                            }
                        });
                    }
                } else if (afecta == 'fraccion') {
                    configuracion.cantidad_premios_extraordinario = parseInt(configuracion.cantidad_premios_extraordinario) - 1;
                }
                //Contador de premios tradicionales y extraordinarios, para mostrar en pantalla
                if (configuracion.cantidad_premios_tradicional == 1)
                    mostrarMensaje({ mensaje: 'Ultima Extraccion del Juego Poceada Cordobesa', tipo: 'info' });
            }

            $("#entero, #fraccion").val('');
            //$("#posicion").val('');
            $("#entero").focus();
            //buscarGanadores1();
        }
    ).error(
        function(jqXHR, textStatus, errorThrown) {
            $("#error_juego.alert").slideDown("slow");
            $('#error_juego.alert > .contenido_error').html('Se ha producido un error ("' + errorThrown + '").<br>' + jqXHR.statusText + '-' + jqXHR.status);
        }
    ).complete();
}

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
    $("#fraccion_div, .subtitulo_juego").css("display", "none");
    $("#entero, #fraccion").val('');
    $("#entero").focus();

}

function cargarConfiguracion(param) {
    return $.post(
        'sorteo/operador/quiniela_poceada/quiniela_poceada_sorteador_ajax.php',
        param,
        function(data) {
            try {
                configuracion = data;

                var cantidad_premios_tradicional = cantidad_premios('entero');
                var cantidad_premios_extraordinario = cantidad_premios('fraccion');
                configuracion.cantidad_premios_tradicional = cantidad_premios_tradicional;
                configuracion.cantidad_premios_extraordinario = cantidad_premios_extraordinario;

                iniciar_juego(param.juego);
                if (buscar_valor_por_campo('sorteado', false, 'posicion') !== undefined) {
                    $("#posicion").val(buscar_valor_por_campo('sorteado', false, 'posicion'));
                    var e = $.Event("keypress", { which: 13 });
                    $("#posicion").trigger(e);
                }

            } catch (err) {
                $("#error_juego.alert").slideDown("slow");
                $('#error_juego.alert > .contenido_error').html('Error ' + err.message);
                $("#fraccion_div, .subtitulo_juego").css("display", "none");
                $("#entero, #fraccion").val('');
                $("#entero").focus();
            }
        }
    ).error(
        function(jqXHR, textStatus, errorThrown) {
            $("#error_juego.alert").slideDown("slow");
            $('#error_juego.alert > .contenido_error').html('Se ha producido un error ("' + errorThrown + '").<br>' + jqXHR.statusText + '-' + jqXHR.status);
        }
    );
}


function deshabilitarJuegos() {
    $("#posicion").prop('disabled', true);
    $("#entero").prop('disabled', true);
    $("#fraccion").prop('disabled', true);
}