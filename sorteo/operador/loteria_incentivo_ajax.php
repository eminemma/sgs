<?php 
@session_start();
include_once dirname(__FILE__).'/../../db.php';
include_once dirname(__FILE__).'/../../librerias/alambre/funcion.inc.php';
/*error_reporting(E_ALL);
ini_set('display_errors',1);*/

$accion=isset($_POST['accion']) ? $_POST['accion'] : '';
$validar=isset($_POST['validar']) ? $_POST['validar'] : '';
$juego=isset($_POST['juego']) ? $_POST['juego'] : '';



/*
$db->debug= true;
var_dump($_SESSION);
die();
*/

/**
Control de ganadores cargados
*/
if($accion=='control_ingreso'){
	conectar_db();
	ComenzarTransaccion($db);
	//$db->debug=true;
	
	$incentivo=isset($_POST['incentivo']) ? $_POST['incentivo'] : '';
	$incentivo_b=isset($_POST['incentivo_b']) ? $_POST['incentivo_b'] : '';
	$sorteo 	=$_SESSION['sorteo'];
	$id_juego 	=$_SESSION['id_juego'];
	$mensaje='';
	
	//busco los valores del incentivo
	try{
		$rs_incentivo = sql("SELECT DESCRIPCION,IMPORTE
							FROM SGS.T_INCENTIVOS
							WHERE ID_JUEGO=?
							AND SORTEO=?
							AND ID_INCENTIVO=?",array($id_juego,$sorteo,$incentivo));
			
	}catch  (exception $e){ 
		$mensaje=array("mensaje"=>"Error al buscar el incentivo: ".$db->ErrorMsg(),"tipo"=>"error");	
	}

	$row_incentivo= siguiente($rs_incentivo);
	$descripcion_incentivo=$row_incentivo->DESCRIPCION;
	$importe_incentivo=$row_incentivo->IMPORTE;

	//Verifico si se ha sorteado el incentivo		
	try{
		$rs_sorteado = sql("SELECT COUNT(*) AS CANTIDAD
							FROM SGS.T_INCENTIVOS_GANADORES
							WHERE ID_INCENTIVO=?
							AND ID_JUEGO=?
							AND SORTEO=?",array($incentivo,$id_juego,$sorteo));
			
	}catch  (exception $e){ 
		$mensaje=array("mensaje"=>"Error al buscar si existen ganadores ya sorteados: ".$db->ErrorMsg(),"tipo"=>"error");	
	}

	
	$row_sorteado= siguiente($rs_sorteado);
		
	if($row_sorteado->CANTIDAD==0){

		//$stmt = $db->PrepareSP("BEGIN SGS.PR_BUSCAR_GANADOR_INCENTIVO_X(:a1, :a2, :a3); END;");
		$stmt = $db->PrepareSP("BEGIN SGS.PR_BUSCAR_GANADOR_INCENTIVO_XY(:a1, :a2, :a3, :a4); END;");
		$db->InParameter($stmt, $id_juego, 'a1');
		$db->InParameter($stmt, $sorteo, 'a2');
		$db->InParameter($stmt, $incentivo, 'a3');
		$db->InParameter($stmt, $incentivo_b, 'a4');
		$ok = $db->Execute($stmt);
				
		if (!$ok)
			$mensaje=array("mensaje"=>"Error al buscar el aleatorio: ".$db->ErrorMsg(),"tipo"=>"error");
	}else
		$mensaje=array("mensaje"=>"El Incentivo ya se ha sorteado","tipo"=>"error");

	FinalizarTransaccion($db);
	header('Content-Type: application/json');	
	echo json_encode($mensaje);		
}

/**
Control ganadores
*/
if($accion=='control_ganador' && $juego=='incentivo'){
	
	$id_juego = $_SESSION['id_juego'];
	$sorteo   = $_SESSION['sorteo'];
	/*
	echo "SELECT I.ID_INCENTIVO,NVL(G.ALEATORIO,0) SORTEADO
									FROM SGS.T_INCENTIVOS_GANADORES G,SGS.T_INCENTIVOS I
									WHERE I.ID_JUEGO = G.ID_JUEGO(+)
									AND I.ID_INCENTIVO = G.ID_INCENTIVO(+)
									AND I.SORTEO = G.SORTEO(+)
									AND I.ID_JUEGO=$id_juego
									AND I.SORTEO=$sorteo";
	*/
	$i=0;
	try{
		$rs_incentivo=sql("SELECT I.ID_INCENTIVO,NVL(G.ALEATORIO,0) SORTEADO
									FROM SGS.T_INCENTIVOS_GANADORES G,SGS.T_INCENTIVOS I
									WHERE I.ID_JUEGO = G.ID_JUEGO(+)
									AND I.ID_INCENTIVO = G.ID_INCENTIVO(+)
									AND I.SORTEO = G.SORTEO(+)
									AND I.ID_JUEGO=?
									AND I.SORTEO=?  order by sorteado desc",array($id_juego,$sorteo));
		
		$resultado_2 = array();
		
		while($row_incentivo= siguiente($rs_incentivo)){
			$resultado[$i]=array("idIncentivo"=>$row_incentivo->ID_INCENTIVO,"sorteado"=>$row_incentivo->SORTEADO);
			$i+=1;
			$resultado_2[] = array("idIncentivo"=>$row_incentivo->ID_INCENTIVO,"sorteado"=>$row_incentivo->SORTEADO);
			
		}
				
		header('Content-Type: application/json');
		echo json_encode($resultado_2);
		
	}catch  (exception $e){ 
		$mensaje=array("mensaje"=>"Error: ".$db->ErrorMsg(),"tipo"=>"error");				
	}
}

/**
Habilitar pantallas segun el juego (FUNCIONANDO)
*/
if($accion=='mostrar_extracto'){
	
	$tipo=isset($_POST['tipo']) ? $_POST['tipo'] : '';
	$id_juego=$_SESSION['id_juego'];
	try{
		conectar_db();
		$juego=4;

		if($tipo=='ver_alcanzan')$id_incentivo=1;
		else if($tipo=='ver_superan_hasta_10')$id_incentivo=2;
		else if($tipo=='ver_superan_10_hasta_20')$id_incentivo=3;
		else if($tipo=='ver_superan_20')$id_incentivo=4;
		
		// nvo invierno 2014
		else if($tipo=='ver_250_omas')$id_incentivo=5;
		else if($tipo=='ver_100_249')$id_incentivo=6;
		else if($tipo=='ver_20_99')$id_incentivo=7;
			
		// raspaguita 82
		else if($tipo=='ver_kit_mundial')$id_incentivo=8;
		
		// incentivo gordo 2014
		else if($tipo=='ver_401_mas') $id_incentivo=9;
		
		else if($tipo=='ver_201_400_1') $id_incentivo=10;
		else if($tipo=='ver_201_400_2') $id_incentivo=11;
		else if($tipo=='ver_201_400_3') $id_incentivo=12;
		else if($tipo=='ver_201_400_4') $id_incentivo=13;
		else if($tipo=='ver_201_400_5') $id_incentivo=14;
		//else if($tipo=='ver_201_400_6') $id_incentivo=15;
		
		
		
		# TODO: FALTAN LOS DEMAS SORTEOS
		else if($tipo=='ver_121_200_1') $id_incentivo=15;
		else if($tipo=='ver_121_200_2') $id_incentivo=16;
		else if($tipo=='ver_121_200_3') $id_incentivo=17;
		else if($tipo=='ver_121_200_4') $id_incentivo=18;
				
		else if($tipo=='ver_30_120_1') $id_incentivo=19;
		else if($tipo=='ver_30_120_2') $id_incentivo=20;
		else if($tipo=='ver_30_120_3') $id_incentivo=21;
		else if($tipo=='ver_30_120_4') $id_incentivo=22;
		else if($tipo=='ver_30_120_5') $id_incentivo=23;
		else if($tipo=='ver_30_120_6') $id_incentivo=24;
		else if($tipo=='ver_30_120_7') $id_incentivo=25;
		else if($tipo=='ver_30_120_8') $id_incentivo=26;
		else if($tipo=='ver_30_120_9') $id_incentivo=27;
		else if($tipo=='ver_30_120_10') $id_incentivo=28;
		else if($tipo=='ver_30_120_11') $id_incentivo=29;
		else if($tipo=='ver_30_120_12') $id_incentivo=30;
		else if($tipo=='ver_30_120_13') $id_incentivo=31;
		else if($tipo=='ver_30_120_14') $id_incentivo=32;
		else if($tipo=='ver_30_120_15') $id_incentivo=33;
		
		
		
		else if($tipo=='ver_vuelta_heli_1') $id_incentivo=34;
		else if($tipo=='ver_vuelta_heli_2') $id_incentivo=35;
		else if($tipo=='ver_vuelta_auto') $id_incentivo=36;
		else if($tipo=='ver_carpa_1') $id_incentivo=37;
		else if($tipo=='ver_carpa_2') $id_incentivo=38;
		else if($tipo=='ver_carpa_3') $id_incentivo=39;
		
		else if($tipo=='ver_carpa_4') $id_incentivo=40;
		else if($tipo=='ver_carpa_5') $id_incentivo=41;
		else if($tipo=='ver_carpa_6') $id_incentivo=42;
		
		else if($tipo=='ver_carpa_7') $id_incentivo=43;
		else if($tipo=='ver_carpa_8') $id_incentivo=44;
		else if($tipo=='ver_carpa_9') $id_incentivo=45;
		else if($tipo=='ver_carpa_10') $id_incentivo=46;
		else if($tipo=='ver_carpa_11') $id_incentivo=47;
		else if($tipo=='ver_carpa_12') $id_incentivo=48;
		else if($tipo=='ver_carpa_13') $id_incentivo=49;
		else if($tipo=='ver_carpa_14') $id_incentivo=50;
		else if($tipo=='ver_carpa_15') $id_incentivo=51;
		else if($tipo=='ver_carpa_16') $id_incentivo=52;
		else if($tipo=='ver_carpa_17') $id_incentivo=53;
		else if($tipo=='ver_carpa_18') $id_incentivo=54;
		else if($tipo=='ver_carpa_19') $id_incentivo=55;
		else if($tipo=='ver_carpa_20') $id_incentivo=56;
		else if($tipo=='ver_carpa_21') $id_incentivo=57;
		else if($tipo=='ver_carpa_22') $id_incentivo=58;
		else if($tipo=='ver_carpa_23') $id_incentivo=59;
		else if($tipo=='ver_carpa_24') $id_incentivo=60;
		else if($tipo=='ver_carpa_25') $id_incentivo=61;
		
		else if($tipo=='ver_62') $id_incentivo=62;else if($tipo=='ver_64') $id_incentivo=64;else if($tipo=='ver_66') $id_incentivo=66; else if($tipo=='ver_67') $id_incentivo=67; else if($tipo=='ver_68') $id_incentivo=68; else if($tipo=='ver_69') $id_incentivo=69; else if($tipo=='ver_70') $id_incentivo=70; else if($tipo=='ver_71') $id_incentivo=71; else if($tipo=='ver_72') $id_incentivo=72; else if($tipo=='ver_73') $id_incentivo=73; else if($tipo=='ver_74') $id_incentivo=74; else if($tipo=='ver_75') $id_incentivo=75; else if($tipo=='ver_76') $id_incentivo=76; else if($tipo=='ver_77') $id_incentivo=77; else if($tipo=='ver_78') $id_incentivo=78; else if($tipo=='ver_79') $id_incentivo=79; else if($tipo=='ver_80') $id_incentivo=80; else if($tipo=='ver_81') $id_incentivo=81; else if($tipo=='ver_82') $id_incentivo=82; else if($tipo=='ver_83') $id_incentivo=83; else if($tipo=='ver_84') $id_incentivo=84; else if($tipo=='ver_85') $id_incentivo=85; else if($tipo=='ver_86') $id_incentivo=86; else if($tipo=='ver_87') $id_incentivo=87; else if($tipo=='ver_88') $id_incentivo=88; else if($tipo=='ver_89') $id_incentivo=89; 
		else if($tipo=='ver_90') $id_incentivo=90;
		
		
		
		else if($tipo=='ver_91') $id_incentivo=91;
		else if($tipo=='ver_92') $id_incentivo=92;
		else if($tipo=='ver_93') $id_incentivo=93;
		else if($tipo=='ver_94') $id_incentivo=94;
		else if($tipo=='ver_95') $id_incentivo=95;
		
		else if($tipo=='ver_96') $id_incentivo=96;
		else if($tipo=='ver_97') $id_incentivo=97;
		else if($tipo=='ver_98') $id_incentivo=98;
		else if($tipo=='ver_99') $id_incentivo=99;
		else if($tipo=='ver_100') $id_incentivo=100;
		
		else if($tipo=='ver_101') $id_incentivo=101;
		else if($tipo=='ver_102') $id_incentivo=102;
		else if($tipo=='ver_103') $id_incentivo=103;
		else if($tipo=='ver_104') $id_incentivo=104;
		else if($tipo=='ver_105') $id_incentivo=105;
		
		else if($tipo=='ver_106') $id_incentivo=106;
		else if($tipo=='ver_107') $id_incentivo=107;
		else if($tipo=='ver_108') $id_incentivo=108;
		else if($tipo=='ver_109') $id_incentivo=109;
		else if($tipo=='ver_110') $id_incentivo=110;
		
		
		else if($tipo=='ver_111') $id_incentivo=111;
		else if($tipo=='ver_112') $id_incentivo=112;
		else if($tipo=='ver_113') $id_incentivo=113;
		else if($tipo=='ver_114') $id_incentivo=114;
		else if($tipo=='ver_115') $id_incentivo=115;
		else if($tipo=='ver_116') $id_incentivo=116;
		else if($tipo=='ver_117') $id_incentivo=117;
		else if($tipo=='ver_118') $id_incentivo=118;
		else if($tipo=='ver_119') $id_incentivo=119;
		else if($tipo=='ver_120') $id_incentivo=120;
		else if($tipo=='ver_121') $id_incentivo=121;
		else if($tipo=='ver_122') $id_incentivo=122;
		else if($tipo=='ver_123') $id_incentivo=123;
		else if($tipo=='ver_124') $id_incentivo=124;
		else if($tipo=='ver_125') $id_incentivo=125;
		else if($tipo=='ver_126') $id_incentivo=126;
		else if($tipo=='ver_127') $id_incentivo=127;
		else if($tipo=='ver_128') $id_incentivo=128;
		else if($tipo=='ver_129') $id_incentivo=129;
		else if($tipo=='ver_130') $id_incentivo=130; 
		
		
		else if($tipo=='ver_131') $id_incentivo=131; 
		else if($tipo=='ver_132') $id_incentivo=132; 
		else if($tipo=='ver_133') $id_incentivo=133; 
		else if($tipo=='ver_134') $id_incentivo=134; 
		else if($tipo=='ver_135') $id_incentivo=135;
		
		else if($tipo=='ver_136') $id_incentivo=136; 
		else if($tipo=='ver_137') $id_incentivo=137; 
		else if($tipo=='ver_138') $id_incentivo=138; 
		else if($tipo=='ver_139') $id_incentivo=139; 
		else if($tipo=='ver_140') $id_incentivo=140; 
		
		else if($tipo=='ver_141') $id_incentivo=141; 
		else if($tipo=='ver_142') $id_incentivo=142; 
		else if($tipo=='ver_143') $id_incentivo=143; 
		else if($tipo=='ver_144') $id_incentivo=144; 
		else if($tipo=='ver_145') $id_incentivo=145; 
		
		else if($tipo=='ver_146') $id_incentivo=146; 
		else if($tipo=='ver_147') $id_incentivo=147; 
		else if($tipo=='ver_148') $id_incentivo=148; 
		else if($tipo=='ver_149') $id_incentivo=149; 
		else if($tipo=='ver_150') $id_incentivo=150; 
		
		else if($tipo=='ver_151') $id_incentivo=151; 
		else if($tipo=='ver_152') $id_incentivo=152;
		else if($tipo=='ver_153') $id_incentivo=153;
		else if($tipo=='ver_154') $id_incentivo=154;
		else if($tipo=='ver_155') $id_incentivo=155;
		else if($tipo=='ver_156') $id_incentivo=156;
		else if($tipo=='ver_157') $id_incentivo=157;
		else if($tipo=='ver_158') $id_incentivo=158;
		else if($tipo=='ver_159') $id_incentivo=159;
		else if($tipo=='ver_160') $id_incentivo=160;
		else if($tipo=='ver_161') $id_incentivo=161;
		else if($tipo=='ver_162') $id_incentivo=162;
		else if($tipo=='ver_163') $id_incentivo=163;
		else if($tipo=='ver_164') $id_incentivo=164;
		else if($tipo=='ver_165') $id_incentivo=165;
		else if($tipo=='ver_166') $id_incentivo=166;
		else if($tipo=='ver_167') $id_incentivo=167;
		else if($tipo=='ver_168') $id_incentivo=168;
		else if($tipo=='ver_169') $id_incentivo=169;
		else if($tipo=='ver_170') $id_incentivo=170; 
		
		// echo "UPDATE SGS.t_parametro_compartido 
						// SET VALOR=$id_incentivo 
					// WHERE ID_JUEGO=$id_juego
					  // AND PARAMETRO='INCENTIVO_MOSTRANDO'";
		
		ComenzarTransaccion($db);
			sql("UPDATE SGS.t_parametro_compartido 
						SET VALOR=? 
					WHERE ID_JUEGO=?
					  AND PARAMETRO='INCENTIVO_MOSTRANDO'",array($id_incentivo,$id_juego));									  
		$pantalla='';
		FinalizarTransaccion($db);
		$juego='';
		
		
		// incentivo gordo de invierno 2014
		if($tipo=='ver_alcanzan') $juego='Alcanzan Objetivo';
		else if($tipo=='ver_superan_hasta_10') $juego='Superan Hasta 10%';
		else if($tipo=='ver_superan_10_hasta_20') $juego='Superan 10% Hasta 20%';
		else if($tipo=='ver_superan_20') $juego='Superan 20%';
		
		// nvo
		else if($tipo=='ver_250_omas') $juego='250 o mas';
		else if($tipo=='ver_100_249') $juego='100 a 149';
		else if($tipo=='ver_20_99')  $juego='20 a 99';
			
		// raspaguita
		else if($tipo=='ver_kit_mundial') $juego='Kit Mundial';
		
		
		
		// incentivo gordo 2014
		if($tipo=='ver_401_mas')$juego='401 o más';
		
		else if($tipo=='ver_201_400_1' || $tipo=='ver_201_400_2' || $tipo=='ver_201_400_3' || $tipo=='ver_201_400_4' || $tipo=='ver_201_400_5') $juego='201 hasta 400';
		
		# TODO: FALTAN LOS DEMAS SORTEOS
		else if($tipo=='ver_121_200_1' || $tipo=='ver_121_200_2' || $tipo=='ver_121_200_3' || $tipo=='ver_121_200_4') $juego='121 hasta 200';
			
			
		else if(
			$tipo=='ver_30_120_1'
			|| $tipo=='ver_30_120_2'
			|| $tipo=='ver_30_120_3'
			|| $tipo=='ver_30_120_4'
			|| $tipo=='ver_30_120_5'
			|| $tipo=='ver_30_120_6'
			|| $tipo=='ver_30_120_7'
			|| $tipo=='ver_30_120_8'
			|| $tipo=='ver_30_120_9'
			|| $tipo=='ver_30_120_10'
			|| $tipo=='ver_30_120_11'
			|| $tipo=='ver_30_120_12'
			|| $tipo=='ver_30_120_13'
			|| $tipo=='ver_30_120_14'
			|| $tipo=='ver_30_120_15'
			)
			$juego='030 hasta 120';
			
			
		if($tipo=='ver_vuelta_heli_1' || $tipo=='ver_vuelta_heli_2')$juego='Una vuelta en Helicóptero';
		if($tipo=='ver_vuelta_auto')$juego='Una vuelta en Auto 0';
		if(
				$tipo=='ver_carpa_1'  || 	$tipo=='ver_carpa_2'  
				|| 	$tipo=='ver_carpa_3'  
				|| 	$tipo=='ver_carpa_4'   
				|| 	$tipo=='ver_carpa_5'    
				|| 	$tipo=='ver_carpa_6' 
				|| $tipo=='ver_carpa_7'
				|| $tipo=='ver_carpa_8'
				|| $tipo=='ver_carpa_9'
				|| $tipo=='ver_carpa_10'
				|| $tipo=='ver_carpa_11'
				|| $tipo=='ver_carpa_12'
				|| $tipo=='ver_carpa_13'
				|| $tipo=='ver_carpa_14'
				|| $tipo=='ver_carpa_15'
				|| $tipo=='ver_carpa_16'
				|| $tipo=='ver_carpa_17'
				|| $tipo=='ver_carpa_18'
				|| $tipo=='ver_carpa_19'
				|| $tipo=='ver_carpa_20'
				|| $tipo=='ver_carpa_21'
				|| $tipo=='ver_carpa_22'
				|| $tipo=='ver_carpa_23'
				|| $tipo=='ver_carpa_24'
				|| $tipo=='ver_carpa_25' 
				
		)$juego='Dos entradas a carpa VIP de Lotería de Córdoba';
		
		
		if($tipo=='ver_62'  || 	$tipo=='ver_64')$juego='$ 20.000.-';
		if($tipo=='ver_66' || $tipo=='ver_67' || $tipo=='ver_68' || $tipo=='ver_69' || $tipo=='ver_70')$juego='$ 7.000.-';
		if($tipo=='ver_71' || $tipo=='ver_72' || $tipo=='ver_73' || $tipo=='ver_74' || $tipo=='ver_75')$juego='$ 4.000.-';
		if($tipo=='ver_76' || $tipo=='ver_77' || $tipo=='ver_78' || $tipo=='ver_79' || $tipo=='ver_80' || $tipo=='ver_81' || $tipo=='ver_82' || $tipo=='ver_83' || $tipo=='ver_84' || $tipo=='ver_85' || $tipo=='ver_86' || $tipo=='ver_87' || $tipo=='ver_88' || $tipo=='ver_89' || $tipo=='ver_90')$juego='$ 2.000.-';
		
		
		if($tipo=='ver_91'  || 	$tipo=='ver_92' || 	$tipo=='ver_93' || 	$tipo=='ver_94' || 	$tipo=='ver_95')$juego='$ 15.000.-';
		if($tipo=='ver_96'  || 	$tipo=='ver_97' || 	$tipo=='ver_98' || 	$tipo=='ver_99' || 	$tipo=='ver_100')$juego='$ 10.000.-';
		if($tipo=='ver_101'  || 	$tipo=='ver_102' || 	$tipo=='ver_103' || 	$tipo=='ver_104' || 	$tipo=='ver_105')$juego='$ 5.000.-';
		if($tipo=='ver_106'  || 	$tipo=='ver_107' || 	$tipo=='ver_108' || 	$tipo=='ver_109' || 	$tipo=='ver_110')$juego='$ 2.000.-';
		
		
		
		if($tipo=='ver_151'  || 	$tipo=='ver_152' || 	$tipo=='ver_153' || 	$tipo=='ver_154' || 	$tipo=='ver_155')$juego='$ 22.000.-';
		if($tipo=='ver_156'  || 	$tipo=='ver_157' || 	$tipo=='ver_158' || 	$tipo=='ver_159' || 	$tipo=='ver_160')$juego='$ 15.000.-';
		if($tipo=='ver_161'  || 	$tipo=='ver_162' || 	$tipo=='ver_163' || 	$tipo=='ver_164' || 	$tipo=='ver_165')$juego='$ 8.000.-';
		if($tipo=='ver_166'  || 	$tipo=='ver_167' || 	$tipo=='ver_168' || 	$tipo=='ver_169' || 	$tipo=='ver_170')$juego='$ 5.000.-';
		
		
		
		
		$mensaje=array("mensaje"=>"Se va a mostrar el incentivo ".$juego,"tipo"=>"info");	
		
		
		
	}catch  (exception $e){ 
		$mensaje=array("mensaje"=>"Error: ".$db->ErrorMsg(),"tipo"=>"error");				
	}

	header('Content-Type: application/json');
	echo json_encode($mensaje);
}

/**
Eliminar extraccion sorteo
*/
if($accion=='eliminar'){
	
	$id_incentivo=isset($_POST['id_incentivo']) ? $_POST['id_incentivo'] : '';
	//$db->debug=true;
	$sorteo 	=$_SESSION['sorteo'];
	$id_juego 	=$_SESSION['id_juego'];
	conectar_db();
	//$db->debug=true;
	ComenzarTransaccion($db);
	try{
		sql('DELETE
				FROM SGS.T_INCENTIVOS_GANADORES
				WHERE ID_INCENTIVO = ?
				AND ID_JUEGO=?
				AND SORTEO=?',array($id_incentivo,$id_juego,$sorteo));

		$mensaje=array("mensaje"=>"Se Elimino el Incentivo ".$id_incentivo,"tipo"=>"error");	
	}catch  (exception $e){ 
		$mensaje=array("mensaje"=>"Error al eliminar: ".$db->ErrorMsg(),"tipo"=>"error");		
	}	
	FinalizarTransaccion($db);
	header('Content-Type: application/json');
	echo json_encode($mensaje);
}
?>