<?php
@session_start();
include_once dirname(__FILE__) . '/../../db.php';

conectar_db();

$accion   = isset($_POST['accion']) ? $_POST['accion'] : '';
$juego    = isset($_POST['juego']) ? $_POST['juego'] : '';
$id_juego = $_SESSION['id_juego'];
$sorteo   = $_SESSION['sorteo'];

if ($accion == 'mostrar' && $juego == 'primer_juego') {
    try {
        $rs_extraccion_segundo = sql("  SELECT  TE.ID_EXTRACCION,
                                        TE.ORDEN,
                                        TE.POSICION,
                                        TE.NUMERO,
                                        TE.FRACCION,
                                        TPP.PREMIO_EFECTIVO,
                                        TPP.TIPO_PREMIO,
                                        UPPER(TPP.AFECTA) AS AFECTA,
                                        DECODE(
                                                (
                                                  SELECT COUNT(*)
                                                  FROM SGS.T_GANADORES
                                                  WHERE ID_PREMIO_DESCRIPCION = TE.POSICION
                                                    AND BILLETE               = TE.NUMERO
                                                ),0,'--',
                                                (
                                                  SELECT COUNT(*)
                                                  FROM SGS.T_GANADORES
                                                  WHERE ID_PREMIO_DESCRIPCION = TE.POSICION
                                                    AND BILLETE               = TE.NUMERO
                                                )
                                              )AS GANADORES,
                                        (
                                          SELECT DESCRIPCION_ESPECIA
                                          FROM SGS.T_DESCRIPCION_ESPECIAS
                                          WHERE ID_DESCRIPCION_ESPECIA=TPP.PREMIO_ID_ESPECIAS
                                        ) AS ESPECIA,
                                        TPD.DESCRIPCION
                                FROM  SGS.T_EXTRACCION TE,
                                      SGS.T_PROGRAMA TP,
                                      SGS.T_PROGRAMA_PREMIOS TPP,
                                      SGS.T_PREMIO_DESCRIPCION TPD,
                                      SGS.T_SORTEO TS
                                WHERE TP.ID_PROGRAMA   = TPP.ID_PROGRAMA
                                  AND TPP.ID_DESCRIPCION = TPD.ID_PREMIO_DESC
                                  AND TE.POSICION        = TPP.ID_DESCRIPCION
                                  AND TP.ID_JUEGO        = TE.ID_JUEGO
                                  AND TE.ID_JUEGO        = ?
                                  AND TE.SORTEO          = ?
                                  AND TS.ID_JUEGO        = TE.ID_JUEGO
                                  AND TS.SORTEO          = TE.SORTEO
                                  AND TS.ID_PROGRAMA = TP.ID_PROGRAMA
                                  AND (TE.ZONA_JUEGO = 1 OR TE.ZONA_JUEGO = 3 )
                                ORDER BY ZONA_JUEGO DESC , ORDEN DESC", array($id_juego, $sorteo));
        if ($rs_extraccion_segundo->RowCount() == 0) {
            die('<div id="warning_juego" class="alert alert-info" >
          <button type="button" class="close" onclick="$(\'#warning_juego.alert\').slideUp(\'slow\');">×</button>
          <span><i class="icon-info-sign"></i></span>
          <span class="contenido_error">Sin Extracciones hasta el momento</span></div>');
        }

    } catch (exception $e) {
        die('<div id="error_juego" class="alert alert-info" >
    <button type="button" class="close" onclick="$(\'#error_juego.alert\').slideUp(\'slow\');">×</button>
    <span><i class="icon-remove"></i></span>
    <span class="contenido_error">"Error al insertar: ' . $db->ErrorMsg() . '</div>
    </span>');
    }

    ?>
<span style="text-align: right;"><a border="0" target="_blank" href="sorteo/acta/quiniela_acta_final_zonas.php" class="icon-print" ></a></span>
<table class="table table-striped table-bordered">
  <thead>
    <tr>
      <th># Orden</th>
      <th>Posicion</th>
      <th>Entero</th>
      <th>Fraccion</th>
      <th>Premio -</th>
      <th>Cant Ganadores</th>
      <th >Importe/Especie</th>
      <th>Eliminar</th>
    </tr>
  </thead>
  <tbody>
  <?php
while ($row_extraccion_segundo = $rs_extraccion_segundo->FetchNextObject($toupper = true)) {
        ?>
  <tr>
    <td class="centerCell"><?php echo $row_extraccion_segundo->ORDEN; ?></td>
    <td class="centerCell"><?php echo $row_extraccion_segundo->POSICION; ?></td>
    <td class="centerCell"><?php echo str_pad($row_extraccion_segundo->NUMERO, 5, "0", STR_PAD_LEFT); ?></td>
    <td class="centerCell"><?php echo $row_extraccion_segundo->FRACCION; ?></td>
    <td><strong><?php echo $row_extraccion_segundo->DESCRIPCION; ?></strong></td>
    <td class="centerCell"><?php echo $row_extraccion_segundo->GANADORES; ?></td>
    <td class="rightCell"><?php if ($row_extraccion_segundo->TIPO_PREMIO == 'EFECTIVO') {
            echo '$ ' . number_format($row_extraccion_segundo->PREMIO_EFECTIVO, 2, ',', '.');
        } else {
            echo utf8_encode($row_extraccion_segundo->ESPECIA);
        }
        ?></td>
    <td class="centerCell"><a href="#" onclick="if(confirm('Desea eliminar la posicion <?php echo $row_extraccion_segundo->POSICION; ?>?')) { $.post('sorteo/operador/loteria_sorteador_ajax.php',{accion:'eliminar',extraccion:<?php echo $row_extraccion_segundo->ID_EXTRACCION; ?>,posicion:<?php echo $row_extraccion_segundo->POSICION; ?>,entero:<?php echo $row_extraccion_segundo->NUMERO; ?>}).done(function(data){
    mostrarMensaje(data);
    if('<?php echo $row_extraccion_segundo->AFECTA; ?>'=='ENTERO'){
    loteria_tradicional.cantidad_premios_tradicional=parseInt(loteria_tradicional.cantidad_premios_tradicional)+1;
    }else if('<?php echo $row_extraccion_segundo->AFECTA; ?>'=='FRACCION'){
    loteria_tradicional.cantidad_premios_extraordinario=parseInt(loteria_tradicional.cantidad_premios_extraordinario)+1;
    }
    });  } return false;" border="0"><div class="icon-trash" border="0"></div></a></td>
  </tr>
  <?php }?>
  </tbody>
</table>
<?php
} else if ($accion == 'mostrar' && $juego == 'segundo_juego') {
    $rs_extraccion_segundo = sql("  SELECT  TE.ID_EXTRACCION,
                                      TE.ORDEN,
                                      TE.POSICION,
                                      TE.NUMERO,
                                      TE.FRACCION,
                                      TPP.PREMIO_EFECTIVO,
                                      TPP.TIPO_PREMIO,
                                      DECODE(
                                              (
                                                SELECT COUNT(*)
                                                FROM SGS.T_GANADORES
                                                WHERE ID_PREMIO_DESCRIPCION   = TE.POSICION
                                                  AND BILLETE                 = TE.NUMERO
                                              ),0,'--',
                                              (
                                                SELECT COUNT(*)
                                                FROM SGS.T_GANADORES
                                                WHERE ID_PREMIO_DESCRIPCION  = TE.POSICION
                                                  AND BILLETE                = TE.NUMERO
                                              )
                                            )AS GANADORES,
                                      DECODE(
                                              (
                                                SELECT COUNT(*)
                                                FROM SGS.T_GANADORES
                                                WHERE ID_PREMIO_DESCRIPCION   = TE.POSICION
                                                  AND BILLETE                 = TE.NUMERO
                                              ),0,'Sin Ganadores','Ganador'
                                            )AS DESCRIPCION
                              FROM  SGS.T_EXTRACCION TE,
                                    SGS.T_PROGRAMA TP,
                                    SGS.T_PROGRAMA_PREMIOS TPP,
                                    SGS.T_PREMIO_DESCRIPCION TPD,SGS.T_SORTEO TS
                              WHERE TE.ID_JUEGO     =?
                                AND TE.SORTEO         =?
                                AND (TE.ZONA_JUEGO    =2)
                                AND TP.ID_JUEGO       =TE.ID_JUEGO
                                AND TPP.ID_DESCRIPCION=TE.POSICION
                                AND TPD.ID_PREMIO_DESC=TE.POSICION
                                AND TP.ID_PROGRAMA    =TPP.ID_PROGRAMA
                                AND TE.SORTEO=TS.SORTEO
                                AND TE.ID_JUEGO=TS.ID_JUEGO
                                AND TS.ID_PROGRAMA=TP.ID_PROGRAMA
                              ORDER BY ZONA_JUEGO DESC , ORDEN DESC", array($id_juego, $sorteo));

    if ($rs_extraccion_segundo->RowCount() == 0) {
        die('<div id="warning_juego" class="alert alert-info" >
  <button type="button" class="close" onclick="$(\'#warning_juego.alert\').slideUp(\'slow\');">×</button>
  <span><i class="icon-info-sign"></i></span>
  <span class="contenido_error">Sin Extracciones hasta el momento</span>
  </div>');
    }

    ?>
<table class="table table-striped table-bordered">
  <thead>
    <tr>
      <th style="width: 8%"># Orden</th>
      <th>Posicion</th>
      <th>Entero</th>
      <th>Premio --</th>
      <th>Fracciones</th>
      <th >Importe/Especie</th>
      <th>Eliminar</th>
    </tr>
  </thead>
  <tbody>
<?php
while ($row_extraccion_segundo = $rs_extraccion_segundo->FetchNextObject($toupper = true)) {
        ?>
  <tr>
    <td class="centerCell"><?php echo $row_extraccion_segundo->ORDEN; ?></td>
    <td class="centerCell"><?php echo $row_extraccion_segundo->POSICION; ?></td>
    <td class="centerCell"><?php echo str_pad($row_extraccion_segundo->NUMERO, 5, "0", STR_PAD_LEFT); ?></td>
    <td><strong><?php echo $row_extraccion_segundo->DESCRIPCION; ?></strong></td>
    <td class="centerCell"><?php echo $row_extraccion_segundo->GANADORES; ?></td>
    <td class="rightCell"><?php if ($row_extraccion_segundo->TIPO_PREMIO == 'EFECTIVO') {
            echo '$ ' . number_format($row_extraccion_segundo->PREMIO_EFECTIVO, 2, ',', '.');
        } else {
            echo utf8_encode($row_extraccion_segundo->ESPECIA);
        }
        ?></td>
    <td class="centerCell"><a href="#" onclick="if(confirm('Desea eliminar la posicion <?php echo $row_extraccion_segundo->POSICION; ?>?')) { $.post('sorteo/operador/loteria_sorteador_ajax.php',{accion:'eliminar',extraccion:<?php echo $row_extraccion_segundo->ID_EXTRACCION; ?>,posicion:<?php echo $row_extraccion_segundo->POSICION; ?>,entero:<?php echo $row_extraccion_segundo->NUMERO; ?>}).done(function(data){
    mostrarMensaje(data);
    });  } return false;"><div class="icon-trash" border="0"></div></td>
  </tr>
  <?php }?>

  </tbody>
</table>

<?php
} else if ($accion == 'mostrar' && $juego == 'tercer_juego') {

    $tmp_juego = 3;

    $rs_extraccion_segundo = sql("  SELECT  TE.ID_EXTRACCION,
                                      TE.ORDEN,
                                      TE.POSICION,
                                      TE.NUMERO,
                                      TE.FRACCION,
                                      TPP.PREMIO_EFECTIVO,
                                      TPP.TIPO_PREMIO,
                                      DECODE(
                                              (
                                                SELECT COUNT(*)
                                                FROM SGS.T_GANADORES
                                                WHERE ID_PREMIO_DESCRIPCION   = TE.POSICION
                                                  AND BILLETE                 = TE.NUMERO
                                              ),0,'--',
                                              (
                                                SELECT COUNT(*)
                                                FROM SGS.T_GANADORES
                                                WHERE ID_PREMIO_DESCRIPCION  = TE.POSICION
                                                  AND BILLETE                = TE.NUMERO
                                              )
                                            )AS GANADORES,
                                      TPD.DESCRIPCION,
                                       (
                                          SELECT DESCRIPCION_ESPECIA
                                          FROM SGS.T_DESCRIPCION_ESPECIAS
                                          WHERE ID_DESCRIPCION_ESPECIA=TPP.PREMIO_ID_ESPECIAS
                                        ) AS ESPECIA
                              FROM  SGS.T_EXTRACCION TE,
                                    SGS.T_PROGRAMA TP,
                                    SGS.T_PROGRAMA_PREMIOS TPP,
                                    SGS.T_PREMIO_DESCRIPCION TPD,SGS.T_SORTEO TS
                              WHERE TE.ID_JUEGO     =?
                                AND TE.SORTEO         =?
                                AND (TE.ZONA_JUEGO    =$tmp_juego)
                                AND TP.ID_JUEGO       =TE.ID_JUEGO
                                AND TPP.ID_DESCRIPCION=TE.POSICION
                                AND TPD.ID_PREMIO_DESC=TE.POSICION
                                AND TP.ID_PROGRAMA    =TPP.ID_PROGRAMA
                                AND TE.SORTEO=TS.SORTEO
                                AND TE.ID_JUEGO=TS.ID_JUEGO
                                AND TS.ID_PROGRAMA=TP.ID_PROGRAMA
                              ORDER BY ZONA_JUEGO DESC , ORDEN DESC", array($id_juego, $sorteo));

    if ($rs_extraccion_segundo->RowCount() == 0) {
        die('<div id="warning_juego" class="alert alert-info" >
  <button type="button" class="close" onclick="$(\'#warning_juego.alert\').slideUp(\'slow\');">×</button>
  <span><i class="icon-info-sign"></i></span>
  <span class="contenido_error">Sin Extracciones hasta el momento</span>
  </div>');
    }

    ?>
<table class="table table-striped table-bordered">
  <thead>
    <tr>
      <th># Orden</th>
      <th>Posicion</th>
      <th>Entero</th>
      <th>Fraccion</th>
      <th>Premio ---</th>
      <th>Cant Ganadores</th>
      <th >Importe/Especie</th>
      <th>Eliminar</th>
    </tr>
  </thead>
  <tbody>
<?php
while ($row_extraccion_segundo = $rs_extraccion_segundo->FetchNextObject($toupper = true)) {
        ?>
  <tr>
    <td class="centerCell"><?php echo $row_extraccion_segundo->ORDEN; ?></td>
    <td class="centerCell"><?php echo $row_extraccion_segundo->POSICION; ?></td>
    <td class="centerCell"><?php echo str_pad($row_extraccion_segundo->NUMERO, 5, "0", STR_PAD_LEFT); ?></td>
    <td class="centerCell"><?php echo $row_extraccion_segundo->FRACCION; ?></td>
    <td><strong><?php echo $row_extraccion_segundo->DESCRIPCION; ?></strong></td>
    <td class="centerCell"><?php echo $row_extraccion_segundo->GANADORES; ?></td>
    <td class="rightCell"><?php if ($row_extraccion_segundo->TIPO_PREMIO == 'EFECTIVO') {
            echo '$ ' . number_format($row_extraccion_segundo->PREMIO_EFECTIVO, 2, ',', '.');
        } else {
            echo utf8_encode($row_extraccion_segundo->ESPECIA);
        }
        ?></td>
    <td class="centerCell"><a href="#" onclick="if(confirm('Desea eliminar la posicion <?php echo $row_extraccion_segundo->POSICION; ?>?')) { $.post('sorteo/operador/loteria_sorteador_ajax.php',{accion:'eliminar',extraccion:<?php echo $row_extraccion_segundo->ID_EXTRACCION; ?>,posicion:<?php echo $row_extraccion_segundo->POSICION; ?>,entero:<?php echo $row_extraccion_segundo->NUMERO; ?>}).done(function(data){
    mostrarMensaje(data);

    $('#numero').attr('disabled',false);
    $('#numero').val('');
    $('#numero').focus();
    });  } return false;"><div class="icon-trash" border="0"></div></td>
  </tr>
  <?php }?>

  </tbody>
</table>
<?php
} else if ($accion == 'mostrar' && $juego == 'incentivo') {

    $rs_extraccion_incentivo = sql("  SELECT  I.ID_INCENTIVO,I.DESCRIPCION AS TIPO_INCENTIVO,
                                        G.ALEATORIO,G.ID_SUCURSAL,
                                        A.DESCRIPCION_SUCURSAL,
                                        G.ID_AGENCIA,A.DESCRIPCION_AGENCIA,I.IMPORTE
                                FROM SGS.T_INCENTIVOS_GANADORES G,SGS.T_INCENTIVOS I,SGS.T_INCENTIVOS_AGENCIAS A
                                WHERE I.ID_INCENTIVO = G.ID_INCENTIVO
                                  AND I.ID_JUEGO     = G.ID_JUEGO
                                  AND I.SORTEO       = G.SORTEO
                                  AND G.ID_AGENCIA   = A.ID_AGENCIA
                                  AND G.ID_INCENTIVO_A = A.ID_INCENTIVO
                                  AND G.ID_SUCURSAL  = A.ID_SUCURSAL
                                  AND G.ID_JUEGO     = ?
                                  AND G.SORTEO       = ?
                                ORDER BY 1", array($id_juego, $sorteo));

    if ($rs_extraccion_incentivo->RowCount() == 0) {
        die('<div id="warning_juego" class="alert alert-info" >
  <button type="button" class="close" onclick="$(\'#warning_juego.alert\').slideUp(\'slow\');">×</button>
  <span><i class="icon-info-sign"></i></span>
  <span class="contenido_error">Sin Extracciones hasta el momento</span>
  </div>');
    }

    ?>
<table class="table table-striped table-bordered">
  <thead>
    <tr>
      <th>Tipo Incentivo</th>
      <th>Aleatorio</th>
      <th>Delegaci&oacute;n</th>
      <th>Agencia</th>
      <th>Titular</th>
      <th>-</th>
      <th>Eliminar</th>
    </tr>
  </thead>
  <tbody>
  <?php
while ($row_extraccion_incentivo = $rs_extraccion_incentivo->FetchNextObject($toupper = true)) {
        ?>
    <tr>
      <td class="centerCell"><?php echo $row_extraccion_incentivo->TIPO_INCENTIVO; ?></td>
      <td class="centerCell"><?php echo $row_extraccion_incentivo->ALEATORIO; ?></td>
      <td class="centerCell"><?php echo $row_extraccion_incentivo->ID_SUCURSAL . ' - ' . $row_extraccion_incentivo->DESCRIPCION_SUCURSAL; ?></td>
      <td class="centerCell"><?php echo $row_extraccion_incentivo->ID_AGENCIA; ?></td>
      <td class="centerCell"><?php echo $row_extraccion_incentivo->DESCRIPCION_AGENCIA; ?></td>
      <td class="rightCell">
      <?php //echo '$ '.number_format($row_extraccion_incentivo->IMPORTE,2,',','.'); ?>
      <?php //echo 'Kit Mundial '; ?>
      </td>
      <td class="centerCell"><a href="#" onclick="if(confirm('Desea eliminar el incentivo <?php echo $row_extraccion_incentivo->TIPO_INCENTIVO; ?>?')) { $.post('sorteo/operador/loteria_incentivo_ajax.php',{accion:'eliminar',id_incentivo:<?php echo $row_extraccion_incentivo->ID_INCENTIVO; ?>}).done(function(data){
      mostrarMensaje(data);
      });  } return false;"><div class="icon-trash" border="0"></div></td>
    </tr>
  <?php }?>
  </tbody>
</table>

<?php
} else if ($accion == 'mostrar' && $juego == 'anticipada') {

    $rs_extraccion_incentivo = sql("
                                    SELECT  TAG.AGENCIA, TAG.LOCALIDAD, TAG.SEMANA, TA.PREMIO,
                                            TAG.BILLETE, TAG.FRACCION, TAG.NOMBRE,TAG.ORDEN,TAG.SUCURSAL
                                    FROM SGS.T_ANTICIPADA_GANADORES TAG, SGS.T_ANTICIPADA TA
                                    WHERE  TAG.ID_JUEGO = TA.ID_JUEGO
                                      AND TAG.SORTEO    = TA.SORTEO
                                      AND TAG.SEMANA    = TA.SEMANA
                                      AND TAG.ORDEN     = TA.ORDEN
                                      AND TAG.ID_JUEGO  = ?
                                      AND TAG.SORTEO    = ?
                                      AND TO_DATE(TA.FECHA_SORTEO) = TO_DATE(SYSDATE)
                                    ORDER BY TAG.SEMANA,TAG.ORDEN", array($id_juego, $sorteo));

    if ($rs_extraccion_incentivo->RowCount() == 0) {
        die('<div id="warning_juego" class="alert alert-info" >
  <button type="button" class="close" onclick="$(\'#warning_juego.alert\').slideUp(\'slow\');">×</button>
  <span><i class="icon-info-sign"></i></span>
  <span class="contenido_error">Sin Extracciones hasta el momento</span>
  </div>');
    }

    ?>
<table class="table table-striped table-bordered table-condensed" style="font-size: 12px">
  <thead>
    <tr>
<!--       <th>Semana</th> -->
      <th>#</th>
      <th>Premio</th>
      <th>Billete</th>
      <th>Frac.</th>
      <th>Agencia</th>
      <th>Localidad</th>
      <th>Eliminar</th>
      <th>Acta</th>
      <th>Vegetal</th>
      <th>Extracto</th>
      <th>Informe</th>
    </tr>
  </thead>
  <tbody>
  <?php
while ($row_extraccion_incentivo = $rs_extraccion_incentivo->FetchNextObject($toupper = true)) {

        if ($row_extraccion_incentivo->NOMBRE == 'VENTA CONTADO CASA CENTRAL') {
            $agencia   = '9001 CORDOBA';
            $localidad = 'CORDOBA';
        } else if ($row_extraccion_incentivo->NOMBRE == 'VENTA CONTADO') {
            if ($row_extraccion_incentivo->SUCURSAL == 'CASA CENTRAL') {
                $localidad = 'CORDOBA';
            } else {
                $localidad = $row_extraccion_incentivo->SUCURSAL;
            }

            $agencia = '9001 ' . $localidad;

        } else if ($row_extraccion_incentivo->AGENCIA != null) {
            $localidad = $row_extraccion_incentivo->LOCALIDAD;
            $agencia   = $row_extraccion_incentivo->AGENCIA . ' - ' . $row_extraccion_incentivo->NOMBRE;
        }

        ?>
    <tr>
<!--       <td class="centerCell"><?php echo $row_extraccion_incentivo->SEMANA; ?></td> -->
      <td class="centerCell"><?php echo $row_extraccion_incentivo->ORDEN; ?></td>
      <td class="leftCell"><?php echo $row_extraccion_incentivo->PREMIO; ?></td>
      <td class="centerCell"><?php echo $row_extraccion_incentivo->BILLETE; ?></td>
      <td class="centerCell"><?php echo $row_extraccion_incentivo->FRACCION; ?></td>
      <td class="leftCell"><?php echo $agencia; ?></td>
      <td class="centerCell"><?php echo $localidad; ?></td>

      <td class="centerCell"><a href="#" onclick="if(confirm('Desea eliminar el sorteo anticipado <?php echo $row_extraccion_incentivo->SEMANA . ' - ' . $row_extraccion_incentivo->PREMIO; ?>?')) { $.post('sorteo/operador/loteria_anticipada_ajax.php',{accion:'eliminar',semana:<?php echo $row_extraccion_incentivo->SEMANA; ?>,id_juego:<?php echo $id_juego; ?>,sorteo:<?php echo $sorteo; ?>,orden:<?php echo $row_extraccion_incentivo->ORDEN; ?>}).done(function(data){
      mostrarMensaje(data);
      });  } return false;"><div class="icon-trash" border="0"></div>
	  </td>
	  <td class="centerCell">
		<a id="Alcanzan Objetivo" target="_blank" href="sorteo/acta/loteria_acta_anticipada.php?semana=<?php echo $row_extraccion_incentivo->SEMANA; ?>"><img style="height:20px;" src="img/printer.png " /></a>
	  </td>
	  <td class="centerCell">
		<a id="Alcanzan Objetivo" target="_blank" href="sorteo/acta/papel_vegetal_anticipada_gordo_invierno_2015.php?semana=<?php echo $row_extraccion_incentivo->SEMANA; ?>"><img style="height:20px;" src="img/printer.png " /></a>
	  </td>
	  <td class="centerCell">
		<a id="Alcanzan Objetivo" target="_blank" href="sorteo/acta/limagen_extracto_anticipada.php?semana=<?php echo $row_extraccion_incentivo->SEMANA; ?>"><img style="height:20px;" src="img/printer.png " /></a>
	  </td>
	  <td class="centerCell">
		<a id="Alcanzan Objetivo" target="_blank" href="sorteo/acta/info_premio_anticipada.php?semana=<?php echo $row_extraccion_incentivo->SEMANA; ?>&orden=<?php echo $row_extraccion_incentivo->ORDEN; ?>"><img style="height:20px;" src="img/printer.png " /></a>
	  </td>


    </tr>
  <?php }?>
  </tbody>
</table>

<?php
} else if ($accion == 'mostrar' && $juego == 'cuarto_juego') {

    $rs_extraccion_segundo = sql("  SELECT  TE.ID_EXTRACCION,
                                      TE.ORDEN,
                                      TE.POSICION,
                                      TE.NUMERO,
                                      TE.FRACCION,
                                      TPP.PREMIO_EFECTIVO,
                                      TPP.TIPO_PREMIO,
                                      DECODE(
                                              (
                                                SELECT COUNT(*)
                                                FROM SGS.T_GANADORES
                                                WHERE ID_PREMIO_DESCRIPCION   = TE.POSICION
                                                  AND BILLETE                 = TE.NUMERO
                                              ),0,'--',
                                              (
                                                SELECT COUNT(*)
                                                FROM SGS.T_GANADORES
                                                WHERE ID_PREMIO_DESCRIPCION  = TE.POSICION
                                                  AND BILLETE                = TE.NUMERO
                                              )
                                            )AS GANADORES,
                                      TPD.DESCRIPCION,
                                       (
                                          SELECT DESCRIPCION_ESPECIA
                                          FROM SGS.T_DESCRIPCION_ESPECIAS
                                          WHERE ID_DESCRIPCION_ESPECIA=TPP.PREMIO_ID_ESPECIAS
                                        ) AS ESPECIA
                              FROM  SGS.T_EXTRACCION TE,
                                    SGS.T_PROGRAMA TP,
                                    SGS.T_PROGRAMA_PREMIOS TPP,
                                    SGS.T_PREMIO_DESCRIPCION TPD,SGS.T_SORTEO TS
                              WHERE TE.ID_JUEGO     =?
                                AND TE.SORTEO         =?
                                AND (TE.ZONA_JUEGO    = 4)
                                AND TP.ID_JUEGO       =TE.ID_JUEGO
                                AND TPP.ID_DESCRIPCION=TE.POSICION
                                AND TPD.ID_PREMIO_DESC=TE.POSICION
                                AND TP.ID_PROGRAMA    =TPP.ID_PROGRAMA
                                AND TE.SORTEO=TS.SORTEO
                                AND TE.ID_JUEGO=TS.ID_JUEGO
                                AND TS.ID_PROGRAMA=TP.ID_PROGRAMA
                              ORDER BY ZONA_JUEGO DESC , ORDEN DESC", array($id_juego, $sorteo));

    if ($rs_extraccion_segundo->RowCount() == 0) {
        die('<div id="warning_juego" class="alert alert-info" >
  <button type="button" class="close" onclick="$(\'#warning_juego.alert\').slideUp(\'slow\');">×</button>
  <span><i class="icon-info-sign"></i></span>
  <span class="contenido_error">Sin Extracciones hasta el momento</span>
  </div>');
    }

    ?>
<table class="table table-striped table-bordered">
  <thead>
    <tr>
      <th># Orden</th>
      <th>Posicion</th>
      <th>Entero</th>
      <th>Premio ---</th>
      <th >Importe/Especie</th>
      <th>Eliminar</th>
    </tr>
  </thead>
  <tbody>
<?php
while ($row_extraccion_segundo = $rs_extraccion_segundo->FetchNextObject($toupper = true)) {
        ?>
  <tr>
    <td class="centerCell"><?php echo $row_extraccion_segundo->ORDEN; ?></td>
    <td class="centerCell"><?php echo $row_extraccion_segundo->POSICION; ?></td>
    <td class="centerCell"><?php echo str_pad($row_extraccion_segundo->NUMERO, 5, "0", STR_PAD_LEFT); ?></td>
    <td><strong><?php echo $row_extraccion_segundo->DESCRIPCION; ?></strong></td>
    <td class="rightCell"><?php if ($row_extraccion_segundo->TIPO_PREMIO == 'EFECTIVO') {
            echo '$ ' . number_format($row_extraccion_segundo->PREMIO_EFECTIVO, 2, ',', '.');
        } else {
            echo utf8_encode($row_extraccion_segundo->ESPECIA);
        }
        ?></td>
    <td class="centerCell"><a href="#" onclick="if(confirm('Desea eliminar la posicion <?php echo $row_extraccion_segundo->POSICION; ?>?')) { $.post('sorteo/operador/loteria_sorteador_ajax.php',{accion:'eliminar',extraccion:<?php echo $row_extraccion_segundo->ID_EXTRACCION; ?>,posicion:<?php echo $row_extraccion_segundo->POSICION; ?>,entero:<?php echo $row_extraccion_segundo->NUMERO; ?>}).done(function(data){
    mostrarMensaje(data);

    $('#numero').attr('disabled',false);
    $('#numero').val('');
    $('#numero').focus();
    });  } return false;"><div class="icon-trash" border="0"></div></td>
  </tr>
  <?php }?>

  </tbody>
</table>
<?php }?>
