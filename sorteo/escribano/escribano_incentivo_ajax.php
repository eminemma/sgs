<?php
@session_start();

include_once dirname(__FILE__).'/../../db.php';
include_once dirname(__FILE__).'/../../librerias/alambre/funcion.inc.php';



//echo "SELECT VALOR FROM sgs.T_PARAMETRO_COMPARTIDO WHERE ID_JUEGO = ".$_SESSION['id_juego']." AND PARAMETRO = 'INCENTIVO_MOSTRANDO'"; 

$res = sql("SELECT
				VALOR
			FROM
				sgs.T_PARAMETRO_COMPARTIDO
			WHERE
				ID_JUEGO = ?
				AND PARAMETRO = 'INCENTIVO_MOSTRANDO'",array($_SESSION['id_juego']));

$row = siguiente($res);
$valor=$row->VALOR;

			/*
			$res = sql("SELECT I.DESCRIPCION,MAX(A.HASTA) AS MAXIMO
			FROM
				SGS.T_INCENTIVOS I,T_INCENTIVOS_AGENCIAS A
			WHERE I.ID_INCENTIVO = A.ID_INCENTIVO
				AND I.ID_JUEGO = ?
				AND I.SORTEO = ?
				AND I.ID_INCENTIVO=?
        	GROUP BY I.DESCRIPCION",
			array($_SESSION['id_juego'],$_SESSION['sorteo'],$valor));
			*/
			/*
			echo "SELECT I.DESCRIPCION,MAX(A.HASTA) AS MAXIMO, I.IMPORTE
			FROM
				SGS.T_INCENTIVOS I,T_INCENTIVOS_AGENCIAS A
			WHERE I.ID_INCENTIVO = A.ID_INCENTIVO
				AND I.ID_JUEGO = ".$_SESSION['id_juego']."
				AND I.SORTEO = ".$_SESSION['sorteo']."
				AND I.ID_INCENTIVO=$valor
        	GROUP BY I.DESCRIPCION, I.IMPORTE ";
			*/
			
			$valor_b = $valor;
			/*
			if($valor > 61 && $valor < 65)$valor_b = 62;
			if($valor > 64 && $valor < 71)$valor_b = 66;
			if($valor > 70 && $valor < 76)$valor_b = 71;
			if($valor > 75 && $valor < 91)$valor_b = 76;
			*/
			
			if($valor > 90 && $valor < 96)$valor_b = 91;
			if($valor > 95 && $valor < 101)$valor_b = 96;
			if($valor > 100 && $valor < 106)$valor_b = 101;
			if($valor > 105 && $valor < 111)$valor_b = 106;
			
			
			if($valor > 110 && $valor < 116)$valor_b = 111;
			if($valor > 115 && $valor < 121)$valor_b = 116;
			if($valor > 120 && $valor < 126)$valor_b = 121;
			if($valor > 125 && $valor < 131)$valor_b = 126;
			
			
			//rangos incentivo navidad 2016
			if($valor >= 131 && $valor <= 135)$valor_b = 131;
			if($valor >= 136 && $valor <= 140)$valor_b = 140;
			if($valor >= 141 && $valor <= 140)$valor_b = 145;
			if($valor >= 146 && $valor <= 150)$valor_b = 150;
			
			
			
			if($valor >= 151 && $valor <= 155)$valor_b = 151;
			if($valor >= 156 && $valor <= 160)$valor_b = 156;
			if($valor >= 161 && $valor <= 165)$valor_b = 161;
			if($valor >= 166 && $valor <= 170)$valor_b = 166;
			
			
			
			$res = sql("SELECT I.DESCRIPCION,MAX(A.HASTA) AS MAXIMO, I.IMPORTE
			FROM
				SGS.T_INCENTIVOS I,T_INCENTIVOS_AGENCIAS A
			WHERE I.ID_INCENTIVO = A.ID_INCENTIVO
				AND I.ID_JUEGO = ?
				AND I.SORTEO = ?
				AND I.ID_INCENTIVO=?
        	GROUP BY I.DESCRIPCION, I.IMPORTE ",
			array($_SESSION['id_juego'],$_SESSION['sorteo'],$valor_b));

$row = siguiente($res);
$descIncentivo= '   Incentivo $ '.$row->IMPORTE. '  - ' .$row->DESCRIPCION   ;	//. ' fracciones vendidas'
//$descIncentivo= '   Incentivo  ' .$row->DESCRIPCION ;
//$descIncentivo= '   Kit Mundial (Sillon - TV Led - Frigobar)  ';
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

/*
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
*/



$res = sql("
SELECT 
	LPAD(G.ALEATORIO,5,'0') AS ALEATORIO,A.ID_SUCURSAL,UPPER(A.DESCRIPCION_SUCURSAL) AS
	 DESCRIPCION_SUCURSAL,LPAD(A.ID_AGENCIA,5,'0') AS ID_AGENCIA,UPPER(A.DESCRIPCION_AGENCIA) AS DESCRIPCION_AGENCIA
	,I.IMPORTE,A.LOCALIDAD
FROM 
	SGS.T_INCENTIVOS_GANADORES G
	,SGS.T_INCENTIVOS_AGENCIAS A
	,SGS.T_INCENTIVOS I
WHERE 
	G.ID_AGENCIA = A.ID_AGENCIA
	AND G.ID_SUCURSAL = A.ID_SUCURSAL
	AND I.ID_INCENTIVO = G.ID_INCENTIVO
	AND I.ID_JUEGO = G.ID_JUEGO
	AND I.SORTEO = G.SORTEO
	AND G.ID_JUEGO=?
	AND G.SORTEO=?
	AND G.ID_INCENTIVO=?
	AND A.ID_INCENTIVO=?",
array($_SESSION['id_juego'], $_SESSION['sorteo'],$valor,$valor_b));




while($row = siguiente($res)){
	$retorno['datosIncentivo'][] = array('aleatorio' => $row->ALEATORIO, 
										'id_sucursal' => $row->ID_SUCURSAL, 
										'desc_sucursal' => $row->DESCRIPCION_SUCURSAL, 
										'id_agencia' => $row->ID_AGENCIA, 
										'desc_agencia' => $row->DESCRIPCION_AGENCIA, 
										'importe' => $row->IMPORTE,
										'localidad' => $row->LOCALIDAD);
}

echo json_encode($retorno);
exit;