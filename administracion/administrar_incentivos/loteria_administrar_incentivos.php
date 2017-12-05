<h3 class="titulo">Administracion de Incentivos</h3>
<?php 
session_start();
include_once dirname(__FILE__).'/../../db.php';
conectar_db();
$sorteo 	=$_SESSION['sorteo'];
$id_juego 	=$_SESSION['id_juego'];
//var_dump($_SESSION);
// $db->debug=true;
$sql = "SELECT  ts.DESCRIPCION,
				to_char(TS.FECHA_SORTEO,'dd/mm/yyyy') as FECHA_SORTEO,
				to_char(ts.fecha_hasta_pago_premios,'dd/mm/yyyy') as fecha_hasta_pago_premio,
				decode(ts.id_jefe,null,'Sin Jefe',jefe.descripcion) as jefe_sorteo,
				decode(ts.id_operador,null,'Sin Operador',operador.descripcion) as operador,
				decode(ts.id_escribano,null,'Sin Escribano',es.descripcion) as escribano,
				TS.SORTEO,ts.ID_INCENTIVO_SORTEO,ROWNUM rnum 
		FROM 	SGS.T_INCENTIVO_SORTEO TS,SUPERUSUARIO.usuarios jefe,SUPERUSUARIO.usuarios operador,SGS.t_escribano es
		WHERE 	ts.SORTEO=?
			AND ts.ID_JUEGO=?
			AND ts.id_jefe=jefe.id_usuario(+)
			AND ts.id_operador=operador.id_usuario(+)
			AND ts.id_escribano=es.id_escribano";
$res_sorteo=sql($sql, array($sorteo ,$id_juego));

require_once('encabezado.php');
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
				<td class="centerCell"><a href="#" onclick="g('administracion/administrar_incentivos/loteria_modificar_incentivos.php?id_sorteo=<?php echo $row->ID_INCENTIVO_SORTEO ?>')" title="Modificar Incentivo"><div class="fa fa-pencil-square-o fa-2x"></div></a></td>
			</tr>
		<?php } ?>
	</tbody>
</table>