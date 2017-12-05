<h3 class="titulo">Administracion de Sorteo</h3>
<?php 
session_start();
include_once dirname(__FILE__).'/../../db.php';
include_once dirname(__FILE__).'/ajax.php';
conectar_db();
$sorteo 	=$_SESSION['sorteo'];
$id_juego 	=$_SESSION['id_juego'];

$res_sorteo=sql(sql_administracion_sorteo(), array($sorteo ,$id_juego));

require_once('quiniela_encabezado.php');
?>
<table class="table table-bordered">
	<thead>
		<tr>
			<th>#</th>
			<th>Sorteo</th>
			<th class="centerCell">Denominacion</th>
			<th class="centerCell">Fecha Sorteo</th>
			<th class="centerCell">Fecha Prescripcion</th>
			<th class="centerCell">Jefe</th>
			<th class="centerCell">Operador</th>
			<th class="centerCell">Escribano</th>
			<th class="centerCell">Accion</th>
		</tr>	
	</thead>
	<tbody>
			<?php while($row = siguiente($res_sorteo)){ ?>
			<tr>
				<td class="centerCell"><?php echo $row->RNUM ?></td>
				<td class="centerCell"><?php echo $row->SORTEO ?></td>
				<td class="centerCell"><?php echo $row->DESCRIPCION ?></td>
				<td class="centerCell"><?php echo $row->FECHA_SORTEO ?></td>
				<td class="centerCell"><?php echo $row->FECHA_HASTA_PAGO_PREMIO ?></td>
				<td class="centerCell"><?php echo $row->JEFE_SORTEO ?></td>
				<td class="centerCell"><?php echo $row->OPERADOR ?></td>
				<td class="centerCell"><?php echo $row->ESCRIBANO ?></td>
				<td class="centerCell"><a href="#" onclick="g('administracion/administrar_sorteos/quiniela_modificar_sorteo.php?id_sorteo=<?php echo $row->ID_SORTEO ?>')" title="Modificar Sorteo"><div class="fa fa-pencil-square-o fa-2x"></div></a></td>
			</tr>
		<?php } ?>
	</tbody>
</table>