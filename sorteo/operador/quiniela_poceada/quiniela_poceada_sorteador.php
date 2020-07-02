<?php
session_start();
include_once dirname(__FILE__) . '/../../../mensajes.php';
require_once dirname(__FILE__) . '/../../../db.php';
if (!isset($_SESSION['sorteo'])) {
    error('Es necesario seleccionar un sorteo');
    die();
}
$protocolo = isset($_SERVER['HTTPS']) ? 'https' : 'http';

if ($_SERVER['SERVER_NAME'] == 'localhost' || $_SERVER['SERVER_NAME'] == '127.0.0.1' || $_SERVER['SERVER_NAME'] == 'desa.local') {
    $url = $protocolo . '://' . $_SERVER['SERVER_NAME'] . '/desa/';
} else if ($_SERVER['SERVER_NAME'] == 'desa.loteriadecordoba.com.ar' || $_SERVER['SERVER_NAME'] == 'svn.loteriadecordoba.com.ar') {
    $url = $protocolo . '://' . $_SERVER['SERVER_NAME'] . '/';
} else {
    $url = $protocolo . '://' . $_SERVER['SERVER_NAME'] . '/app/';
}

?>
<!DOCTYPE html>
<html>

<head>
    <title>SGS - Sistema de Gestion de Sorteos</title>
    <base href="<?php echo $url; ?>/sgs/">
    <link rel="stylesheet" type="text/css" href="librerias/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="css/estilo.css">
    <link rel="stylesheet" type="text/css" href="sorteo/operador/quiniela_poceada/estilo_sorteador.css">
    <script type="text/javascript" src="librerias/jquery/jquery-1.10.1.js"></script>
    <script type="text/javascript" src="librerias/bootstrap/js/bootstrap.min.js"></script>
    <script type="text/javascript" src="librerias/bootstrap/js/bootstrap-fileupload.min.js"></script>
    <script type="text/javascript" src="librerias/bootstrap/js/bootstrap-datetimepicker.min.js"></script>
    <script type="text/javascript" src="sorteo/operador/quiniela_poceada/funciones.js"></script>
    <script type="text/javascript" src="js/loadingoverlay.js"></script>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <?php
$_SESSION['tipo_usuario'] = 'ROL_JEFE_SORTEO';

if ($_SESSION['tipo_usuario'] != 'ROL_JEFE_SORTEO' && $_SESSION['tipo_usuario'] != 'ROL_OPERADOR') {
    die(' <div id="error" class="alert alert-error">
                <button type="button" class="close" onclick="$(\'.alert\').slideUp(\'slow\');">x</button>
                <div class="contenido_error">Para poder acceder al sorteo es necesario ser JEFE DE SORTEO u OPERADOR</div>
              </div>');
}

try {
    conectar_db();
    $rs_usuario = sql("SELECT TS.ID_JEFE,TS.ID_OPERADOR,S.DESCRIPCION AS USUARIO,E.DESCRIPCION AS USUARIO1
                      FROM SGS.T_SORTEO TS,
                           SUPERUSUARIO.USUARIOS S,
                          SUPERUSUARIO.USUARIOS E
                      WHERE TS.SORTEO = ?
                        AND TS.ID_JUEGO=?
                        AND TS.ID_OPERADOR = S.ID_USUARIO(+)
                        AND TS.ID_JEFE = E.ID_USUARIO(+)", array($_SESSION['sorteo'], $_SESSION['id_juego']));
    if (!$row = siguiente($rs_usuario)) {
        die(error('Sorteo inexistente, debe seleccionar el sorteo'));
    }
    if ($row->ID_JEFE != 'DU' . $_SESSION['dni'] && $row->ID_OPERADOR != 'DU' . $_SESSION['dni']) {
        die(error('Usuario no habilitado para realizar el sorteo'));
    }
    $usuario_opera = ($row->ID_JEFE == 'DU' . $_SESSION['dni']) ? $row->USUARIO : $row->USUARIO1;

} catch (exception $e) {
    die($db->ErrorMsg());
}

?>
    <script type="text/javascript">
    var param;

    param = {
        accion: 'mostrar',
        juego: 'primer_juego'
    };



    function cargar_juego(classN) {
        $.get('sorteo/operador/quiniela_poceada/quiniela_poceada_sorteador_ajax.php', {
            accion: 'mostrar_extracto',
            tipo: classN
        }).done(
                function(data){
                    mostrarMensaje(data);

              });
    };

    var buscarGanadores = function buscarGanadores() {
        $.post('sorteo/operador/quiniela_poceada/quiniela_poceada_listado_ganadores.php?buscarGanadores=' + parseInt(Math.random() * 1000000000), {
            accion: 'mostrar',
            juego: param.juego
        }).done(
            function(data) {
                $('#ganadores').html(data);
            }
        );
    }

    function habilitarJuegos() {
        $("#entero").prop('disabled', false);
        $("#fraccion").prop('disabled', false);
    }

    var controlGanadores = function controlGanadores() {
        Ajax2 = $.post('sorteo/operador/quiniela_poceada/quiniela_poceada_sorteador_ajax.php?controlGanador=1', {
            accion: 'control_ganador',
            juego: param.juego
        }).done(
            function(data) {
                if (data.mensaje == 'No Finalizo') {
                    $("#finalizar_sorteo").css("display", "none");
                    habilitarJuegos();
                }
                if (data.mensaje == 'Finalizo' && param.juego == 'primer_juego') {
                    $("#warning_juego2.alert").slideDown("slow");
                    $('#warning_juego2 > .contenido_error').html('Finalizo la extraccion de todos los numeros sorteados');
                    $("#finalizar_sorteo").css("display", "inline");
                    deshabilitarJuegos();
                } else if (data.tipo == 'database') {
                    $("#error_juego.alert").slideDown("slow");
                    $('#error_juego >.contenido_error').html(data.mensaje);
                } else if ((typeof data.coincidencia != 'undefined' || typeof data.reinicio != 'undefined' || typeof data.validacion != 'undefined')) {
                   // alert(JSON.stringify(data));
                    if (data.tipo == 'success' || data.tipo == 'info') {

                        if (data.validacion == 'VALIDA' || data.reinicio == 'SI') {
                            cargarConfiguracion({ accion: "configuracion", juego: "primer_juego" });
                        } else if(data.coincidencia != 'undefined' ) {
                            $("#success_juego.alert").slideDown("slow");
                            $('#success_juego > .contenido_error').html(data.coincidencia);
                        }
                    } else {
                        $("#error_juego.alert").slideDown("slow");
                        $('#error_juego > .contenido_error').html(data.coincidencia);
                    }
                }


            }
        );
    }

 function init()
        {
            cargarConfiguracion({ accion: "configuracion", juego: "primer_juego" });
             buscarGanadores();
            controlGanadores();
            var ref = window.setInterval(buscarGanadores, 2000);
            var ref2 = setInterval(controlGanadores, 1000);
        }
    $(document).ready(

        function() {


            init();
        });
    </script>
</head>

<body>
    <div id="contenedor_general" class="container">
        <h3 class="titulo"><img width="40" border="0" src="img/logo_loteria_peque.png">
            <?php echo $_SESSION['juego'] ?>
            <?php echo $_SESSION['sorteo'] ?> <span style="font-size:11px"><i class="icon-user"></i>
                <?php echo $_SESSION['nombre_usuario'] . ', ' . $usuario_opera; ?></span></h3>
        <div class="bar" style="width: 40%"></div>
        <div>
            <div class="row-fluid show-grid">
                <div class="navbar">
                    <div class="navbar-inner">
                        <ul class="nav juegos">
                            <li class="active">
                                <a href="#" class="primer_juego">
                                    <?php echo $_SESSION['juego_tipo']; ?></a>
                            </li>
                            <li>
                                <a href="#" class="ver_sorteo" onclick="cargar_juego(this.className); return false;"><img src="img/icono_screen.png" width="19" height="19" border="0" style="vertical-align: middle;">Pantalla Sorteo</a>
                            </li>
                            <li>
                                <a href="#" class="ver_buscando" onclick="cargar_juego(this.className); return false;"><img src="img/icono_screen.png" width="19" height="19" border="0" style="vertical-align: middle;">Pantalla Buscando Ganadores...</a>
                            </li>
                            <li>
                                <a href="#" class="ver_pozo_8_aciertos" onclick="cargar_juego(this.className); return false;"><img src="img/icono_screen.png" width="19" height="19" border="0" style="vertical-align: middle;">Pantalla Pozo 8 Aciertos</a>
                            </li>
                            <li>
                                <a href="#" class="ver_pozo_67_aciertos" onclick="cargar_juego(this.className); return false;"><img src="img/icono_screen.png" width="19" height="19" border="0" style="vertical-align: middle;">Pantalla Pozos 7 y 6 Aciertos</a>
                            </li>
                            <li class="divider-vertical"></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
        <div class="row-fluid show-grid">
            <div id="contendio_juego" class="well form-inline text-center">
                <h4>
                    Juego
                    <?php echo $_SESSION['juego_tipo']; ?>
                </h4>
                <div class="subtitulo_juego label label-info" style="display:none">Primer Premio</div>
                <form class="form-inline" action="#">
                    <span class="texto_sorteo">Posicion</span>
                    <input type="text" id="posicion" style="text-align: right;" disabled="disabled" name="posicion" class="input-small bola" size="1" tabindex="0">
                    <span id="entero_div">
                        <span class="texto_sorteo">Numero</span>
                        <input type="text" id="entero" style="text-align: right;width: 70px" name="entero" class="bola" size="1" tabindex="1">
                    </span>
                    <span id="fraccion_div" style="display:none">
                        <span class="texto_sorteo">Fraccion</span>
                        <input type="text" id="fraccion" name="fraccion" class="input-small bola" size="1" tabindex="2">
                    </span>
                </form>
            </div>
            <div>
                <input type="button" id="finalizar_sorteo" style="display:none; float: right;" onclick="if(confirm('¿Desea Finalizar el Sorteo <?php echo $_SESSION['sorteo'] ?>?')){
              $.LoadingOverlay('show', {
                    image: '',
                    text: 'Buscando Ganadores...'
                });
              cargar_juego('ver_buscando');
                            $.get('sorteo/operador/quiniela_poceada/procesar_finalizar_sorteo.php?sorteo=<?php echo $_SESSION['sorteo'] ?>&id_juego=<?php echo $_SESSION['id_juego'] ?>&accion=finalizar_sorteo',
                              function(data){
                                mostrarMensaje(data);
                               }
                            ).complete(
            function() {
                $.LoadingOverlay('hide');

            }
        );
              }" value="Finalizar Sorteo" />
            </div>
        </div>
        <div id="error_juego" class="alert alert-error" style="display:none">
            <button type="button" class="close" onclick="$('#error_juego').slideUp('slow');">x</button>
            <span><i class="icon-remove"></i></span>
            <span class="contenido_error"></span>
        </div>
        <div id="success_juego" class="alert alert-success" style="display:none">
            <button type="button" class="close" onclick="$('#success_juego.alert').slideUp('slow');">x</button>
            <span><i class="icon-ok"></i></span>
            <span class="contenido_error"></span>
        </div>
        <div id="warning_juego" class="alert alert-info" style="display:none">
            <button type="button" class="close" onclick="$('#warning_juego.alert').slideUp('slow');">×</button>
            <span><i class="icon-info-sign"></i></span>
            <span class="contenido_error"></span>
        </div>
        <div id="warning_juego2" class="alert alert-info" style="display:none">
            <button type="button" class="close" onclick="$('#warning_juego2.alert').slideUp('slow');">×</button>
            <span><i class="icon-info-sign"></i></span>
            <span class="contenido_error"></span>
        </div>
        <div id="error" class="alert alert-error" style="display:none">
            <button type="button" class="close" onclick="$('.alert').slideUp('slow');">x</button>
            <span><i class="icon-ok"></i></span>
            <span class="contenido_error"></span>
        </div>
        <div id="ganadores"></div>
        <div id="pie"><img src="img/logo_loteria_peque.png" width="20" height="20"> Desarrollado por la Loteria de Córdoba 2013</div>
    </div>
</body>

</html>