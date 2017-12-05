<?php session_start(); ?>
<h1 class="titulo">Exportación de Datos</h1>
<div class="well form-inline">
	<div class="row-fluid">
		Exportar Datos del Sorteo a Kanban Sorteo:<?php echo $_SESSION['sorteo'] ?>
		<button class="btn-primary btn" <button class="btn-primary btn" onclick="g('datos/exportar/ajax.php?'+a('exportar'))"></i> Exportar Datos</button>
	</div>
</div>