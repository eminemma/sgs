<?php
include_once dirname(__FILE__) . '/../../db.php';
session_start();
$sorteo   = $_SESSION['sorteo'];
$id_juego = $_SESSION['id_juego'];
$rs       = sql("SELECT to_char(FECHA_SORTEO,'dd/mm/yyyy') as FECHA_SORTEO FROM sgs.t_sorteo WHERE SORTEO=? AND ID_JUEGO=?", array($sorteo, $id_juego));
$row      = siguiente($rs);
$today    = date("d/m/Y");
?>
<h1 class="titulo">Importaci&oacute;n de Datos</h1>
<h3 class="titulo">Sorteo <?php echo $_SESSION['sorteo']; ?></h3>
<div class="well form-inline">
    <h4 class="titulo">Extraordinarios - Sorteos Anticipados Programa</h4>
    <div class="row-fluid">

        <button class="btn-primary btn" class="btn-primary btn" onclick="if(confirm('¿Desea importar el programa de premios y billetes que participan de los anticipados del sorteo <?php echo $_SESSION['sorteo'] ?>?')) { g('datos/importar/ajax.php?'+a('importar_loteria_anticipada')+'&solo_billetes='+$('input[name=solo_billetes]:checkbox:checked').val()+'&solo_venta='+$('input[name=solo_venta]:checkbox:checked').val()) } "></i> Importar Datos</button>
    </div>
</div>
<div class="well form-inline">
    <h4 class="titulo">Ordinarios - Extraordinarios / Sorteo Final</h4>
	<div class="row-fluid">
		<div>
			<input type="checkbox" name="solo_venta" id="solo_venta" value="1"> Migrar Solo Venta Neta (Active esta casilla para evitar importar datos de Sorteo y Programa de Premios, y solo migrar la Venta Neta)
		</div>
		<br />
		<button class="btn-primary btn" <button class="btn-primary btn" onclick="if(confirm('¿Desea importar el programa de premios / vendidos para el sorteo final del sorteo <?php echo $_SESSION['sorteo'] ?>?')) { g('datos/importar/ajax.php?'+a('importar_loteria')+'&solo_billetes='+$('input[name=solo_billetes]:checkbox:checked').val()+'&solo_venta='+$('input[name=solo_venta]:checkbox:checked').val()) }" <?php if ($row->FECHA_SORTEO != $today) {
    ;

    ?>disabled<?php }
?> ></i> Importar Datos</button>
	</div>
</div>