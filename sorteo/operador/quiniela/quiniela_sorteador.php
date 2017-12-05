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
    $url = $protocolo . '://' . $_SERVER['SERVER_NAME'] . '/app/';
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
    <link rel="stylesheet" type="text/css" href="sorteo/operador/quiniela/estilo_sorteador.css">
    <script type="text/javascript" src="librerias/jquery/jquery-1.10.1.js"></script>
    <script type="text/javascript" src="librerias/bootstrap/js/bootstrap.min.js"></script>
    <script type="text/javascript" src="librerias/bootstrap/js/bootstrap-fileupload.min.js"></script>
    <script type="text/javascript" src="librerias/bootstrap/js/bootstrap-datetimepicker.min.js"></script>

    <script type="text/javascript" src="sorteo/operador/quiniela/funciones.js"></script>
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
      param={
              accion:'mostrar',
              juego:'primer_juego'
            };
      var param;
      var intervalo2;
      var intervalo1;
      var Ajax1;
      var Ajax2;
      function cambiar_juego(classN){
        $('.alert').hide('slow');
        if(classN=='primer_juego'){
          accion='sorteo/operador/quiniela/quiniela_sorteador_primer_juego.php';
          param={accion:'mostrar',juego:'primer_juego'};
        }
        if(typeof param2 === 'object' && (param2.tipo=='ver_tradicional')){
          $.post( accion,
                  param2
          ).done(
                  function(data){
                    if(data.tipo=='info'){
                      $("#warning_juego.alert").slideDown("slow");
                      $('#warning_juego > .contenido_error').html(data.mensaje);
                    }
                    delete param2;
                  }
          );

        }else
         cargar_juego(accion,param);
        $("ul.juegos > li.active").removeClass('active');
        $('.'+classN).parent().addClass('active');
      }


      function cargar_juego (accion,parametros){
        $.post( accion,
                parametros
              ).done(
                function(data){
                  if(param.tipo!='ver_tradicional'){
                    $('#juego').html(data);
                  }
                }
              );

      };

     var buscarGanadores1= function buscarGanadores(){
        param={accion:'mostrar',juego:param.juego};
        $.post('sorteo/operador/quiniela/quiniela_listado_ganadores.php?buscarGanadores='+parseInt(Math.random() * 1000000000),
                param
        ).done(
                function(data){
                  $('#ganadores').html(data);

                }
        );
      }


      function habilitarJuegos(){
        $("#posicion").prop('disabled', false);
        $("#entero").prop('disabled', false);
        $("#fraccion").prop('disabled', false);

      }



      var controlGanadores2= function controlGanadores(){
          param={
                  accion:'control_ganador',
                  juego:param.juego
                };
          Ajax2=  $.post('sorteo/operador/quiniela/quiniela_sorteador_ajax.php?controlGanador=1',
                   param
                  ).done(
                    function(data){
                      if(data.mensaje=='No Finalizo'){
                        habilitarJuegos();
                      }
                      if(data.mensaje=='Finalizo' && param.juego=='primer_juego'){
                        $("#warning_juego2.alert").slideDown("slow");
                        $('#warning_juego2 > .contenido_error').html('Finalizo la extraccion de todos los numeros sorteados');
                        deshabilitarJuegos();
                      }else if(data.mensaje=='Finalizo' && param.juego=='segundo_juego'){
                        $("#warning_juego2.alert").slideDown("slow");
                        $('#warning_juego2 > .contenido_error').html('Finalizo, se encontraron ganadores');
                        deshabilitarJuegos();
                      } else if(data.tipo=='database'){
                        $("#error_juego.alert").slideDown("slow");
                        $('#error_juego >.contenido_error').html(data.mensaje);
                     }else if(data.mensaje=='NO SALE_O_SALE' && param.juego=='segundo_juego'){
                        $("#error_juego.alert").slideDown("slow");
                        $('#error_juego >.contenido_error').html('No existe en este sorteo Sortea Hasta que Sale');
                        deshabilitarJuegos();
                      }

                      if((typeof data.coincidencia != 'undefined')){
                        if(data.tipo == 'success'){
                          $("#success_juego.alert").slideDown("slow");
                          $('#success_juego > .contenido_error').html(data.coincidencia);
                            if(param.juego=='primer_juego')
                                cargarExtracciones({accion: "configuracion",juego:"primer_juego"});

                            if(param.juego=='segundo_juego')
                                cargarExtracciones({accion: "configuracion",juego:"segundo_juego"});
                        }else {
                          $("#error_juego.alert").slideDown("slow");
                          $('#error_juego > .contenido_error').html(data.coincidencia);
                        }
                      }


                    }
                );
        }


      $(document).ready(
        function() {
          cambiar_juego("primer_juego");
          buscarGanadores1();
          controlGanadores2();
          setInterval(buscarGanadores1,2000);
          setInterval(controlGanadores2,2000);
        });
    </script>
  </head>
  <body>
      <div id="contenedor_general" class="container">
        <h3 class="titulo"><img width="40" border="0" src="img/logo_loteria_peque.png"><?php echo $_SESSION['juego'] ?> <?php echo $_SESSION['sorteo'] ?> <span style="font-size:11px"><i class="icon-user"></i><?php echo $_SESSION['nombre_usuario'] . ', ' . $usuario_opera; ?></span></h3>
        <div class="bar" style="width: 40%"></div>
          <div>
            <div class="row-fluid show-grid">
              <div class="navbar">
                  <div class="navbar-inner">
                    <ul class="nav juegos">
                      <li class="active">
                        <a href="#" class="primer_juego" onclick="cambiar_juego(this.className); return false;"><?php echo $_SESSION['juego_tipo']; ?></a>
                      </li>
                      <li class="divider-vertical"></li>
                    </ul>
                  </div>
                </div>

            </div>
          </div>
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