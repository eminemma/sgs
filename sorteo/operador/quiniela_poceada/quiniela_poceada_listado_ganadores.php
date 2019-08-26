<?php
@session_start();
include_once dirname(__FILE__) . '/../../../db.php';
conectar_db();
$accion         = isset($_POST['accion']) ? $_POST['accion'] : '';
$juego          = isset($_POST['juego']) ? $_POST['juego'] : '';
$id_juego       = $_SESSION['id_juego'];
$sorteo         = $_SESSION['sorteo'];
$colores        = array('SlateGrey', 'Khaki', 'Goldenrod', 'Salmon', 'OliveDrab', 'SteelBlue', 'Brown');
$color_posicion = array();
if ($accion == 'mostrar' && $juego == 'primer_juego') {
    try {
        $rs_extraccion_segundo = sql("  SELECT
                                               ID_EXTRACCION,
                                               ORDEN,
                                               NUMERO,
                                               POSICION,

                                              CASE WHEN (SORTEO_ASOC='QUINIELA DUPLICADO%') THEN 'EXTRACCION QUINIELA '|| POSICION
                                              WHEN SORTEO_ASOC LIKE ('QUINIELA ASOCIADA%') THEN 'EXTRACCION QUINIELA ' || POSICION
                                              ELSE
                                              'EXTRACCION ' || POSICION
                                               END  AS DESCRIPCION,
                                              'ENTERO' AS AFECTA,
                                              SORTEO_ASOC,
                                              POSICION_DUPLICADO
                                        FROM
                                            SGS.T_EXTRACCION           TE
                                        WHERE
                                             TE.ID_JUEGO = ?
                                            AND TE.SORTEO = ?
                                        ORDER BY
                                            ZONA_JUEGO DESC,
                                            ORDEN DESC", array($id_juego, $sorteo));
        if ($rs_extraccion_segundo->RowCount() == 0) {
            error('Sin Extracciones hasta el momento');
        }

    } catch (exception $e) {
        error('Error al insertar: ' . $db->ErrorMsg());
    }
    $repetidos_posicion = array();
    while ($row_extraccion_segundo = $rs_extraccion_segundo->FetchNextObject($toupper = true)) {
        $repetidos_posicion[$row_extraccion_segundo->NUMERO] += 1;
    }
    $rs_extraccion_segundo->MoveFirst();
    ?>
<span style="text-align: right;"><a border="0" target="_blank" href="sorteo/acta/quiniela_poceada_acta_final.php" class="icon-print" ></a></span>
  <table class="table table-bordered">
  <thead>
    <tr>
      <th># Orden</th>
      <th>Extraccion</th>
      <th>Tipo</th>
      <th>Numero</th>
      <th>Eliminar</th>
    </tr>
  </thead>
  <tbody>
<?php
while ($row_extraccion_segundo = $rs_extraccion_segundo->FetchNextObject($toupper = true)) {
        if ($color_posicion[$row_extraccion_segundo->NUMERO] == null && $repetidos_posicion[$row_extraccion_segundo->NUMERO] > 1) {
            $color_posicion[$row_extraccion_segundo->NUMERO] = $colores[0];
            unset($colores[0]);
            $colores = array_values($colores);
        } else if (strpos($row_extraccion_segundo->SORTEO_ASOC, 'VALIDA') !== false) {
            $color_posicion[$row_extraccion_segundo->NUMERO] = 'YellowGreen';
        }
        ?>
     <tr style="background-color: <?php echo $color_posicion[$row_extraccion_segundo->NUMERO] ?>">
      <td class="centerCell"><?php echo $row_extraccion_segundo->ORDEN; ?></td>
      <td class="leftCell"><?php echo $row_extraccion_segundo->DESCRIPCION; ?></td>
      <td class="leftCell"><?php echo ($row_extraccion_segundo->SORTEO_ASOC); ?></td>
      <td class="centerCell"><?php echo str_pad($row_extraccion_segundo->NUMERO, 2, "0", STR_PAD_LEFT); ?></td>
      <td class="centerCell">
        <?php if (strpos($row_extraccion_segundo->SORTEO_ASOC, 'VALIDA') !== false || strpos($row_extraccion_segundo->SORTEO_ASOC, 'COINCIDE') !== false) {?>
        <a href="#" onclick="if(confirm('Desea eliminar la posicion <?php echo $row_extraccion_segundo->POSICION; ?>?')) { $.post('sorteo/operador/quiniela_poceada/quiniela_poceada_sorteador_ajax.php',{accion:'eliminar',extraccion:<?php echo $row_extraccion_segundo->ID_EXTRACCION; ?>,posicion:<?php echo $row_extraccion_segundo->POSICION; ?>,entero:<?php echo $row_extraccion_segundo->NUMERO; ?>}).done(function(data){
          mostrarMensaje(data);
          if(data.tipo == 'success'){
            configuracion.cantidad_premios_tradicional=parseInt(configuracion.cantidad_premios_tradicional)+1;
          }else if('<?php echo $row_extraccion_segundo->AFECTA; ?>'=='FRACCION'){
            configuracion.cantidad_premios_extraordinario=parseInt(configuracion.cantidad_premios_extraordinario)+1;
          }
      });  } return false;" border="0"><div class="icon-trash" border="0"></div></a>
       <?php }?>


    </td>

    </tr>
    <?php }?>

  </tbody>
  </table>
<?php }
