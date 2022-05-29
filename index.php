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
        <meta http-equiv="Content-Language" content="es"/>
        <meta name="distribution" content="global"/>
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
        <!--<script type="text/javascript" src="librerias/bootstrap/js/locales/bootstrap-datepicker.es.js"></script>    -->


        <script type="text/javascript" src="js/funciones.js"></script>
        <script type="text/javascript" src="librerias/modernizr/modernizr.js"></script>
         <script type="text/javascript" src="js/loadingoverlay.js"></script>
        <link rel="stylesheet" href="librerias/font-awesome/css/font-awesome.min.css">


        <title>SGS - Sistema de Gestion de Sorteos </title>
        <style type="text/css">
            #menu_acciones{
                margin-top: 53px;
                margin-top: 53px;
                background-color: #dedddd;
                padding: 10px;
            }
        </style>
    </head>

    <body>
        <div class="navbar navbar-inverse navbar-fixed-top">
            <div class="navbar-inner">
                <div class="container">
                    <button data-target=".nav-collapse" data-toggle="collapse" class="btn btn-navbar" type="button">
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                    </button>
              <!--       <a href="#" class="brand">SGS</a>
              -->       <div class="nav-collapse collapse">
                        <ul class="nav">
                            <li class="dropdown">
                                <a data-toggle="dropdown" class="dropdown-toggle" href="#"><i class="icon-home icon-white"></i> Administración <b class="caret"></b></a>
                                <ul class="dropdown-menu">
                                    <li><a href="#" onclick="$.get('juego/ajax.php?accion=obtener_juego', function(data) {
                                                if (data.id_juego == '1')
                                                    g('administracion/administrar_sorteos/loteria_administrar_sorteos.php');
                                                else if (data.id_juego == '2')
                                                    g('administracion/administrar_sorteos/quiniela_administrar_sorteos.php');
                                                else if (data.id_juego == '3')
                                                    g('administracion/administrar_incentivos/loteria_administrar_incentivos.php');
                                                else if (data.id_juego == '32')
                                                    g('administracion/administrar_sorteos/quiniela_poceada_administrar_sorteos.php');
                                                else if (data.id_juego !== 'null')
                                                    alert('Este juego no esta contemplado para esta opción');
                                                else
                                                    alert('Es necesario seleccionar un Juego');
                                            });">Administrar Sorteos</a></li>
                                    <li><a href="#" onclick="$.get('juego/ajax.php?accion=obtener_juego', function(data) {
                                         if (data.id_juego == '1')
                                             g('administracion/administrar_incentivos/loteria_administrar_incentivos.php');
                                         else if (data.id_juego !== 'null')
                                            alert('Este juego no esta contemplado para esta opción');
                                         else
                                            alert('Es necesario seleccionar un Juego');
                                        });
                                        ">Administrar Incentivos</a></li>
                                    <li><a href="#" onclick="g('parametros_generales.php');">Parametros Generales</a></li>
                                    <li><a href="#" onclick="g('version.php');">Auditoria</a></li>
                                    <li><a href="#" onclick="g('version_alta.php');">Version</a></li>
                                    <li class="divider"></li>
                                    <li class="nav-header">Panel Vivo</li>
                                    <li><a href="stream/panel.php" target="_blank">Vivo y Extracto Quiniela Cba</a></li>
                                    <!-- <li><a href="#" onclick="g('administracion/administrar_anticipada/loteria_administrar_anticipada.php');">Administrar Anticipada</a></li> -->
                                </ul>
                            </li>


                            <li class="dropdown">
                                <a data-toggle="dropdown" class="dropdown-toggle" href="#"><i class="icon-briefcase icon-white"></i> Sorteo <b class="caret"></b></a>
                                <ul class="dropdown-menu">
                                    <li><a href="#" onclick="$.get('juego/ajax.php?accion=obtener_juego', function(data) {
                                                if (data.id_juego == '1' && data.tipo_juego == 'EXTRAORDINARIA')
                                                    p('sorteo/escribano/escribano.php');
                                                else if (data.id_juego == '2')
                                                    p('sorteo/escribano/quiniela/escribano.php');
                                                else if (data.id_juego == '1' && data.tipo_juego == 'ORDINARIA') {
                                                    p('sorteo/escribano/loteria_ordinaria_escribano.php');
                                                }
                                                else if (data.id_juego == '32')
                                                    p('sorteo/escribano/quiniela_poceada/escribano.php');
                                                else if (data.id_juego !== 'null')
                                                    alert('Este juego no esta contemplado para esta opción');
                                                else
                                                    alert('Es necesario seleccionar un Juego');
                                            });">Escribano</a></li>
                                    <li class="divider"></li>
                                    <li><a href="#" onclick="$.get('juego/ajax.php?accion=obtener_juego', function(data) {
                                                if (data.id_juego == '1')
                                                    p('sorteo/operador/loteria_sorteador.php');
                                                else if (data.id_juego == '2')
                                                    p('sorteo/operador/quiniela/quiniela_sorteador.php');
                                                else if (data.id_juego == '32' && localStorage.getItem('windows') == 0)
                                                    p('sorteo/operador/quiniela_poceada/quiniela_poceada_sorteador.php');
                                                else if (data.id_juego == '32' && localStorage.getItem('windows') == 1)
                                                     alert('La ventana del operador ya se encuentra abierta');
                                                else if (data.id_juego !== 'null')
                                                    alert('Este juego no esta contemplado para esta opción');
                                                else
                                                    alert('Es necesario seleccionar un Juego');
                                            });">Operador</a></li>
                                    <li class="divider"></li>
                                    <li><a href="#" onclick="if (confirm('¿Desea reiniciar por completo el sorteo <?php echo $_SESSION['sorteo']; ?>?'))
                                                g('sorteo/loteria_reiniciar_sorteo.php');">Reiniciar Sorteo</a></li>
                                    <li><a href="#" onclick="if (confirm('¿Desea reiniciar por completo el sorteo <?php echo $_SESSION['sorteo']; ?>?'))
                                                g('sorteo/loteria_reiniciar_sorteo.php?reiniciar_entero=1');">Reiniciar Sorteo Entero</a></li>
                                </ul>
                            </li>


                            <li class="dropdown">
                                <a data-toggle="dropdown" class="dropdown-toggle" href="#"><i class="icon-briefcase icon-white"></i> Anticipada <b class="caret"></b></a>
                                <ul class="dropdown-menu">
                                    <li><a href="#" onclick="
                                    $.get('juego/ajax.php?accion=obtener_juego', function(data) {
                                    if (data.id_juego == '1')
                                       p('sorteo/escribano/escribano_anticipada_electronica.php');
                                    else if (data.id_juego !== 'null')
                                        alert('Este juego no esta contemplado para esta opción');
                                    else
                                        alert('Es necesario seleccionar un Juego');
                                    });">Escribano</a></li>
                                    <li class="divider"></li>
                                    <li><a href="#" onclick="$.get('juego/ajax.php?accion=obtener_juego', function(data) {
                                                if (data.id_juego == '1')
                                                    p('sorteo/operador/loteria_anticipada.php');
                                                else if (data.id_juego !== 'null')
                                                    alert('Este juego no esta contemplado para esta opción');
                                                else
                                                    alert('Es necesario seleccionar un Juego');
                                            });">Operador</a></li>
                                    <li class="divider"></li>
                                    <!-- <li><a href="#" onclick="g('datos/exportar/loteria_exportar_anticipada.php');">Exportar ganadores a kanban</a></li> -->
                                </ul>
                            </li>



                            <li class="dropdown">
                                <a data-toggle="dropdown" class="dropdown-toggle" href="#"><i class="icon-briefcase icon-white"></i> Incentivo <b class="caret"></b></a>
                                <ul class="dropdown-menu">
                                    <li><a href="#" onclick="$.get('juego/ajax.php?accion=obtener_juego', function(data) {
                                                if (data.id_juego == '1')
                                                   p('sorteo/escribano/escribano_incentivo.php');
                                                else if (data.id_juego !== 'null')
                                                    alert('Este juego no esta contemplado para esta opción');
                                                else
                                                    alert('Es necesario seleccionar un Juego');
                                            });"
                                        >Escribano</a></li>
                                    <li class="divider"></li>
                                    <li><a href="#" onclick="$.get('juego/ajax.php?accion=obtener_juego', function(data) {
                                                if (data.id_juego == '1')
                                                   p('sorteo/operador/loteria_incentivo.php');
                                                else if (data.id_juego !== 'null')
                                                    alert('Este juego no esta contemplado para esta opción');
                                                else
                                                    alert('Es necesario seleccionar un Juego');
                                            });"
                                        >Operador</a></li>
                                    <li class="divider"></li>
                                    <li class="nav-header">Datos</li>
                                    <li><a href="#" onclick="if (confirm('¿Desea mezclar los rangos asignados al Incentivo del sorteo <?php echo $_SESSION['sorteo']; ?>?'))
                                                g('sorteo/loteria_mezclar_incentivo.php');">Mezclar Incentivo</a></li>
                                    <li><a href="#" onclick="p('sorteo/acta/loteria_incentivo_rangos.php');">Imprimir Rangos</a></li>
                                </ul>
                            </li>
                            <li class="dropdown">
                                <a data-toggle="dropdown" class="dropdown-toggle" href="#"><i class="icon-briefcase icon-white"></i> Actas Loteria <b class="caret"></b></a>
                                <ul class="dropdown-menu">
                                    <li class="divider"></li>
                                    <li class="nav-header">Actas Generales</li>
                                    <li><a href="#" onclick="p('sorteo/acta/loteria_acta_extractos.php');">Acta Sorteo Extractos</a></li>
                                    <li><a href="#" onclick="p('sorteo/acta/loteria_acta_primer_premio.php');">Acta Primer Premio Extracto (Distribucion)</a></li>
                                    <li><a href="#" onclick="p('sorteo/acta/loteria_acta_primer_premio_solo.php');">Acta Primer Premio Extracto</a></li>
                                    <li><a href="#" onclick="p('sorteo/acta/loteria_acta_premios_extraordinarios.php');">Acta Premios Extraordinarios</a></li>
                                    <li><a href="#" onclick="p('sorteo/acta/loteria_acta_premios_extraordinarios_ext.php');">Acta Premios Extraordinarios extendido</a></li>


                                    <li><a href="#" onclick="p('sorteo/acta/loteria_acta_cinco_primeros_premios.php');">Acta Cinco Primeras Extracciones</a></li>
                                    <li><a href="#" onclick="p('sorteo/acta/loteria_acta_final_zonas.php');">Acta Final Extracciones</a></li>
                                    <li class="divider"></li>
                                    <li class="nav-header">Actas Sorteo Especial</li>
                                    <li><a href="#" onclick="p('sorteo/acta/loteria_acta_primer_especial_entero.php');">Acta Premio Especial Entero</a></li>
                                    <li><a href="#" onclick="p('sorteo/acta/loteria_informe_enteros_participantes.php');">Billetes Participantes del Sorteo de Entero</a></li>
                                    <li class="divider"></li>


                                    <li class="nav-header">Actas Sale o Sale</li>
                                    <li><a href="#" onclick="p('sorteo/acta/loteria_acta_sale_o_sale.php');">Acta Extracciones Sale o Sale</a></li>
                                    <li><a href="#" onclick="p('sorteo/acta/loteria_acta_primer_premio_sale_sale.php');">Acta Primer Premio Sale o Sale</a></li>
                                    <li><a href="#" onclick="p('sorteo/acta/loteria_acta_primer_premio_sale_sale_s_det.php');">Acta Primer Premio Sale o Sale s/ Detalle</a></li>
                                    <li class="divider"></li>


                                    <li class="nav-header">Actas Incentivo</li>
                                    <li><a href="#" onclick="p('sorteo/acta/loteria_acta_incentivo_final.php');">Acta Final Incentivos</a></li>
                                    <li class="divider"></li>
                                </ul>

                            </li>
                            <li class="dropdown">
                                <a data-toggle="dropdown" class="dropdown-toggle" href="#"><i class="icon-briefcase icon-white"></i> Actas Quiniela <b class="caret"></b></a>
                                <ul class="dropdown-menu">
                                    <li class="divider"></li>
                                    <li class="nav-header">Actas Generales</li>
                                    <li><a href="#" onclick="p('sorteo/acta/quiniela_acta_extracto.php');">Acta Sorteo</a></li>
                                    <li><a href="#" onclick="p('sorteo/acta/quiniela_acta_final_zonas.php');">Acta Extracciones</a></li>
                                    <li class="divider"></li>
                                </ul>

                            </li>

                            <li class="dropdown">
                                <a data-toggle="dropdown" class="dropdown-toggle" href="#"><i class="icon-briefcase icon-white"></i> Actas Poceada<b class="caret"></b></a>
                                <ul class="dropdown-menu">
                                    <li class="divider"></li>
                                    <li class="nav-header">Actas Generales</li>
                                    <li><a href="#" onclick="p('sorteo/acta/quiniela_poceada_acta_extracto.php');">Acta Sorteo</a></li>
                                    <li><a href="#" onclick="p('sorteo/acta/quiniela_poceada_acta_final.php');">Acta Extracciones</a></li>
                                    <li><a href="#" onclick="p('sorteo/acta/quiniela_poceada_acta_contralor.php?reportePDF=1');">Informe Contralor</a></li>
                                    <li class="divider"></li>
                                </ul>

                            </li>
                            <li class="dropdown">
                                <a data-toggle="dropdown" class="dropdown-toggle" href="#"><i class="icon-briefcase icon-white"></i> Datos <b class="caret"></b></a>
                                <ul class="dropdown-menu">
                                    <li><a href="#" onclick="$.get('juego/ajax.php?accion=obtener_juego', function(data) {
                                                if (data.id_juego == 1)
                                                    g('datos/importar/loteria_importar_datos.php');
                                                else if (data.id_juego == 2)
                                                    g('datos/importar/quiniela_importar_datos.php');
                                                else if (data.id_juego == 32)
                                                    g('datos/importar/quiniela_poceada_importar_datos.php');
                                                else if (data.id_juego !== 'null')
                                                    alert('Este juego no esta contemplado para esta opción');
                                                else
                                                    alert('Es necesario seleccionar un Juego');
                                            })">Importar</a></li>
                                    <li><a href="#" onclick="$.get('juego/ajax.php?accion=obtener_juego', function(data) {
                                                if (data.id_juego == 1)
                                                    p('datos/importar/loteria_billetes_importados.php');
                                                else if (data.id_juego == 2)
                                                    p('datos/importar/loteria_billetes_importados.php');
                                                else if (data.id_juego == 32)
                                                    p('datos/importar/quiniela_poceada_importados.php');
                                                else if (data.id_juego !== 'null')
                                                    alert('Este juego no esta contemplado para esta opción');
                                                else
                                                    alert('Es necesario seleccionar un Juego');
                                            })">Datos Importados</a></li>
                                    <li><a href="#" onclick="$.get('juego/ajax.php?accion=obtener_juego', function(data) {
                                                if (data.id_juego == 1)
                                                    if (confirm('¿Desea generar archivo csv del sorteo <?php echo $_SESSION['sorteo']; ?>?'))
                                                    p('datos/importar/loteria_anticiapda_datos.php');
                                            })">Importados en formato CSV</a></li>
                                    <li><a href="#" onclick="
                                    $.get('juego/ajax.php?accion=obtener_juego', function(data) {
                                        let id_juego = parseInt(data.id_juego);
                                        if (id_juego == 1 || data.id_juego == 2)
                                            g('datos/exportar/loteria_exportar_datos.php');
                                        else if (data.id_juego == 32)
                                            g('datos/exportar/quiniela_poceada_exportar_datos.php');
                                        else if (data.id_juego !== 'null')
                                           alert('Este juego no esta contemplado para esta opción');
                                        else
                                            alert('Es necesario seleccionar un Juego');
                                        })

                                    ">Exportar</a></li>

                                    <li><a href="#" onclick="
                                    $.get('juego/ajax.php?accion=obtener_juego', function(data) {
                                        if (data.id_juego == 1)
                                            g('datos/exportar/loteria_exportar_anticipada.php');
                                        else if (data.id_juego !== 'null')
                                           alert('Este juego no esta contemplado para esta opción');
                                        else
                                            alert('Es necesario seleccionar un Juego');
                                        })
                                    ">Exportar Anticipados</a></li>
                                </ul>
                            </li>



                        </ul>

                    </div>
                </div>
            </div>
        </div>
        <div class="container" id="menu_acciones">
            <form class="navbar-form pull-left">
                <h3 class="label label-info" style="font-size: 18px;"><div id="sorteo_s">Seleccionar Sorteo</div></h3>
            </form>
            <form class="navbar-form pull-right">

                            <span id="cantidad_registros_ultima_consulta" class="badge badge-info">0</span> <span class="label label-info">registros</span>
                            <a id="recargar" class="btn btn-small" href="#" onclick="recargar();"><i class="icon-refresh"></i> Recargar</a>
                            <button <?php echo (int) $_SESSION['dni'] > 0 ? '' : 'style="display:none;"'; ?> id="cambiar_juego_sorteo" class="btn" onclick="g('sesion/cambiar_juego_sorteo/cambiar_juego_sorteo.php');
                                            return false;">Cambiar Juego/Sorteo</button>
                            <button <?php echo (int) $_SESSION['dni'] > 0 ? '' : 'style="display:none;"'; ?> id="cerrar_sesion" class="btn" onclick="g('sesion/cerrar_sesion/cerrar_sesion_ajax.php' + a('cerrar_sesion'));
                                            return false;">Cerrar Sesión</button>
                            <button <?php echo (int) $_SESSION['dni'] == 0 ? '' : 'style="display:none;"'; ?> id="iniciar_sesion" class="btn" onclick="g('sesion/iniciar_sesion/iniciar_sesion.php');
                                            return false;">Iniciar Sesión</button>
                        </form>
        </div>
        <div id="contenedor_general" class="container"></div>

        <script type="text/javascript">
                                        var juego = null;
                                        var sorteo = null;

                                        $(document).ready(
                                                function() {
                                                    g('sesion/iniciar_sesion/iniciar_sesion.php');
                                                }
                                        );

                                        $.LoadingOverlaySetup({
                                                image       : "",
                                                fontawesome : "fa fa-cog fa-spin",
                                                size                    : 5
                                        });

                                        $(document).ajaxStart(function(){
                                            $.LoadingOverlay("show");
                                        });
                                        $(document).ajaxStop(function(){
                                            $.LoadingOverlay("hide");
                                        });
        </script>

    </body>
</html>