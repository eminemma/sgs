<h3 class="titulo">Administracion de Incentivos</h3>
<?php 
session_start();
include_once dirname(__FILE__).'/../../db.php';
conectar_db();

$sorteo 	= 	$_SESSION['sorteo'];
$id_juego 	= 	$_SESSION['id_juego'];

//var_dump($_SESSION);
//$db->debug=true;

$sql = "
SELECT ts.PREMIO,
  TO_CHAR(ts.FECHA_SORTEO,'dd/mm/yyyy')                       AS FECHA_SORTEO,
  TO_CHAR(ts.PRESCRIPCION,'dd/mm/yyyy')                       AS PRESCRIPCION,
  DECODE(ts.id_jefe,NULL,'Sin Jefe',jefe.descripcion)         AS jefe_sorteo,
  DECODE(ts.id_escribano,NULL,'Sin Escribano',es.descripcion) AS escribano,
  TS.SORTEO,
  TS.SEMANA
FROM SGS.T_ANTICIPADA TS ,
  SUPERUSUARIO.usuarios jefe ,
  SGS.t_escribano es
WHERE 
ts.SORTEO    = ?
AND ts.ID_JUEGO    =?
AND ts.id_jefe     =jefe.id_usuario(+)
AND ts.id_escribano=es.id_escribano(+)
ORDER BY SEMANA ASC";
			
$res_sorteo=sql($sql, array($sorteo ,$id_juego));

require_once('encabezado.php');
?>
<table class="table table-bordered">
	<thead>
		<tr>
			
			<th>Sorteo</th>
			<th class="centerCell">Premio</th>
			<th class="centerCell">Fecha Sorteo</th>
			<th class="centerCell">Fecha Prescripcion</th>
			<th class="centerCell">Jefe</th>
			<th class="centerCell">Escribano</th>
			<th class="centerCell">Accion</th>
		</tr>	
	</thead>
	<tbody>
			<?php while($row = siguiente($res_sorteo)){ ?>
			<tr>
				<td class="centerCell"><?php echo 'Semana: ' .$row->SEMANA; ?></td>
				<td class="leftCell"><?php echo $row->PREMIO ?></td>
				<td class="centerCell"><?php echo $row->FECHA_SORTEO ?></td>
				<td class="centerCell"><?php echo $row->PRESCRIPCION ?></td>
				<td class="leftCell"><?php echo $row->JEFE_SORTEO ?></td>
				<td class="leftCell"><?php echo $row->ESCRIBANO ?></td>
				<td class="centerCell"><a href="#" onclick="g('administracion/administrar_anticipada/loteria_modificar_anticipada.php?id_juego=<?php echo $id_juego; ?>&sorteo=<?php echo $row->SORTEO; ?>&semana=<?php echo $row->SEMANA; ?>')" title="Modificar Incentivo"><div class="fa fa-pencil-square-o fa-2x"></div></a></td>
			</tr>
		<?php } ?>
	</tbody>
</table>