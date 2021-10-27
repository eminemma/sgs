<?php
session_start();

$protocolo = isset($_SERVER['HTTPS']) ? 'https' : 'http';

if ($_SERVER['SERVER_NAME'] == 'localhost' || $_SERVER['SERVER_NAME'] == '127.0.0.1' || $_SERVER['SERVER_NAME'] == 'desa.local')
//$url = $protocolo . '://' . $_SERVER['SERVER_NAME'] . '/desa';
{
    $url = $protocolo . '://' . $_SERVER['SERVER_NAME'] . '/desa';
} else if ($_SERVER['SERVER_NAME'] == 'desa.loteriadecordoba.com.ar' || $_SERVER['SERVER_NAME'] == 'svn.loteriadecordoba.com.ar') {
    $url = $protocolo . '://' . $_SERVER['SERVER_NAME'] . '/';
} else {
    $url = $protocolo . '://' . $_SERVER['SERVER_NAME'] . '/app';
}

//echo "url:$url:";
//var_dump($_SESSION);die;

?>
<!DOCTYPE html>
<html>

<head>
    <meta http-equiv="Content-Language" content="es" />
    <meta name="distribution" content="global" />
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <base href="<?php echo $url; ?>/sgs/">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">


    <link rel="stylesheet" type="text/css" href="librerias/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="librerias/bootstrap/css/bootstrap-responsive.min.css">
    <link rel="stylesheet" type="text/css" href="librerias/bootstrap/css/bootstrap-datetimepicker.min.css">
    <link rel="stylesheet" type="text/css" href="librerias/bootstrap/css/bootstrap-fileupload.min.css">
    <link rel="stylesheet" type="text/css" href="css/estilo.css">


    <script type="text/javascript" src="librerias/jquery/jquery-1.10.1.js"></script>
    <script type="text/javascript" src="librerias/bootstrap/js/bootstrap.min.js"></script>
    <script type="text/javascript" src="librerias/bootstrap/js/bootstrap-datetimepicker.min.js"></script>
    <script type="text/javascript" src="librerias/bootstrap/js/bootstrap-fileupload.min.js"></script>
    <!--<script type="text/javascript" src="librerias/bootstrap/js/locales/bootstrap-datepicker.es.js"></script>	-->


    <script type="text/javascript" src="js/funciones.js"></script>
    <script type="text/javascript" src="librerias/modernizr/modernizr.js"></script>
    <link rel="stylesheet" href="librerias/font-awesome/css/font-awesome.min.css">

    <script type="text/javascript" src="js/loadingoverlay.min.js"></script>


    <title>SGS - Sistema de Gestion de Sorteos </title>
    <style type="text/css">
        .show-grid {
            margin-top: 10px;
            margin-bottom: 20px;
        }

        #contenedor_general {
            margin-top: 10px;
        }

        .page-header {
            padding-bottom: 6px;
            margin: 5px 0 10px;
            border-bottom: 1px solid #eee;
        }

        @media (min-width:600px) {
            #eventos {
                font-size: 16px;
            }
        }

        @media (min-width:800px) {
            #eventos {
                font-size: 20px;
            }
        }

        @media (min-width:1000px) {
            #eventos {
                /* Never get larger than this */
                font-size: 25px;
            }
        }
    </style>
</head>

<body>

    <div id="contenedor_general">

        <div id="panelJefe" class="container well" style="display: none;">
            <div id="mensaje"></div>
            <div class="page-header">
                <h2>Panel de Control de Vivo y Extracto Quiniela Cba</h2>
                <h3><?php echo $_SESSION['juego'] ?></h3>
                <h3><?php echo $_SESSION['sorteo'] ?></h3>
            </div>
            <!--<div class="row show-grid">
                <div class="span12">
                    <div class="btn-group" style="width: 100%">
                        <button id="demorado" class="btn btn-large btn-danger" disabled style="width: 100%" data-toggle="modal" data-target="#demoradoSorteo">Sorteo Demorado</button>
                    </div>
                </div>
            </div>-->

            <div class="row show-grid">
                <div class="span12">
                    <div class="btn-group" style="width: 100%">
                        <button id="iniciar" class="btn btn-large btn-success"  style="width: 100%" data-toggle="modal" data-target="#iniciarSorteo">Iniciar transmisión</button>
                    </div>
                </div>
            </div>
            <!--<div class="row show-grid">
                <div class="span12">
                    <div class="btn-group" style="width: 100%">
                        <button id="detener" class="btn btn-large btn-danger" disabled style="width: 100%" data-toggle="modal" data-target="#detenerSorteo">Detener Sorteo</button>
                    </div>
                </div>
            </div>-->
            <div class="row show-grid">
                <div class="span12">
                    <div class="btn-group" style="width: 100%">
                        <button id="finalizar" class="btn btn-large btn-primary"  style="width: 100%" data-toggle="modal" data-target="#finalizarSorteo">Finalizar transmisión</button>

                    </div>
                </div>
            </div>
        </div>
        <div class="container well">
            <div class="page-header">
                <h3>Notificaciones</h3>
                <h3><?php echo $_SESSION['juego'] ?></h3>
                <h3><?php echo $_SESSION['sorteo'] ?></h3>
            </div>
            <div id="eventos" class="well well-small"></div>
        </div>
    </div>
    <!-- Modal Iniciar Sorteo -->
    <div id="demoradoSorteo" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
            <h3 id="myModalLabel">Confirmacion</h3>
        </div>
        <div class="modal-body">
            ¿Esta seguro que el sorteo se encuentra demorado?
        </div>
        <div class="modal-footer">
            <button class="btn" data-dismiss="modal" aria-hidden="true">Cancelar</button>
            <button class="btn btn-primary" id="finDemorar">Si estoy seguro</button>
        </div>
    </div>
    <!-- Modal Iniciar Sorteo -->
    <div id="iniciarSorteo" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
            <h3 id="myModalLabel">Confirmacion</h3>
        </div>
        <div class="modal-body">
            ¿Esta seguro de iniciar de la transmisión?
        </div>
        <div class="modal-footer">
            <button class="btn" data-dismiss="modal" aria-hidden="true">Cancelar</button>
            <button class="btn btn-primary" id="finIniciar">Si estoy seguro</button>
        </div>
    </div>
    <!-- Modal Finalizar Sorteo -->
    <div id="finalizarSorteo" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
            <h3 id="myModalLabel">Confirmacion</h3>
        </div>
        <div class="modal-body">
            ¿Esta seguro de finalizar el sorteo? Al accionar esta opción no tendra vuelta atras.
        </div>
        <div class="modal-footer">
            <button class="btn" data-dismiss="modal" aria-hidden="true">Cancelar</button>
            <button class="btn btn-primary" id="finFinalizar">Si estoy seguro</button>
        </div>
    </div>
    <!-- Modal Detener Sorteo -->
    <div id="detenerSorteo" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
            <h3 id="myModalLabel">Detener Sorteo</h3>
        </div>
        <div class="modal-body">
            <form id="formDetener" class="form-horizontal">
                <div class="control-group">
                    <label class="control-label">Situacion</label>
                    <div class="controls">
                        <select id="situacion" name="situacion">
                            <option value="D">Detenido</option>
                            <option value="C">Cancelado</option>
                            <option value="S">Suspendido</option>
                        </select>
                    </div>
                </div>
                <div class="control-group">
                    <label class="control-label">Descripcion</label>
                    <div class="controls">
                        <textarea id="descripcion" name="descripcion" rows="3"></textarea>
                    </div>
                </div>
            </form>
        </div>
        <div class="modal-footer">
            <button class="btn" data-dismiss="modal" aria-hidden="true">Cancelar</button>
            <button class="btn btn-primary" id="finDetener">Confirmar</button>
        </div>
    </div>
    <script type="text/javascript">
        $(function() {
            $.LoadingOverlaySetup({
                zIndex: 99999
            });
            $.when(esJefeSorteo()).done(function(data) {
                if (data.esJefe == true) {
                    $('#panelJefe').show();
                    situacionActual();
                } else {
                    $('#mensaje').html('<div class="alert alert-error">Solo puede acceder el jefe de sorteos</div>');
                }

                setInterval(function() {
                    leerEventos();
                }, 2000);
            });



        });

        $("#finDemorar").bind("click", function() {
            sorteoDemorado();

        });

        $("#finIniciar").bind("click", function() {
            iniciarSorteo();

        });

        $("#finDetener").bind("click", function() {
            detenerSorteo();
        });

        $("#finFinalizar").bind("click", function() {
            finalizarSorteo();
        });

        function situacionActual() {
            $.LoadingOverlay("show");
            $.get('stream/ajax.php?accion=situacion_actual', {},
                function(data) {
                    if (data.tipo == 'error') {
                        $('#mensaje').html(data.mensaje);
                    } else if (data.tipo == 'success') {
                        if (data.situacion == 'I') {

                            $('#demorado').prop('disabled', true);
                            $('#iniciar').prop('disabled', true);
                            $('#finalizar').prop('disabled', false);
                           // $('#detener').prop('disabled', false);
                        } else if (data.situacion == 'C' || data.situacion == 'S' || data.situacion == 'D') {
                            $('#iniciar').text('Reiniciar Sorteo');
                            $('#iniciarSorteo > .modal-body').text('¿Esta seguro de reiniciar el sorteo?');
                            $('#demorado').prop('disabled', true);
                            $('#detener').prop('disabled', true);
                            $('#iniciar').prop('disabled', false);
                            //$('#finalizar').prop('disabled', true);
                        } else if (data.situacion == 'F') {
                            $('#demorado').prop('disabled', true);
                            $('#detener').prop('disabled', true);
                            $('#iniciar').prop('disabled', false);
                            $('#finalizar').prop('disabled', true);
                        } else if (data.situacion == null) {
                            //$('#demorado').prop('disabled', false);
                            $('#iniciar').prop('disabled', false);
                            //$('#finalizar').prop('disabled', true);
                            $('#detener').prop('disabled', true);
                        } else if (data.situacion == 'P') {
                            $('#demorado').prop('disabled', false);
                            $('#iniciar').prop('disabled', false);
                        }
                    }
                    leerEventos();
                }
            ).complete(
                function() {
                    $.LoadingOverlay("hide");
                }
            );
        }

        function sorteoDemorado() {
            $.LoadingOverlay("show");
            $.get('stream/ajax.php?accion=sorteo_demorado', {},
                function(data) {
                    if (data.tipo == 'error') {
                        $('#mensaje').html(data.mensaje);
                    } else if (data.tipo == 'success') {
                        $('#demoradoSorteo').modal('hide');
                    }
                    leerEventos();
                }
            ).complete(
                function() {
                    $.LoadingOverlay("hide");
                }
            );
        }

        function iniciarSorteo() {
            $.LoadingOverlay("show");
            $.get('stream/ajax.php?accion=iniciar_sorteo', {},
                function(data) {
                    if (data.tipo == 'error') {
                        $('#mensaje').html(data.mensaje);
                    } else if (data.tipo == 'success') {
                        //$('#demorado').prop('disabled', true);
                        $('#iniciar').prop('disabled', true);
                        $('#finalizar').prop('disabled', false);
                        //$('#detener').prop('disabled', false);
                        $('#iniciarSorteo').modal('hide');
                    }
                    leerEventos();
                }
            ).complete(
                function() {
                    $.LoadingOverlay("hide");
                }
            );
            /**/
        }

        function detenerSorteo() {
            $.LoadingOverlay("show");
            $.post('stream/ajax.php?accion=detener_sorteo', $('#formDetener').serialize(),
                function(data) {
                    if (data.tipo == 'error') {
                        $('#mensaje').html(data.mensaje);
                    } else if (data.tipo == 'success') {
                        $('#iniciar').text('Reiniciar Sorteo');
                        $('#iniciarSorteo > .modal-body').text('¿Esta seguro de reiniciar el sorteo?');
                        $('#detener').prop('disabled', true);
                        $('#iniciar').prop('disabled', false);
                        $('#finalizar').prop('disabled', true);
                        $('#detenerSorteo').modal('hide');
                    }
                    leerEventos();
                }
            ).complete(
                function() {
                    $.LoadingOverlay("hide");
                }
            );

        }

        function finalizarSorteo() {
            $.LoadingOverlay("show");
            $.get('stream/ajax.php?accion=finalizar_sorteo', {},
                function(data) {
                    if (data.tipo == 'error') {
                        $('#mensaje').html(data.mensaje);
                    } else if (data.tipo == 'success') {
                        //$('#detener').prop('disabled', true);
                        $('#iniciar').prop('disabled', false);
                        $('#finalizar').prop('disabled', true);
                        $('#finalizarSorteo').modal('hide');
                    }
                    leerEventos();
                }
            ).complete(
                function() {
                    $.LoadingOverlay("hide");
                }
            );

        }

        function leerEventos() {
            // $("#eventos").LoadingOverlay("show");
            $.get('stream/ajax.php?accion=listar_eventos', {},
                function(data) {
                    $('#eventos').html(data);
                }
            ).complete(
                function() {
                    //$("#eventos").LoadingOverlay("hide");
                }
            );
        }

        function esJefeSorteo() {
            return $.get('stream/ajax.php?accion=es_jefe_sorteo');
        }
    </script>

</body>

</html>