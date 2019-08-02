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
    <link rel="stylesheet" type="text/css" href="sorteo/operador/quiniela/estilo_sorteador.css">
    <script type="text/javascript" src="librerias/jquery/jquery-1.10.1.js"></script>
    <script type="text/javascript" src="librerias/bootstrap/js/bootstrap.min.js"></script>
    <script type="text/javascript" src="librerias/bootstrap/js/bootstrap-fileupload.min.js"></script>
    <script type="text/javascript" src="librerias/bootstrap/js/bootstrap-datetimepicker.min.js"></script>

    <script type="text/javascript" src="sorteo/operador/quiniela_poceada/funciones.js"></script>
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
                accion  : 'mostrar',
                juego   : 'primer_juego'
              };      
      var intervalo2;
      var intervalo1;
      var Ajax1;
      var Ajax2;

     var buscarGanadores1= function buscarGanadores(){
        param={accion:'mostrar',juego:param.juego};
        $.post('sorteo/operador/quiniela_poceada/quiniela_poceada_listado_ganadores.php?buscarGanadores='+parseInt(Math.random() * 1000000000),
                param
        ).done(
                function(data){
                  $('#ganadores').html(data);

                }
        );
      }

      function habilitarJuegos(){
        $("#entero").prop('disabled', false);
        $("#fraccion").prop('disabled', false);

      }

      var controlGanadores2= function controlGanadores(){
          param={
                  accion:'control_ganador',
                  juego:param.juego
                };
          Ajax2=  $.post('sorteo/operador/quiniela_poceada/quiniela_poceada_sorteador_ajax.php?controlGanador=1',
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
                      } else if(data.tipo=='database'){
                        $("#error_juego.alert").slideDown("slow");
                        $('#error_juego >.contenido_error').html(data.mensaje);
                     }

                      if((typeof data.coincidencia != 'undefined' || typeof data.reinicio != 'undefined')){
                        if(data.tipo == 'success' || data.tipo == 'info'){
                          if(data.coincidencia == 'VALIDA'){
                            
                            marcar_extraccion_sorteada($('#posicion').val(), true);
                            if(buscar_valor_por_campo('sorteado',false,'posicion') !== undefined){
                              $("#posicion").val('');
                              $("#posicion").val(buscar_valor_por_campo('sorteado',false,'posicion'));
                              var e = $.Event( "keypress", { which: 13 } );
                              $( "#posicion" ).trigger(e);
                            }
                          }else if(data.reinicio == 'SI'){
                            cargarConfiguracion({accion: "configuracion",juego:"primer_juego"}); 
                          }else {
                             $("#success_juego.alert").slideDown("slow");
                             $('#success_juego > .contenido_error').html(data.coincidencia);
                          }                           
                            
                            
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

          cargarConfiguracion({accion: "configuracion",juego:"primer_juego"}); 

          buscarGanadores1();
          controlGanadores2();
          setInterval(buscarGanadores1,2000);
          setInterval(controlGanadores2,1000);
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
          <div class="row-fluid show-grid">   
          <div id="contendio_juego" class="well form-inline text-center">
            <h4>Juego <?php echo $_SESSION['juego_tipo']; ?></h4>
            <div class="subtitulo_juego label label-info" style="display:none">Primer Premio</div>
            <form class="form-inline" action="#">
              <span class="texto_sorteo">Posicion</span> 
                <input type="text" id="posicion"  disabled="disabled"  name="posicion" class="input-small bola" size="1" tabindex="0"> 
              <span id="entero_div">
                <span class="texto_sorteo">Numero</span> 
                  <input type="text" id="entero"name="entero" class="input-medium bola" size="1" tabindex="1">
              </span>
            <span id="fraccion_div" style="display:none">
                <span class="texto_sorteo">Fraccion</span> 
                  <input type="text" id="fraccion" name="fraccion" class="input-small bola" size="1" tabindex="2">
              </span>    
            </form>
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