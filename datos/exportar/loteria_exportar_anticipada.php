<?php
@session_start();
include_once dirname(__FILE__) . '/../../db.php';
include_once dirname(__FILE__) . '/../../db_kanban.php';

conectar_db();

$sorteo   = $_SESSION['sorteo'];
$id_juego = $_SESSION['id_juego'];

$sql = "
SELECT TS.PREMIO,
  TO_CHAR(TS.FECHA_SORTEO,'DD/MM/YYYY')                       AS FECHA_SORTEO,
  DECODE(TS.ID_JEFE,NULL,'SIN JEFE',JEFE.DESCRIPCION)         AS JEFE_SORTEO,
  DECODE(TS.ID_ESCRIBANO,NULL,'SIN ESCRIBANO',ES.DESCRIPCION) AS ESCRIBANO,
  TS.SORTEO,to_char(FECHA_SORTEO,'dd/mm/yyyy') as FECHA_SORTEO,
  TS.SEMANA,
  TS.ORDEN
FROM
	SGS.T_ANTICIPADA TS ,
	SUPERUSUARIO.USUARIOS JEFE ,
	SGS.T_ESCRIBANO ES
WHERE
TS.SORTEO    = ?
AND TS.ID_JUEGO    =?
AND TS.ID_JEFE     =JEFE.ID_USUARIO(+)
AND TS.ID_ESCRIBANO=ES.ID_ESCRIBANO(+)
AND TRUNC(TS.FECHA_SORTEO) = TRUNC(SYSDATE)
ORDER BY SEMANA,ORDEN ASC";
$today      = date("d/m/Y");
$res_sorteo = sql($sql, array($sorteo, $id_juego));
if ($res_sorteo->RecordCount() == 0) {
    die(error('No posee anticipados el sorteo seleccionado'));
}
$semana = 0;
?>
<h3 class="titulo">Exportación sorteo anticipada a KANBAN</h3>
<div id="mensaje"></div>
<table class="table table-bordered">
	<thead>
		<tr>

			<th class="centerCell">Semana</th>
			<th class="centerCell">Orden</th>
			<th class="centerCell">Premio</th>
			<th class="centerCell">Exportar</th>
			<th class="centerCell">Extracto</th>
		</tr>
	</thead>
	<tbody>
<?php while ($row = siguiente($res_sorteo)) {
    ?>
			<tr>
				<td class="centerCell"><?php echo 'Semana ' . $row->SEMANA; ?></td>
				<td class="centerCell"><?php echo $row->ORDEN ?></td>
				<td class="leftCell"><?php echo $row->PREMIO ?></td>
<?php
$rs = sql("	SELECT EXPORTADO
			FROM SGS.T_ANTICIPADA_GANADORES
			WHERE SORTEO = ?
			AND SEMANA  = ?
			AND ID_JUEGO = ?
			AND ORDEN = ?", array($_SESSION['sorteo'], $row->SEMANA, $_SESSION['id_juego'], $row->ORDEN));
    $row_ganador = siguiente($rs);
    if ($row_ganador->EXPORTADO == 'SI') {
        ?>
				<td class="centerCell"><span class="label label-success">Exportado</span></td>

<?php
} else {
        ?>
        <td class="centerCell"><button  id="cambiar_juego_sorteo" class="btn" onclick="
        iniciar_giro('.icon-refresh');
        $.get(
    'datos/exportar/ajax.php?accion=exportar_anticipada&semana=<?php echo $row->SEMANA ?>&orden=<?php echo $row->ORDEN ?>',
    function(data){
      $('#mensaje').html(data);
    }
  ).error(
    function(jqXHR, textStatus, errorThrown){
      $('#mensaje').html('Se ha producido un error ('+errorThrown+').<br>'+textStatus);
    }
  ).complete(
    function(){
      detener_giro('.icon-refresh');
      g('datos/exportar/loteria_exportar_anticipada.php');

    }
  );
                                            return false;"
           <?php if ($row->FECHA_SORTEO != $today) {
            ?>disabled<?php }
        ?>>Enviar a Kanban</button></td>


<?php
}
    ?>
<td class="centerCell">
<?php

    if ($row->SEMANA != $semana) {
        $semana = $row->SEMANA;
        ?>

<button id="cambiar_juego_sorteo" class="btn" onclick="g('mail/procesar_enviar_mail.php?semana=<?php echo $row->SEMANA ?>');
                                            return false;" <?php if ($row->FECHA_SORTEO != $today) {
            ?>disabled<?php }
        ?>>Enviar a grupo</button>
<?php
}
    ?>
    </td>
</tr>
<?php
}
?>


	</tbody>
</table>


<div class="info  alert-info"><i class="icon-info-sign"></i> Al exportar se generaran de manera automaticamente los premios al apostador y a la agencia</div>
<br>
<div class="info  alert-info"><i class="icon-info-sign"></i> El grupo utilizado para enviar el extracto es:
<ul>
	<li>extracto.compra.anticipada@loteriacba.com.ar</li>
</ul>
</div>