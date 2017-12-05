<?php
@session_start();
include_once dirname(__FILE__) . '/../../db.php';
include_once dirname(__FILE__) . '/../../librerias/alambre/funcion.inc.php';

$res = sql("SELECT
  					PRIMER_ELEMENTO,
  					ULTIMO_ELEMENTO
			FROM  SGS.T_SORTEO
			WHERE SORTEO=?
			AND ID_JUEGO=?",
    array($_SESSION['sorteo'], $_SESSION['id_juego']));

$row             = siguiente($res);
$primer_elemento = $row->PRIMER_ELEMENTO;
$ultimo_elemento = $row->ULTIMO_ELEMENTO;

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
    'zonaMostrando'  => 'zona' . (int) $row->VALOR,
    'billetesZona1'  => array(),
    'billetesZona2'  => array(),
    'billetesZona3'  => array(),
    'billetesZona4'  => array(),
    'parametroZona4' => array('primer_elemento' => $primer_elemento, 'ultimo_elemento' => $ultimo_elemento),
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

while ($row = siguiente($res)) {
    $retorno['billetesZona1'][] = array('numero' => $row->NUMERO, 'posicion' => $row->POSICION, 'vendido' => $row->VENDIDO);
}

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

while ($row = siguiente($res)) {
    $retorno['billetesZona2'][] = array('numero' => $row->NUMERO,
        'posicion'                                   => $row->POSICION,
        'fraccion'                                   => $row->FRACCION);
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
        GROUP BY BILLETE,ID_AGENCIA,ID_PREMIO_DESCRIPCION,LOCALIDAD,PROVINCIA,DESCRIPCION_AGENCIA,DESCRIPCION_SUCURSAL,ID_SUCURSAL
        ORDER BY ID_AGENCIA",
    array($_SESSION['id_juego'], $_SESSION['sorteo']));

if ($res->RecordCount() > 0) {
    $localidad = array();
    while ($row = siguiente($res)) {
        $billete  = $row->BILLETE;
        $posicion = $row->POSICION;

        if ($row->DESCRIPCION_AGENCIA == 'VENTA CONTADO CASA CENTRAL') {
            $localidad[] = 'VENTA CONTADO CASA CENTRAL, ' . $row->PROVINCIA;
        } else if ($row->DESCRIPCION_AGENCIA == 'VENTA CONTADO') {
            $localidad[] = 'VENTA CONTADO, ' . $row->PROVINCIA;
        } else {
            /*$localidad[] = utf8_encode(str_pad($row->ID_AGENCIA, 5, "0", STR_PAD_LEFT) . ' - ' . $row->DESCRIPCION_AGENCIA . ', ' . $row->LOCALIDAD . ', ' . $row->PROVINCIA);*/
            $localidad[] = str_pad($row->ID_AGENCIA, 5, "0", STR_PAD_LEFT) . ' - ' . $row->DESCRIPCION_AGENCIA . ', ' . $row->LOCALIDAD . ', ' . $row->PROVINCIA;
        }
    }

    $retorno['billetesZona3'][] = array('numero' => $billete,
        'posicion'                                   => $posicion,
        'localidad'                                  => $localidad,
    );
}

/**
BUSCAMOS LAS EXTRACCIONES DE LA ZONA 4 SORTEO POR ENTERO LOTERIA
 */
//$db->debug = true;

$res = sql("	SELECT
				  	POSICION,
				  	LPAD(NUMERO, 5, 0) AS BILLETE
				FROM T_EXTRACCION
				WHERE SORTEO     = ?
					AND ID_JUEGO = ?
					and posicion = 21", array($_SESSION['sorteo'], $_SESSION['id_juego']));

if ($res->RecordCount() > 0) {
    $localidad = array();
    while ($row = siguiente($res)) {
        $billete  = $row->BILLETE;
        $posicion = $row->POSICION;

        // if($row->DESCRIPCION_AGENCIA=='VENTA MOSTRADOR'){
        //     $agencia   = 'VENTA MOSTRADOR';
        //     $provincia = $row->PROVINCIA;
        //     $sucursal = ($row->DESCRIPCION_SUCURSAL == NULL) ? '' : $row->DESCRIPCION_SUCURSAL;
        //     $localidad = ($row->PROVINCIA == NULL) ? '' : $row->PROVINCIA;
        // }else if($row->DESCRIPCION_AGENCIA=='VENTA CONTADO'){
        //     $agencia   = 'VENTA MOSTRADOR';
        //     $provincia = $row->PROVINCIA;
        //     $sucursal = $row->DESCRIPCION_SUCURSAL;
        //     $localidad = $row->LOCALIDAD.', '.$row->PROVINCIA;
        // }else{
        //     $agencia   = str_pad($row->ID_AGENCIA, 5, "0", STR_PAD_LEFT).' - '.$row->DESCRIPCION_AGENCIA;
        //     $localidad = $row->LOCALIDAD;
        //     $sucursal = $row->DESCRIPCION_SUCURSAL;
        // }
    }

    $retorno['billetesZona4'][] = array('numero' => $billete,
        'posicion'                                   => $posicion,
        // 'agencia'  => $agencia,
        // 'localidad' => $localidad,
        // 'sucursal' => $sucursal
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
//         "zonaMostrando" : "zona1",

//         "billetesZona1" :     [
//                                 { "numero" : "23455", "posicion" : "01" },
//                                 { "numero" : "12345", "posicion" : "02" },
//                                 { "numero" : "02345", "posicion" : "03" }
//                             ],

//         "billetesZona2" :     [
//                                 { "numero" : "99999", "posicion" : "01", "fraccion" : "11" },
//                                 { "numero" : "12345", "posicion" : "02", "fraccion" : "07" },
//                                 { "numero" : "12345", "posicion" : "03", "fraccion" : "15" }
//                             ],

//         "billetesZona3" :     [
//                                 { "numero" : "12345", "posicion" : "01", "nro_agen" : "00001", "localidad" : "San Antonio de Litin, Córdoba" }
//                             ]
//     }';
