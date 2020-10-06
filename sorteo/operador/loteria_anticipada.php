<?php
session_start();
include_once dirname(__FILE__) . '/../../mensajes.php';

if (!isset($_SESSION['sorteo'])) {
    error('Es necesario seleccionar un sorteo');
    die();
}

$protocolo = isset($_SERVER['HTTPS']) ? 'https' : 'http';

if ($_SERVER['SERVER_NAME'] == 'localhost' || $_SERVER['SERVER_NAME'] == '127.0.0.1') {
    $url = $protocolo . '://' . $_SERVER['SERVER_NAME'] . '/desa/';
} else if ($_SERVER['SERVER_NAME'] == 'desa.loteriadecordoba.com.ar' || $_SERVER['SERVER_NAME'] == 'svn.loteriadecordoba.com.ar' || $_SERVER['SERVER_NAME'] == 'desa.local') {
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
    <link rel="stylesheet" type="text/css" href="sorteo/operador/estilo_sorteador.css">
    <script type="text/javascript" src="librerias/jquery/jquery-1.10.1.js"></script>
    <script type="text/javascript" src="librerias/bootstrap/js/bootstrap.min.js"></script>
    <script type="text/javascript" src="librerias/bootstrap/js/bootstrap-fileupload.min.js"></script>
    <script type="text/javascript" src="librerias/bootstrap/js/bootstrap-datetimepicker.min.js"></script>
    <script type="text/javascript" src="sorteo/operador/funciones_anticipada.js"></script>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <?php
$_SESSION['juego']        = 'LOTERIA';
$_SESSION['tipo_usuario'] = 'ROL_JEFE_SORTEO';
if ($_SESSION['tipo_usuario'] != 'ROL_JEFE_SORTEO' && $_SESSION['tipo_usuario'] != 'ROL_OPERADOR') {
    die(' <div id="error" class="alert alert-error"><button type="button" class="close" onclick="$(\'.alert\').slideUp(\'slow\');">x</button><div class="contenido_error">Para poder acceder al sorteo es necesario ser JEFE DE SORTEO u OPERADOR</div></div>');
}

?>
        <script type="text/javascript">
        param = {
            accion: 'mostrar',
            juego: 'incentivo'
        };

        var param;
        var intervalo2;
        var intervalo1;
        var Ajax1;
        var Ajax2;

        function cambiar_juego(classN,orden) {
            $('.alert').hide('slow');

            if (classN == 'anticipada') {
                accion = 'sorteo/operador/loteria_sorteador_anticipada.php';
                param = {
                    accion: 'mostrar',
                    juego: 'anticipada'
                };
            } else if (classN == 'ver_semana_1') {
                accion = 'sorteo/operador/loteria_anticipada_ajax.php';
                param2 = {
                    accion: 'mostrar_extracto',
                    tipo: 'semana_1',
                    orden: orden
                };
            } else if (classN == 'ver_semana_2') {
                accion = 'sorteo/operador/loteria_anticipada_ajax.php';
                param2 = {
                    accion: 'mostrar_extracto',
                    tipo: 'semana_2',
                    orden: orden
                };
            } else if (classN == 'ver_semana_3') {
                accion = 'sorteo/operador/loteria_anticipada_ajax.php';
                param2 = {
                    accion: 'mostrar_extracto',
                    tipo: 'semana_3',
                    orden: orden
                };
            } else if (classN == 'ver_semana_4') {
                accion = 'sorteo/operador/loteria_anticipada_ajax.php';
                param2 = {
                    accion: 'mostrar_extracto',
                    tipo: 'semana_4',
                    orden: orden
                };
            } else if (classN == 'ver_semana_5') {
                accion = 'sorteo/operador/loteria_anticipada_ajax.php';
                param2 = {
                    accion: 'mostrar_extracto',
                    tipo: 'semana_5',
                    orden: orden
                };
            } else if (classN == 'ver_semana_6') {
                accion = 'sorteo/operador/loteria_anticipada_ajax.php';
                param2 = {
                    accion: 'mostrar_extracto',
                    tipo: 'semana_6',
                    orden: orden
                };
            } else if (classN == 'ver_semana_7') {
                accion = 'sorteo/operador/loteria_anticipada_ajax.php';
                param2 = {
                    accion: 'mostrar_extracto',
                    tipo: 'semana_7',
                    orden: orden
                };
            }else if (classN == 'ver_semana_8') {
                accion = 'sorteo/operador/loteria_anticipada_ajax.php';
                param2 = {
                    accion: 'mostrar_extracto',
                    tipo: 'semana_8',
                    orden: orden
                };
            }else if (classN == 'ver_semana_9') {
                accion = 'sorteo/operador/loteria_anticipada_ajax.php';
                param2 = {
                    accion: 'mostrar_extracto',
                    tipo: 'semana_9',
                    orden: orden
                };
            }else if (classN == 'ver_semana_10') {
                accion = 'sorteo/operador/loteria_anticipada_ajax.php';
                param2 = {
                    accion: 'mostrar_extracto',
                    tipo: 'semana_10',
                    orden: orden
                };
            }else if (classN == 'ver_semana_11') {
                accion = 'sorteo/operador/loteria_anticipada_ajax.php';
                param2 = {
                    accion: 'mostrar_extracto',
                    tipo: 'semana_11',
                    orden: orden
                };
            }else if (classN == 'ver_semana_12') {
                accion = 'sorteo/operador/loteria_anticipada_ajax.php';
                param2 = {
                    accion: 'mostrar_extracto',
                    tipo: 'semana_12',
                    orden: orden
                };
            }

            if (typeof param2 === 'object') {
                $.post(accion, param2).done(
                    function(data) {
                        if (data.tipo == 'info') {
                            $("#warning_juego.alert").slideDown("slow");
                            $('#warning_juego > .contenido_error').html(data.mensaje);
                        }
                        if (data.tipo == 'error') {
                            $("#warning_juego.alert").slideDown("slow");
                            $('#warning_juego > .contenido_error').html(data.mensaje);
                        }
                        delete param2;
                    });

            } else{
                cargar_juego(accion, param);
            }

            $("ul.juegos > li.active").removeClass('active');
            $('.' + classN).parent().addClass('active');
        }

        function mostrar_resumen(parametro){
            $.post('sorteo/operador/loteria_anticipada_ajax.php', {accion:'mostrar_resumen',semana:parametro}).done(
                function(data) {
                   if (data.tipo == 'info') {
                            $("#warning_juego.alert").slideDown("slow");
                            $('#warning_juego > .contenido_error').html(data.mensaje);
                        }
                        if (data.tipo == 'error') {
                            $("#warning_juego.alert").slideDown("slow");
                            $('#warning_juego > .contenido_error').html(data.mensaje);
                        }
                }
            );
        }

        function cargar_juego(accion, parametros) {
            $.post(accion, parametros).done(
                function(data) {
                        $('#juego').html(data);
                }
            );
        };



        var buscarGanadores1 = function buscarGanadores() {
            param = {
                accion: 'mostrar',
                juego: 'anticipada'
            };
            $.post('sorteo/operador/loteria_listado_ganadores.php?buscarGanadores=' + parseInt(Math.random() * 1000000000), param).done(
                function(data) {
                    $('#ganadores').html(data);
                }
            );
        }


        var controlGanadores2 = function controlGanadores() {
            param = {
                accion: 'control_ganador',
                juego: 'incentivo'
            };

            Ajax2 = $.post('sorteo/operador/loteria_anticipada_ajax.php?controlGanador=1', param).done(
                function(Ajax2) {
                    for (var i = 0; i < Ajax2.length; i++) {
                        if (Ajax2[i].sorteado > 0) {
                            deshabilitarIncentivo(Ajax2[i].idIncentivo);
                            //console.log('DESHABILITAR');
                        } else {
                            habilitarIncentivo(Ajax2[i].idIncentivo);
                            //console.log('HABILITAR');
                        }
                    }
                }
            );
        }

        $(document).ready(
            function() {
                cambiar_juego("anticipada");
                buscarGanadores1();
                controlGanadores2();
                setInterval(buscarGanadores1, 2000);
                setInterval(controlGanadores2, 2000);
            });
</script>
</head>

<body>
    <div id="contenedor_general" class="container">
        <h3 class="titulo"><img width="40" border="0" src="img/logo_loteria_peque.png"><?php echo $_SESSION['juego'] ?> Sorteo Compra Anticipada <?php echo $_SESSION['sorteo'] ?> <span style="font-size:11px"><i class="icon-user"></i><?php echo $_SESSION['nombre_usuario'] . ' (' . $_SESSION['tipo_usuario'] . ')'; ?></span></h3>
        <div class="bar" style="width: 40%"></div>
<!--         <div>
            <div class="row-fluid show-grid">
                <div class="navbar">
                    <div class="navbar-inner">
                        <ul class="nav juegos">
                            <li></li>
                            <li></li>
                            <li></li>
                            <li></li>
                            <li class="divider-vertical"></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div> -->
        <div id="juego"></div>
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
