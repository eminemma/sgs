<?php
@session_start();
include_once dirname(__FILE__).'/../../db.php';
include_once dirname(__FILE__).'/../../librerias/alambre/funcion.inc.php';


$res = sql("SELECT
				VALOR
			FROM
				sgs.T_PARAMETRO_COMPARTIDO
			WHERE
				ID_JUEGO = ?
				AND PARAMETRO = 'ZONA_MOSTRANDO'",
			array($_SESSION['id_juego']));

$row = siguiente($res);

$retorno = array(
					'zonaMostrando' => 'zona'.(int)$row->VALOR,
					'billetesZona1' => array(),
					'billetesZona2' => array(),
					'billetesZona3' => array(),
					'datosSorteo'   => array()
				);

/**
	BUSCAMOS LAS EXTRACCIONES DE LA ZONA 1
*/

$res = sql("SELECT
				LPAD(NUMERO, 5, 0) AS NUMERO,
				LPAD(POSICION, 2, 0) AS POSICION,
        		(	SELECT decode(COUNT(*),0,'NO','SI')
        			FROM sgs.T_BILLETES_PARTICIPANTES WHERE ID_JUEGO=te.id_juego and SORTEO=te.sorteo and billete=te.numero) AS VENDIDO

			FROM
				sgs.T_EXTRACCION te
			WHERE
					ID_JUEGO = ?
				AND SORTEO = ?
				AND ZONA_JUEGO = 1
			ORDER BY
				ORDEN",
			array($_SESSION['id_juego'], $_SESSION['sorteo']));

while($row = siguiente($res)){
	$retorno['billetesZona1'][] = array('numero' => $row->NUMERO, 'posicion' => $row->POSICION, 'vendido' => $row->VENDIDO);
}


$res = sql("SELECT 	TE.DESCRIPCION AS ESCRIBANO,
				  	TOP.DESCRIPCION     AS OPERADOR,
				  	TJ.DESCRIPCION      AS JEFE,
				  	TJT.DESCRIPCION AS TIPO_JUEGO,
				  	TO_CHAR(TS.FECHA_SORTEO,'dd/mm/YYYY') as FECHA_SORTEO,
				  	TO_CHAR(TS.FECHA_SORTEO,'HH24:MI') AS HORA_SORTEO,
				  	TS.SORTEO
			FROM 	SGS.T_SORTEO TS,
				  	SGS.T_ESCRIBANO TE,
				  	SUPERUSUARIO.USUARIOS TOP,
				  	SUPERUSUARIO.USUARIOS TJ,
				  	SGS.T_JUEGO_TIPO TJT
			WHERE TS.ID_ESCRIBANO 	  = TE.ID_ESCRIBANO
				AND TS.ID_OPERADOR    = TOP.ID_USUARIO
				AND TS.ID_JEFE        = TJ.ID_USUARIO
				AND TS.ID_TIPO_JUEGO  = TJT.ID_JUEGO_TIPO
				AND TS.SORTEO         = ?
				AND TS.ID_JUEGO 	  = ?",
			array($_SESSION['sorteo'],$_SESSION['id_juego']));
$row = siguiente($res);

$retorno['datosSorteo'] = 	array(    
	  						    'sorteo'        => ($row->SORTEO==NULL)       ? '' : $row->SORTEO,
								'fecha_sorteo'  => ($row->FECHA_SORTEO==NULL) ? '' : $row->FECHA_SORTEO,
								'hora_sorteo'   => ($row->HORA_SORTEO==NULL)  ? '' : $row->HORA_SORTEO,
								'programa'	   => array()
							);

$retorno['datosSorteo']['programa'] = array();
$res = sql("  	SELECT TS.SORTEO,TPD.DESCRIPCION,TPP.PREMIO_EFECTIVO,TPP.ID_DESCRIPCION,TPP.PROGRESION
  				FROM  	SGS.T_SORTEO TS,
        				SGS.T_PROGRAMA TP,
        				SGS.T_PROGRAMA_PREMIOS TPP,
          				SGS.T_PREMIO_DESCRIPCION TPD
  				WHERE TS.SORTEO = ?
    				AND TS.ID_PROGRAMA = TP.ID_PROGRAMA
    				AND TP.ID_PROGRAMA = TPP.ID_PROGRAMA
    				AND TPP.ID_DESCRIPCION = TPD.ID_PREMIO_DESC
    			ORDER BY TPP.ID_DESCRIPCION",
			array($_SESSION['sorteo']));
while($row = siguiente($res)){
	if($row->ID_DESCRIPCION == 1){
		$retorno['datosSorteo']['programa'][] = array("tipo_premio" => "PRIMER PREMIO", "monto" => '$'.number_format($row->PREMIO_EFECTIVO,0,',','.'));
		$progresion = '$'.number_format($row->PROGRESION,0,',','.');
	}	
	if($row->ID_DESCRIPCION == 2)
		$retorno['datosSorteo']['programa'][] = array("tipo_premio" => "2º PREMIO", "monto" =>'$'.number_format($row->PREMIO_EFECTIVO,0,',','.'));
	if($row->ID_DESCRIPCION == 3)
		$retorno['datosSorteo']['programa'][] = array("tipo_premio" => "3º PREMIO", "monto" => '$'.number_format($row->PREMIO_EFECTIVO,0,',','.'));
	if($row->ID_DESCRIPCION == 4)
		$retorno['datosSorteo']['programa'][] = array("tipo_premio" => "4º PREMIO", "monto" => '$'.number_format($row->PREMIO_EFECTIVO,0,',','.'));
	if($row->ID_DESCRIPCION == 5)
		$retorno['datosSorteo']['programa'][] = array("tipo_premio" => "5º PREMIO", "monto" =>'$'.number_format($row->PREMIO_EFECTIVO,0,',','.'));
	if($row->ID_DESCRIPCION == 6)
		$retorno['datosSorteo']['programa'][] = array("tipo_premio" => "5 PREMIOS", "monto" => '$'.number_format($row->PREMIO_EFECTIVO,0,',','.'));
	if($row->ID_DESCRIPCION == 11){
		$retorno['datosSorteo']['programa'][] = array("tipo_premio" => "10 PREMIOS", "monto" => '$'.number_format($row->PREMIO_EFECTIVO,0,',','.'));
		break;
	}
		
}

$retorno['datosSorteo']['programa'][] = array("tipo_premio" => "PROGRESION", "monto" => $progresion);


/**
	BUSCAMOS LAS EXTRACCIONES DE LA ZONA 2
*/

$res = sql("SELECT
				LPAD(NUMERO, 5, 0) AS NUMERO,
				LPAD(POSICION, 2, 0) AS POSICION,
				LPAD(FRACCION, 2, 0) AS FRACCION
			FROM
				sgs.T_EXTRACCION
			WHERE
					ID_JUEGO = ?
				AND SORTEO = ?
				AND ZONA_JUEGO = 3
			ORDER BY
				ORDEN",
			array($_SESSION['id_juego'], $_SESSION['sorteo']));

while($row = siguiente($res)){
	$retorno['billetesZona2'][] = array('numero' => $row->NUMERO,
										'posicion' => $row->POSICION,
										'fraccion' => $row->FRACCION);
}

/**
	BUSCAMOS EL GANADOR DEL SIEMPRE SALE (ZONA 3)
*/

$res = sql("SELECT

				LPAD(BILLETE, 5, 0) AS BILLETE,
				--LPAD(FRACCION, 2, 0) AS FRACCION,
				LPAD(ID_AGENCIA, 4, 0) AS ID_AGENCIA,
				LPAD(ID_PREMIO_DESCRIPCION, 2, 0) AS POSICION,
				LOCALIDAD,
				PROVINCIA,
				DESCRIPCION_AGENCIA,
				DESCRIPCION_SUCURSAL,
				ID_SUCURSAL
			FROM
				sgs.T_GANADORES
			WHERE
					ID_JUEGO = ?
				AND SORTEO = ?
				AND ID_PREMIO_DESCRIPCION = 1
        GROUP BY BILLETE,ID_AGENCIA,ID_PREMIO_DESCRIPCION,LOCALIDAD,PROVINCIA,DESCRIPCION_AGENCIA,DESCRIPCION_SUCURSAL,ID_SUCURSAL",
			array($_SESSION['id_juego'], $_SESSION['sorteo']));

if($res->RecordCount()>0){
	$localidad = array();
	while($row = siguiente($res)){
		$billete=$row->BILLETE;
		$posicion=$row->POSICION;
		
		if($row->DESCRIPCION_AGENCIA=='VENTA MOSTRADOR'){
			$localidad[]='VENTA MOSTRADOR, '.$row->PROVINCIA;
		}else if($row->DESCRIPCION_AGENCIA=='VENTA CONTADO'){
			$localidad[]='VENTA MOSTRADOR, '.$row->PROVINCIA;
		}else{
			$localidad[]=utf8_encode(str_pad($row->ID_AGENCIA, 5, "0", STR_PAD_LEFT).' - '.$row->LOCALIDAD.', '.$row->PROVINCIA);
		}
	}


	$retorno['billetesZona3'][] = array('numero' => $billete,
										'posicion' => $posicion,
										'localidad' => $localidad
										);
}
//var_dump($retorno['billetesZona3']);
echo json_encode($retorno);
exit;

echo '{
		"zonaMostrando" : "zona2",

		"billetesZona1" : 	[
								{ "numero" : "12345", "posicion" : "13" },
								{ "numero" : "99999", "posicion" : "01" ,"vendido":"NO"},
								{ "numero" : "12345", "posicion" : "15" }
							],
		"programa"		: [
							{"tipo_premio" : "PRIMER PREMIO", "monto" : "150000"}
						  ]					

		"billetesZona2" : 	[
								{ "numero" : "12345", "posicion" : "03", "fraccion" : "15" },
								{ "numero" : "12344", "posicion" : "01", "fraccion" : "01" },
								{ "numero" : "12344", "posicion" : "02", "fraccion" : "01" }

							],

		"billetesZona3" : 	[
								{ "numero" : "12346", "posicion" : "01", "nro_agen" : "00001", "localidad" : "San Antonio de Litin, Córdoba" }
							]
	}';
exit;






// echo '{
// 		"zonaMostrando" : "zona1",

// 		"billetesZona1" : 	[
// 								{ "numero" : "23455", "posicion" : "01" },
// 								{ "numero" : "12345", "posicion" : "02" },
// 								{ "numero" : "02345", "posicion" : "03" }
// 							],

// 		"billetesZona2" : 	[
// 								{ "numero" : "99999", "posicion" : "01", "fraccion" : "11" },
// 								{ "numero" : "12345", "posicion" : "02", "fraccion" : "07" },
// 								{ "numero" : "12345", "posicion" : "03", "fraccion" : "15" }
// 							],

// 		"billetesZona3" : 	[
// 								{ "numero" : "12345", "posicion" : "01", "nro_agen" : "00001", "localidad" : "San Antonio de Litin, Córdoba" }
// 							]
// 	}';