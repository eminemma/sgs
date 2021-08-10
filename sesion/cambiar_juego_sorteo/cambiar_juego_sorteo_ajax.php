<?php
@session_start();
require_once '../../db.php';
include_once '../../tablas.php';
include_once '../../mensajes.php';
include_once '../../paginador.php';

if ($_GET['accion'] == 'ver_sorteos') {
    //Limpiar una por una la sessiones del sistema
    $_SESSION['id_juego'] = (int) $_GET['juego'];
    $_SESSION['juego']    = $_GET['descripcion_juego'];
    $juego                = $_GET['juego'];
    conectar_db();

//$db->debug=true;

    $rs_juegos = sql("		SELECT 	ID_JUEGO_TIPO,
							  		ID_JUEGO,
							  		CODIGO_TIPO_JUEGO,
							  		DESCRIPCION
							FROM SGS.T_JUEGO_TIPO
							WHERE ID_JUEGO=?
							AND ID_JUEGO_TIPO<>83",
        array((int) $_GET['juego']));

    $i = 0;
    ?>
<div>
<ul class="nav nav-pills">
<?php
$tipo_juego = $_GET['tipo_juego'];

    if ($tipo_juego == 62) {
        sql("UPDATE T_PARAMETRO_COMPARTIDO
		SET VALOR           = 1
		WHERE PARAMETRO       = 'ZONA_MOSTRANDO'
		AND ID_JUEGO        = ?",
            array((int) $_GET['juego']));
    }
    ?>
    <li>
    <a onclick="g('sesion/cambiar_juego_sorteo/cambiar_juego_sorteo_ajax.php?juego=<?php echo $_GET['juego']; ?>&descripcion_juego=<?php echo $_GET['descripcion_juego'] ?>&tipo_juego=-1'+a('ver_sorteos'), '#contenedor_sorteos');" href="#">SIN PROGRAMA</a>
    </li>
<?php
while ($row = siguiente($rs_juegos)) {
        if ($i == 0) {
            $tipo_juego     = isset($_GET['tipo_juego']) ? $_GET['tipo_juego'] : $row->CODIGO_TIPO_JUEGO;
            $rs_tipo_juegos = sql("SELECT ID_JUEGO_TIPO,
									  ID_JUEGO,
									  CODIGO_TIPO_JUEGO,
									  DESCRIPCION
									FROM T_JUEGO_TIPO
									WHERE ID_JUEGO_TIPO = ?
									",
                array((int) $tipo_juego));
            $row_tipo_juego = siguiente($rs_tipo_juegos);

            $i = 1;
        }
        ?>
	<li
		<?php
if ($row->ID_JUEGO_TIPO == $tipo_juego) {

            if ($row_tipo_juego->DESCRIPCION == 'NOCTURNO') {
                ?>
				class="active"
			<?php
} else if ($row_tipo_juego->DESCRIPCION == 'LA TURISTA') {
                ?>
				class="active"
			<?php
} else if ($row_tipo_juego->DESCRIPCION == 'LA PRIMERA DE LA MAÑANA') {
                ?>
				class="active"
			<?php
} else if ($row_tipo_juego->DESCRIPCION == 'VESPERTINO') {
                ?>
				class="active"
			<?php
} else if ($row_tipo_juego->DESCRIPCION == 'MATUTINO') {?>
				class="active"
			<?php } else if ($row_tipo_juego->DESCRIPCION == 'ORDINARIA') {
                ?>
				class="active"
			<?php
} else if ($row_tipo_juego->DESCRIPCION == 'EXTRAORDINARIA') {
                ?>
				class="active"
			<?php
} else if ($row_tipo_juego->DESCRIPCION == 'RASPAGUITA') {
                ?>
				class="active"
			<?php
}

        }
        ?>
	>
		<a onclick="g('sesion/cambiar_juego_sorteo/cambiar_juego_sorteo_ajax.php?juego=<?php echo $_GET['juego']; ?>&descripcion_juego=<?php echo $_GET['descripcion_juego'] ?>&tipo_juego=<?php echo $row->ID_JUEGO_TIPO ?>'+a('ver_sorteos'), '#contenedor_sorteos');" href="#"><?php echo $row->DESCRIPCION; ?></a>
	</li>

<?php
}
    ?>
</ul>
</div>
<?php
$sql = '';
    $rs  = null;
    if ((int) $tipo_juego == -1) {
        $sql = " SELECT
					    LPAD(TS.SORTEO, 5, '0')
					    || ' - '
					    || TS.DESCRIPCION AS DESC_SORTEO,
					    TO_CHAR(TS.FECHA_SORTEO, 'DD/MM/YYYY') AS FECHA_SORTEO,
					    TS.SORTEO,
					    TJ.DESCRIPCION    AS DESC_JUEGO
					FROM
					    SGS.T_SORTEO       TS,
					    SGS.T_JUEGO        TJ
					WHERE
					    TS.ID_JUEGO = TJ.ID_JUEGO
					    AND TJ.ID_JUEGO = ?
                        AND TS.ID_PROGRAMA IS NULL
					ORDER BY
					    TS.FECHA_SORTEO DESC,
					    TS.SORTEO DESC";

        $rs = getPaginadorRs($sql, array($juego));
    } else {
        $sql = "	    SELECT
					    LPAD(TS.SORTEO, 5, '0')
					    || ' - '
					    || TS.DESCRIPCION AS DESC_SORTEO,
					    TO_CHAR(TS.FECHA_SORTEO, 'DD/MM/YYYY') AS FECHA_SORTEO,
					    TS.SORTEO,
					    TJ.DESCRIPCION    AS DESC_JUEGO,
					    TJP.DESCRIPCION   AS TIPO_JUEGO
					FROM
					    SGS.T_SORTEO       TS,
					    SGS.T_JUEGO        TJ,
					    SGS.T_JUEGO_TIPO   TJP,
					    SGS.T_PROGRAMA     TP
					WHERE
					    TS.ID_JUEGO = TJ.ID_JUEGO
					    AND TS.ID_PROGRAMA = TP.ID_PROGRAMA
					    AND TJP.CODIGO_TIPO_JUEGO = TP.CODIGO_TIPO_JUEGO
					    AND TJ.ID_JUEGO = ?
					    AND TJP.ID_JUEGO_TIPO = ?
										--AND TS.FECHA_SORTEO>=SYSDATE - 8
					ORDER BY
					    TS.FECHA_SORTEO DESC,
					    TS.SORTEO DESC";
        $rs = getPaginadorRs($sql, array($juego, $tipo_juego));

    }
    getPaginadorLinks('#contenedor_sorteos');
    ?>
<table class="table table-condensed table-bordered">
	<thead>
		<tr>
			<th class="centerCell">#</th>
			<th>Fecha Sorteo</th>
			<th>Sorteo</th>
			<th class="centerCell">Accion</th>
		</tr>
	</thead>
	<tbody>
			<?php while ($row = siguiente($rs)) {?>
			<tr>
				<td class="centerCell" style="width:10%"><?php echo $row->RNUM ?></td>
				<td class="centerCell" style="width:10%"><?php echo $row->FECHA_SORTEO ?></td>
				<td class="leftCell"><?php echo $row->DESC_SORTEO ?></td>
				<td class="centerCell" style="width:10%"><a href="#" title="Cambiar Juego/Sorteo" onclick="$.get('sesion/cambiar_juego_sorteo/cambiar_juego_sorteo_ajax.php?accion=cambiar_sorteo&sorteo=<?php echo $row->SORTEO ?>&juego=<?php echo $juego ?>&descripcion_juego=<?php echo $row->DESC_JUEGO ?>&tipo_juego=<?php echo $row->TIPO_JUEGO ?>',function(data){
						$('#sorteo_s').html(<?php echo $row->SORTEO ?>);
						if('<?php echo $juego ?>'=='1')
										g('administracion/administrar_sorteos/loteria_administrar_sorteos.php');
									else if('<?php echo $juego ?>'=='2')
										g('administracion/administrar_sorteos/quiniela_administrar_sorteos.php');
									else if('<?php echo $juego ?>'=='32')
										g('administracion/administrar_sorteos/quiniela_poceada_administrar_sorteos.php');


					})" title="Modificar Sorteo/Juego"><div class="fa fa-play fa-2x"></div></a></td>
			</tr>
		<?php }?>
	</tbody>
</table>
<?php
getPaginadorLinks('#contenedor_sorteos');
} else if ($_GET['accion'] == 'cambiar_sorteo') {
    $_SESSION['id_juego']           = (int) $_GET['juego'];
    $_SESSION['sorteo']             = (int) $_GET['sorteo'];
    $_SESSION['juego']              = $_GET['descripcion_juego'];
    $_SESSION['serie']              = 1;
    $_SESSION['descripcion_sorteo'] = 'EXTRAORDINARIO';
    $_SESSION['juego_tipo']         = $_GET['tipo_juego'];
    $_SESSION['sale_o_sale']        = 'NO';

    $sql = "	SELECT COUNT(*) AS CANTIDAD
				FROM 	SGS.T_SORTEO TS,
 						SGS.T_PROGRAMA_PREMIOS TPP
				WHERE       TS.SORTEO     	=  ?
					AND 	TS.ID_JUEGO     =  ?
					AND 	TPP.ID_PROGRAMA = TS.ID_PROGRAMA
				GROUP BY 	TPP.SALE_O_SALE
				HAVING 		 UPPER(TPP.SALE_O_SALE) = 'SI'";
    $res = sql($sql, array($_SESSION['sorteo'], $_SESSION['id_juego']));

    if ($res->RecordCount() > 0) {
        $_SESSION['sale_o_sale'] = 'SI';
    }

}

//var_dump($_SESSION);
