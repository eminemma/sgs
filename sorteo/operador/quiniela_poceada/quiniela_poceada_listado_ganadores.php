<?php
@session_start();
include_once dirname(__FILE__) . '/../../../db.php';
conectar_db();
$accion   = isset($_POST['accion']) ? $_POST['accion'] : '';
$juego    = isset($_POST['juego']) ? $_POST['juego'] : '';
$id_juego = $_SESSION['id_juego'];
$sorteo   = $_SESSION['sorteo'];

if ($accion == 'mostrar' && $juego == 'primer_juego') {
    try {
        $rs_extraccion_segundo = sql("  SELECT
                                               ID_EXTRACCION,ORDEN,NUMERO,POSICION, 'EXTRACCION '||POSICION AS DESCRIPCION,'ENTERO' AS AFECTA,SORTEO_ASOC
                                        FROM
                                            SGS.T_EXTRACCION           TE
                                        WHERE
                                             TE.ID_JUEGO = ?
                                            AND TE.SORTEO = ?
                                        ORDER BY
                                            ZONA_JUEGO DESC,
                                            ORDEN DESC", array($id_juego, $sorteo));
        if ($rs_extraccion_segundo->RowCount() == 0) {
            die('<div id="warning_juego" class="alert alert-info" >
      <button type="button" class="close" onclick="$(\'#warning_juego.alert\').slideUp(\'slow\');">×</button>
      <span><i class="icon-info-sign"></i></span>
      <span class="contenido_error">Sin Extracciones hasta el momento</span>
      </div>');
        }

    } catch (exception $e) {
        die('<div id="error_juego" class="alert alert-info" >
      <button type="button" class="close" onclick="$(\'#error_juego.alert\').slideUp(\'slow\');">×</button>
      <span><i class="icon-remove"></i></span>
      <span class="contenido_error">"Error al insertar: ' . $db->ErrorMsg() . '</div>
      </span>');
    }

    ?>
<span style="text-align: right;"><a border="0" target="_blank" href="sorteo/acta/quiniela_poceada_acta_final.php" class="icon-print" ></a></span>
  <table class="table table-striped table-bordered">
  <thead>
    <tr>
      <th># Orden</th>
      <th>Extraccion</th>
      <th>Tipo</th>
      <th>Numero</th>
<!--      <th >Importe/Especie</th>-->
      <th>Eliminar</th>
    </tr>
  </thead>
  <tbody>
    <?php while ($row_extraccion_segundo = $rs_extraccion_segundo->FetchNextObject($toupper = true)) {?>
     <tr>
      <td class="centerCell"><?php echo $row_extraccion_segundo->ORDEN; ?></td>
      <td class="leftCell"><?php echo $row_extraccion_segundo->DESCRIPCION; ?></td>
      <td class="leftCell"><?php echo ($row_extraccion_segundo->SORTEO_ASOC); ?></td>
      <td class="centerCell"><?php echo str_pad($row_extraccion_segundo->NUMERO, 2, "0", STR_PAD_LEFT); ?></td>
      <td class="centerCell">
        <?php if(strpos($row_extraccion_segundo->SORTEO_ASOC,'VALIDA') !== FALSE || strpos($row_extraccion_segundo->SORTEO_ASOC, 'COINCIDE') !== false) { ?>
        <a href="#" onclick="if(confirm('Desea eliminar la posicion <?php echo $row_extraccion_segundo->POSICION; ?>?')) { $.post('sorteo/operador/quiniela_poceada/quiniela_poceada_sorteador_ajax.php',{accion:'eliminar',extraccion:<?php echo $row_extraccion_segundo->ID_EXTRACCION; ?>,posicion:<?php echo $row_extraccion_segundo->POSICION; ?>,entero:<?php echo $row_extraccion_segundo->NUMERO; ?>}).done(function(data){
          mostrarMensaje(data);
          if('<?php echo $row_extraccion_segundo->AFECTA; ?>'=='ENTERO'){
            marcar_extraccion_sorteada('<?php echo $row_extraccion_segundo->POSICION; ?>', false);
            if(buscar_valor_por_campo('sorteado',false,'posicion') !== undefined){
              $('#posicion').val(buscar_valor_por_campo('sorteado',false,'posicion'));
              var e = $.Event( 'keypress', { which: 13 } );
              $( '#posicion' ).trigger(e);
            }
            configuracion.cantidad_premios_tradicional=parseInt(configuracion.cantidad_premios_tradicional)+1;
          }else if('<?php echo $row_extraccion_segundo->AFECTA; ?>'=='FRACCION'){
            configuracion.cantidad_premios_extraordinario=parseInt(configuracion.cantidad_premios_extraordinario)+1;
          }
      });  } return false;" border="0"><div class="icon-trash" border="0"></div></a>
       <?php } ?>


    </td>

    </tr>
    <?php }?>

  </tbody>
  </table>
<?php }
