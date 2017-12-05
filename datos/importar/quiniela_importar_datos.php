<?php 
session_start();
?>
<h1 class="titulo">Importaci&oacute;n de Datos</h1>
<h3 class="titulo">Sorteo <?php echo $_SESSION['sorteo']; ?></h3>
<div class="well form-inline">
	<div class="row-fluid">
		<button class="btn-primary btn" <button class="btn-primary btn" onclick="g('datos/importar/ajax.php?'+a('importar_quiniela')+'&solo_billetes='+$('input[name=solo_billetes]:checkbox:checked').val()+'&solo_venta='+$('input[name=solo_venta]:checkbox:checked').val())"></i> Importar Datos</button>
	</div>
</div>