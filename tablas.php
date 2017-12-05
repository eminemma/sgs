<?php
$tablas_cantidad = 1;

function generarTabla($rs, $totalizar = null, $corte = null){
	global $tablas_cantidad;

	echo '<table id="tabla_'.$tablas_cantidad.'" class="table table-striped table-hover table-condensed table-bordered">';
		echo '<tr>';
			foreach (siguiente($rs, true) as $columna => $valor) {
				echo '<th>'.$columna.'</th>';
			}
		echo '</tr>';


		$rs->MoveFirst();

		while($row = siguiente($rs, true)){
			echo '<tr>';
			foreach($row as $columna => $valor){
				echo '<td>'.$valor.'</td>';
			}
			echo '</tr>';
		}
	echo '</table>';
	
	$tablas_cantidad++;
}