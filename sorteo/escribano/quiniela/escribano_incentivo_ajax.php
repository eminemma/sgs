<?php
@session_start();
include_once dirname(__FILE__).'/../../db.php';
include_once dirname(__FILE__).'/../../librerias/alambre/funcion.inc.php';

//$db->debug=true;

/*$retorno = array(
					'incentivoMostrando' => 1,
					'descIncentivo' => 'Lalalalalala',
					'datosIncentivo' => array()
				);
echo json_encode($retorno);
exit;*/


$res = sql("SELECT
				VALOR
			FROM
				sgs.T_PARAMETRO_COMPARTIDO
			WHERE
				ID_JUEGO = ?
				AND PARAMETRO = 'INCENTIVO_MOSTRANDO'",array($_SESSION['id_juego']));

$row = siguiente($res);
$valor=$row->VALOR;

//$valor=1;

$res = sql("SELECT I.DESCRIPCION,MAX(A.HASTA) AS MAXIMO
			FROM
				SGS.T_INCENTIVOS I,T_INCENTIVOS_AGENCIAS A
			WHERE I.ID_INCENTIVO = A.ID_INCENTIVO
				AND I.ID_JUEGO = ?
				AND I.SORTEO = ?
				AND I.ID_INCENTIVO=?
        	GROUP BY I.DESCRIPCION",
			array($_SESSION['id_juego'],$_SESSION['sorteo'],$valor));

$row = siguiente($res);
$descIncentivo=$row->DESCRIPCION;
$maximoAleatorio=$row->MAXIMO;

$retorno = array(
					'incentivoMostrando' => (int)$valor,
					'descIncentivo' => $descIncentivo,
					'maximoAleatorio' => $maximoAleatorio,
					'datosIncentivo' => array()
				);
/**
	BUSCAMOS LOS DATOS DEL GANADOR DEL INCENTIVO
*/
//$db->debug=true;
$res = sql("SELECT LPAD(G.ALEATORIO,5,'0') AS ALEATORIO,A.ID_SUCURSAL,UPPER(A.DESCRIPCION_SUCURSAL) AS DESCRIPCION_SUCURSAL,LPAD(A.ID_AGENCIA,5,'0') AS ID_AGENCIA,UPPER(A.DESCRIPCION_AGENCIA) AS DESCRIPCION_AGENCIA,I.IMPORTE,A.LOCALIDAD
			FROM SGS.T_INCENTIVOS_GANADORES G,SGS.T_INCENTIVOS_AGENCIAS A,SGS.T_INCENTIVOS I
			WHERE G.ID_AGENCIA = A.ID_AGENCIA
			AND G.ID_SUCURSAL = A.ID_SUCURSAL
			AND G.ID_INCENTIVO = A.ID_INCENTIVO
			AND I.ID_JUEGO = G.ID_JUEGO
			AND I.ID_INCENTIVO = G.ID_INCENTIVO
			AND I.SORTEO = G.SORTEO
			AND G.ID_JUEGO=?
			AND G.SORTEO=?
			AND G.ID_INCENTIVO=?",
			array($_SESSION['id_juego'], $_SESSION['sorteo'],$valor));

while($row = siguiente($res)){
	$retorno['datosIncentivo'][] = array('aleatorio' => $row->ALEATORIO, 
										'id_sucursal' => $row->ID_SUCURSAL, 
										'desc_sucursal' => $row->DESCRIPCION_SUCURSAL, 
										'id_agencia' => $row->ID_AGENCIA, 
										'desc_agencia' => $row->DESCRIPCION_AGENCIA, 
										'importe' => $row->IMPORTE,
										'localidad' => $row->LOCALIDAD);
}
/*$retorno['datosIncentivo'][] = array('aleatorio' => '99999', 
										'id_sucursal' => '1', 
										'desc_sucursal' => 'Prueba', 
										'id_agencia' => 'Prueba', 
										'desc_agencia' => 'Prueba', 
										'importe' => '99999');*/

//var_dump($retorno['datosIncentivo']);
echo json_encode($retorno);
exit;