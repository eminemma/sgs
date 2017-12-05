<?php 
session_start();
include_once dirname(__FILE__).'/../../mensajes.php';
if(!isset($_SESSION['sorteo'])) {
  error('Es necesario seleccionar un sorteo');
  die();
}  
$protocolo = isset($_SERVER['HTTPS']) ? 'https' : 'http';

  if($_SERVER['SERVER_NAME'] == 'localhost' || $_SERVER['SERVER_NAME'] == '127.0.0.1')
    $url = $protocolo.'://'.$_SERVER['SERVER_NAME'].'/';
    //$url = $protocolo.'://'.$_SERVER['SERVER_NAME'].'/desa/';
  else if($_SERVER['SERVER_NAME'] == 'desa.loteriadecordoba.com.ar' || $_SERVER['SERVER_NAME'] == 'svn.loteriadecordoba.com.ar')
    $url = $protocolo.'://'.$_SERVER['SERVER_NAME'].'/';
  else
    $url = $protocolo.'://'.$_SERVER['SERVER_NAME'].'/app/';
?>
<!DOCTYPE html>
<html>
  <head>
    <title>SGS - Sistema de Gestion de Sorteos</title>
    <base href="<?php echo $url; ?>/sgs/">
    <link rel="stylesheet" type="text/css" href="librerias/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="css/estilo.css">
    <link rel="stylesheet" type="text/css" href="sorteo/operador/estilo_sorteador.css">
    <script type="text/javascript" src="librerias/jquery/jquery-1.10.1.js"></script>
    <script type="text/javascript" src="librerias/bootstrap/js/bootstrap.min.js"></script>
    <script type="text/javascript" src="librerias/bootstrap/js/bootstrap-fileupload.min.js"></script>
    <script type="text/javascript" src="librerias/bootstrap/js/bootstrap-datetimepicker.min.js"></script>

    <script type="text/javascript" src="sorteo/operador/funciones_incentivo.js"></script>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <?php 
    $_SESSION['juego']='LOTERIA';
    $_SESSION['tipo_usuario']='ROL_JEFE_SORTEO';
  
    if($_SESSION['tipo_usuario']<>'ROL_JEFE_SORTEO' && $_SESSION['tipo_usuario']<>'ROL_OPERADOR')
      die(' <div id="error" class="alert alert-error">
                <button type="button" class="close" onclick="$(\'.alert\').slideUp(\'slow\');">x</button>
                <div class="contenido_error">Para poder acceder al sorteo es necesario ser JEFE DE SORTEO u OPERADOR</div>
              </div>');
    ?>
    <script type="text/javascript">
      param={
              accion:'mostrar',
              juego:'incentivo'
            };
			
			
      var param;
      var intervalo2;
      var intervalo1;
      var Ajax1;
      var Ajax2;
	  
	  
      function cambiar_juego(classN){
        $('.alert').hide('slow');
        if(classN=='incentivo'){
          accion='sorteo/operador/loteria_sorteador_incentivo.php';
          param={accion:'mostrar',juego:'incentivo'};
		  
        }else if(classN=='ver_alcanzan'){
          accion='sorteo/operador/loteria_incentivo_ajax.php';
          param2={accion:'mostrar_extracto',tipo:'ver_alcanzan'};
		  
        }else if(classN=='ver_superan_hasta_10'){
          accion='sorteo/operador/loteria_incentivo_ajax.php';
          param2={accion:'mostrar_extracto',tipo:'ver_superan_hasta_10'};
		  
        }else if(classN=='ver_superan_10_hasta_20'){
          accion='sorteo/operador/loteria_incentivo_ajax.php';
          param2={accion:'mostrar_extracto',tipo:'ver_superan_10_hasta_20'};
		  
        }else if(classN=='ver_superan_20'){
          accion='sorteo/operador/loteria_incentivo_ajax.php';
          param2={accion:'mostrar_extracto',tipo:'ver_superan_20'};
		  
		}else if(classN=='ver_250_omas'){
          accion='sorteo/operador/loteria_incentivo_ajax.php';
          param2={accion:'mostrar_extracto',tipo:'ver_250_omas'};
        }
		else if(classN=='ver_100_249'){
          accion='sorteo/operador/loteria_incentivo_ajax.php';
          param2={accion:'mostrar_extracto',tipo:'ver_100_249'};
        }
		else if(classN=='ver_20_99'){
          accion='sorteo/operador/loteria_incentivo_ajax.php';
          param2={accion:'mostrar_extracto',tipo:'ver_20_99'};
        }
		else if(classN=='ver_kit_mundial'){
          accion='sorteo/operador/loteria_incentivo_ajax.php';
          param2={accion:'mostrar_extracto',tipo:'ver_kit_mundial'};
        }
		
		else if(classN=='ver_401_mas'){
          accion='sorteo/operador/loteria_incentivo_ajax.php';
          param2={accion:'mostrar_extracto',tipo:'ver_401_mas'};
        }
		else if(classN=='ver_201_400_1'){
          accion='sorteo/operador/loteria_incentivo_ajax.php';
          param2={accion:'mostrar_extracto',tipo:'ver_201_400_1'};
        }
		else if(classN=='ver_201_400_2'){
          accion='sorteo/operador/loteria_incentivo_ajax.php';
          param2={accion:'mostrar_extracto',tipo:'ver_201_400_2'};
        }
		else if(classN=='ver_201_400_3'){
          accion='sorteo/operador/loteria_incentivo_ajax.php';
          param2={accion:'mostrar_extracto',tipo:'ver_201_400_3'};
        }
		else if(classN=='ver_201_400_4'){
          accion='sorteo/operador/loteria_incentivo_ajax.php';
          param2={accion:'mostrar_extracto',tipo:'ver_201_400_4'};
        }
		else if(classN=='ver_201_400_5'){
          accion='sorteo/operador/loteria_incentivo_ajax.php';
          param2={accion:'mostrar_extracto',tipo:'ver_201_400_5'};
        }
		else if(classN=='ver_121_200_1'){
          accion='sorteo/operador/loteria_incentivo_ajax.php';
          param2={accion:'mostrar_extracto',tipo:'ver_121_200_1'};
        }
		else if(classN=='ver_121_200_2'){
          accion='sorteo/operador/loteria_incentivo_ajax.php';
          param2={accion:'mostrar_extracto',tipo:'ver_121_200_2'};
        }
		else if(classN=='ver_121_200_3'){
          accion='sorteo/operador/loteria_incentivo_ajax.php';
          param2={accion:'mostrar_extracto',tipo:'ver_121_200_3'};
        }
		else if(classN=='ver_121_200_4'){
          accion='sorteo/operador/loteria_incentivo_ajax.php';
          param2={accion:'mostrar_extracto',tipo:'ver_121_200_4'};
        }
		else if(classN=='ver_30_120_1'){ accion='sorteo/operador/loteria_incentivo_ajax.php'; param2={accion:'mostrar_extracto',tipo:'ver_30_120_1'}; }
		else if(classN=='ver_30_120_2'){ accion='sorteo/operador/loteria_incentivo_ajax.php'; param2={accion:'mostrar_extracto',tipo:'ver_30_120_2'}; }
		else if(classN=='ver_30_120_3'){ accion='sorteo/operador/loteria_incentivo_ajax.php'; param2={accion:'mostrar_extracto',tipo:'ver_30_120_3'}; }
		else if(classN=='ver_30_120_4'){ accion='sorteo/operador/loteria_incentivo_ajax.php'; param2={accion:'mostrar_extracto',tipo:'ver_30_120_4'}; }
		else if(classN=='ver_30_120_5'){ accion='sorteo/operador/loteria_incentivo_ajax.php'; param2={accion:'mostrar_extracto',tipo:'ver_30_120_5'}; }
		else if(classN=='ver_30_120_6'){ accion='sorteo/operador/loteria_incentivo_ajax.php'; param2={accion:'mostrar_extracto',tipo:'ver_30_120_6'}; }
		else if(classN=='ver_30_120_7'){ accion='sorteo/operador/loteria_incentivo_ajax.php'; param2={accion:'mostrar_extracto',tipo:'ver_30_120_7'}; }
		else if(classN=='ver_30_120_8'){ accion='sorteo/operador/loteria_incentivo_ajax.php'; param2={accion:'mostrar_extracto',tipo:'ver_30_120_8'}; }
		else if(classN=='ver_30_120_9'){ accion='sorteo/operador/loteria_incentivo_ajax.php'; param2={accion:'mostrar_extracto',tipo:'ver_30_120_9'}; }
		else if(classN=='ver_30_120_10'){ accion='sorteo/operador/loteria_incentivo_ajax.php'; param2={accion:'mostrar_extracto',tipo:'ver_30_120_10'}; }
		else if(classN=='ver_30_120_11'){ accion='sorteo/operador/loteria_incentivo_ajax.php'; param2={accion:'mostrar_extracto',tipo:'ver_30_120_11'}; }
		else if(classN=='ver_30_120_12'){ accion='sorteo/operador/loteria_incentivo_ajax.php'; param2={accion:'mostrar_extracto',tipo:'ver_30_120_12'}; }
		else if(classN=='ver_30_120_13'){ accion='sorteo/operador/loteria_incentivo_ajax.php'; param2={accion:'mostrar_extracto',tipo:'ver_30_120_13'}; }
		else if(classN=='ver_30_120_14'){ accion='sorteo/operador/loteria_incentivo_ajax.php'; param2={accion:'mostrar_extracto',tipo:'ver_30_120_14'}; }
		else if(classN=='ver_30_120_15'){ accion='sorteo/operador/loteria_incentivo_ajax.php'; param2={accion:'mostrar_extracto',tipo:'ver_30_120_15'}; }
		
		
		
		else if(classN=='ver_vuelta_heli_1'){ accion='sorteo/operador/loteria_incentivo_ajax.php'; param2={accion:'mostrar_extracto',tipo:'ver_vuelta_heli_1'}; }
		else if(classN=='ver_vuelta_heli_2'){ accion='sorteo/operador/loteria_incentivo_ajax.php'; param2={accion:'mostrar_extracto',tipo:'ver_vuelta_heli_2'}; }
		else if(classN=='ver_vuelta_auto'){ accion='sorteo/operador/loteria_incentivo_ajax.php'; param2={accion:'mostrar_extracto',tipo:'ver_vuelta_auto'}; }
		else if(classN=='ver_carpa_1'){ accion='sorteo/operador/loteria_incentivo_ajax.php'; param2={accion:'mostrar_extracto',tipo:'ver_carpa_1'}; }
		else if(classN=='ver_carpa_2'){ accion='sorteo/operador/loteria_incentivo_ajax.php'; param2={accion:'mostrar_extracto',tipo:'ver_carpa_2'}; }
		else if(classN=='ver_carpa_3'){ accion='sorteo/operador/loteria_incentivo_ajax.php'; param2={accion:'mostrar_extracto',tipo:'ver_carpa_3'}; }
		
		else if(classN=='ver_carpa_4'){ accion='sorteo/operador/loteria_incentivo_ajax.php'; param2={accion:'mostrar_extracto',tipo:'ver_carpa_4'}; }
		else if(classN=='ver_carpa_5'){ accion='sorteo/operador/loteria_incentivo_ajax.php'; param2={accion:'mostrar_extracto',tipo:'ver_carpa_5'}; }
		else if(classN=='ver_carpa_6'){ accion='sorteo/operador/loteria_incentivo_ajax.php'; param2={accion:'mostrar_extracto',tipo:'ver_carpa_6'}; }
		
		else if(classN=='ver_carpa_7'){ accion='sorteo/operador/loteria_incentivo_ajax.php'; param2={accion:'mostrar_extracto',tipo:'ver_carpa_7'}; }
		else if(classN=='ver_carpa_8'){ accion='sorteo/operador/loteria_incentivo_ajax.php'; param2={accion:'mostrar_extracto',tipo:'ver_carpa_8'}; }
		else if(classN=='ver_carpa_9'){ accion='sorteo/operador/loteria_incentivo_ajax.php'; param2={accion:'mostrar_extracto',tipo:'ver_carpa_9'}; }
		else if(classN=='ver_carpa_10'){ accion='sorteo/operador/loteria_incentivo_ajax.php'; param2={accion:'mostrar_extracto',tipo:'ver_carpa_10'}; }
		else if(classN=='ver_carpa_11'){ accion='sorteo/operador/loteria_incentivo_ajax.php'; param2={accion:'mostrar_extracto',tipo:'ver_carpa_11'}; }
		else if(classN=='ver_carpa_12'){ accion='sorteo/operador/loteria_incentivo_ajax.php'; param2={accion:'mostrar_extracto',tipo:'ver_carpa_12'}; }
		else if(classN=='ver_carpa_13'){ accion='sorteo/operador/loteria_incentivo_ajax.php'; param2={accion:'mostrar_extracto',tipo:'ver_carpa_13'}; }
		else if(classN=='ver_carpa_14'){ accion='sorteo/operador/loteria_incentivo_ajax.php'; param2={accion:'mostrar_extracto',tipo:'ver_carpa_14'}; }
		else if(classN=='ver_carpa_15'){ accion='sorteo/operador/loteria_incentivo_ajax.php'; param2={accion:'mostrar_extracto',tipo:'ver_carpa_15'}; }
		else if(classN=='ver_carpa_16'){ accion='sorteo/operador/loteria_incentivo_ajax.php'; param2={accion:'mostrar_extracto',tipo:'ver_carpa_16'}; }
		else if(classN=='ver_carpa_17'){ accion='sorteo/operador/loteria_incentivo_ajax.php'; param2={accion:'mostrar_extracto',tipo:'ver_carpa_17'}; }
		else if(classN=='ver_carpa_18'){ accion='sorteo/operador/loteria_incentivo_ajax.php'; param2={accion:'mostrar_extracto',tipo:'ver_carpa_18'}; }
		else if(classN=='ver_carpa_19'){ accion='sorteo/operador/loteria_incentivo_ajax.php'; param2={accion:'mostrar_extracto',tipo:'ver_carpa_19'}; }
		else if(classN=='ver_carpa_20'){ accion='sorteo/operador/loteria_incentivo_ajax.php'; param2={accion:'mostrar_extracto',tipo:'ver_carpa_20'}; }
		else if(classN=='ver_carpa_21'){ accion='sorteo/operador/loteria_incentivo_ajax.php'; param2={accion:'mostrar_extracto',tipo:'ver_carpa_21'}; }
		else if(classN=='ver_carpa_22'){ accion='sorteo/operador/loteria_incentivo_ajax.php'; param2={accion:'mostrar_extracto',tipo:'ver_carpa_22'}; }
		else if(classN=='ver_carpa_23'){ accion='sorteo/operador/loteria_incentivo_ajax.php'; param2={accion:'mostrar_extracto',tipo:'ver_carpa_23'}; }
		else if(classN=='ver_carpa_24'){ accion='sorteo/operador/loteria_incentivo_ajax.php'; param2={accion:'mostrar_extracto',tipo:'ver_carpa_24'}; }
		else if(classN=='ver_carpa_25'){ accion='sorteo/operador/loteria_incentivo_ajax.php'; param2={accion:'mostrar_extracto',tipo:'ver_carpa_25'}; }

		else if(classN=='ver_62'){ accion='sorteo/operador/loteria_incentivo_ajax.php'; param2={accion:'mostrar_extracto',tipo:'ver_62'}; }
		else if(classN=='ver_64'){ accion='sorteo/operador/loteria_incentivo_ajax.php'; param2={accion:'mostrar_extracto',tipo:'ver_64'}; }
		else if(classN=='ver_66'){ accion='sorteo/operador/loteria_incentivo_ajax.php'; param2={accion:'mostrar_extracto',tipo:'ver_66'}; }
		
		else if(classN=='ver_67'){ accion='sorteo/operador/loteria_incentivo_ajax.php'; param2={accion:'mostrar_extracto',tipo:'ver_67'}; }
		else if(classN=='ver_68'){ accion='sorteo/operador/loteria_incentivo_ajax.php'; param2={accion:'mostrar_extracto',tipo:'ver_68'}; }
		else if(classN=='ver_69'){ accion='sorteo/operador/loteria_incentivo_ajax.php'; param2={accion:'mostrar_extracto',tipo:'ver_69'}; }
		else if(classN=='ver_70'){ accion='sorteo/operador/loteria_incentivo_ajax.php'; param2={accion:'mostrar_extracto',tipo:'ver_70'}; }
		else if(classN=='ver_71'){ accion='sorteo/operador/loteria_incentivo_ajax.php'; param2={accion:'mostrar_extracto',tipo:'ver_71'}; }
		else if(classN=='ver_72'){ accion='sorteo/operador/loteria_incentivo_ajax.php'; param2={accion:'mostrar_extracto',tipo:'ver_72'}; }
		else if(classN=='ver_73'){ accion='sorteo/operador/loteria_incentivo_ajax.php'; param2={accion:'mostrar_extracto',tipo:'ver_73'}; }
		else if(classN=='ver_74'){ accion='sorteo/operador/loteria_incentivo_ajax.php'; param2={accion:'mostrar_extracto',tipo:'ver_74'}; }
		else if(classN=='ver_75'){ accion='sorteo/operador/loteria_incentivo_ajax.php'; param2={accion:'mostrar_extracto',tipo:'ver_75'}; }
		else if(classN=='ver_76'){ accion='sorteo/operador/loteria_incentivo_ajax.php'; param2={accion:'mostrar_extracto',tipo:'ver_76'}; }
		else if(classN=='ver_77'){ accion='sorteo/operador/loteria_incentivo_ajax.php'; param2={accion:'mostrar_extracto',tipo:'ver_77'}; }
		else if(classN=='ver_78'){ accion='sorteo/operador/loteria_incentivo_ajax.php'; param2={accion:'mostrar_extracto',tipo:'ver_78'}; }
		else if(classN=='ver_79'){ accion='sorteo/operador/loteria_incentivo_ajax.php'; param2={accion:'mostrar_extracto',tipo:'ver_79'}; }
		else if(classN=='ver_80'){ accion='sorteo/operador/loteria_incentivo_ajax.php'; param2={accion:'mostrar_extracto',tipo:'ver_80'}; }
		else if(classN=='ver_81'){ accion='sorteo/operador/loteria_incentivo_ajax.php'; param2={accion:'mostrar_extracto',tipo:'ver_81'}; }
		else if(classN=='ver_82'){ accion='sorteo/operador/loteria_incentivo_ajax.php'; param2={accion:'mostrar_extracto',tipo:'ver_82'}; }
		else if(classN=='ver_83'){ accion='sorteo/operador/loteria_incentivo_ajax.php'; param2={accion:'mostrar_extracto',tipo:'ver_83'}; }
		else if(classN=='ver_84'){ accion='sorteo/operador/loteria_incentivo_ajax.php'; param2={accion:'mostrar_extracto',tipo:'ver_84'}; }
		else if(classN=='ver_85'){ accion='sorteo/operador/loteria_incentivo_ajax.php'; param2={accion:'mostrar_extracto',tipo:'ver_85'}; }
		else if(classN=='ver_86'){ accion='sorteo/operador/loteria_incentivo_ajax.php'; param2={accion:'mostrar_extracto',tipo:'ver_86'}; }
		else if(classN=='ver_87'){ accion='sorteo/operador/loteria_incentivo_ajax.php'; param2={accion:'mostrar_extracto',tipo:'ver_87'}; }
		else if(classN=='ver_88'){ accion='sorteo/operador/loteria_incentivo_ajax.php'; param2={accion:'mostrar_extracto',tipo:'ver_88'}; }
		else if(classN=='ver_89'){ accion='sorteo/operador/loteria_incentivo_ajax.php'; param2={accion:'mostrar_extracto',tipo:'ver_89'}; }
		else if(classN=='ver_90'){ accion='sorteo/operador/loteria_incentivo_ajax.php'; param2={accion:'mostrar_extracto',tipo:'ver_90'}; }
		
		
		else if(classN=='ver_91'){ accion='sorteo/operador/loteria_incentivo_ajax.php'; param2={accion:'mostrar_extracto',tipo:'ver_91'}; }
		else if(classN=='ver_92'){ accion='sorteo/operador/loteria_incentivo_ajax.php'; param2={accion:'mostrar_extracto',tipo:'ver_92'}; }
		else if(classN=='ver_93'){ accion='sorteo/operador/loteria_incentivo_ajax.php'; param2={accion:'mostrar_extracto',tipo:'ver_93'}; }
		else if(classN=='ver_94'){ accion='sorteo/operador/loteria_incentivo_ajax.php'; param2={accion:'mostrar_extracto',tipo:'ver_94'}; }
		else if(classN=='ver_95'){ accion='sorteo/operador/loteria_incentivo_ajax.php'; param2={accion:'mostrar_extracto',tipo:'ver_95'}; }
		else if(classN=='ver_96'){ accion='sorteo/operador/loteria_incentivo_ajax.php'; param2={accion:'mostrar_extracto',tipo:'ver_96'}; }
		else if(classN=='ver_97'){ accion='sorteo/operador/loteria_incentivo_ajax.php'; param2={accion:'mostrar_extracto',tipo:'ver_97'}; }
		else if(classN=='ver_98'){ accion='sorteo/operador/loteria_incentivo_ajax.php'; param2={accion:'mostrar_extracto',tipo:'ver_98'}; }
		else if(classN=='ver_99'){ accion='sorteo/operador/loteria_incentivo_ajax.php'; param2={accion:'mostrar_extracto',tipo:'ver_99'}; }
		else if(classN=='ver_100'){ accion='sorteo/operador/loteria_incentivo_ajax.php'; param2={accion:'mostrar_extracto',tipo:'ver_100'}; }
		else if(classN=='ver_101'){ accion='sorteo/operador/loteria_incentivo_ajax.php'; param2={accion:'mostrar_extracto',tipo:'ver_101'}; }
		else if(classN=='ver_102'){ accion='sorteo/operador/loteria_incentivo_ajax.php'; param2={accion:'mostrar_extracto',tipo:'ver_102'}; }
		else if(classN=='ver_103'){ accion='sorteo/operador/loteria_incentivo_ajax.php'; param2={accion:'mostrar_extracto',tipo:'ver_103'}; }
		else if(classN=='ver_104'){ accion='sorteo/operador/loteria_incentivo_ajax.php'; param2={accion:'mostrar_extracto',tipo:'ver_104'}; }
		else if(classN=='ver_105'){ accion='sorteo/operador/loteria_incentivo_ajax.php'; param2={accion:'mostrar_extracto',tipo:'ver_105'}; }
		else if(classN=='ver_106'){ accion='sorteo/operador/loteria_incentivo_ajax.php'; param2={accion:'mostrar_extracto',tipo:'ver_106'}; }
		else if(classN=='ver_107'){ accion='sorteo/operador/loteria_incentivo_ajax.php'; param2={accion:'mostrar_extracto',tipo:'ver_107'}; }
		else if(classN=='ver_108'){ accion='sorteo/operador/loteria_incentivo_ajax.php'; param2={accion:'mostrar_extracto',tipo:'ver_108'}; }
		else if(classN=='ver_109'){ accion='sorteo/operador/loteria_incentivo_ajax.php'; param2={accion:'mostrar_extracto',tipo:'ver_109'}; }
		else if(classN=='ver_110'){ accion='sorteo/operador/loteria_incentivo_ajax.php'; param2={accion:'mostrar_extracto',tipo:'ver_110'}; }
		
		
		else if(classN=='ver_111'){ accion='sorteo/operador/loteria_incentivo_ajax.php'; param2={accion:'mostrar_extracto',tipo:'ver_111'}; }
		else if(classN=='ver_112'){ accion='sorteo/operador/loteria_incentivo_ajax.php'; param2={accion:'mostrar_extracto',tipo:'ver_112'}; }
		else if(classN=='ver_113'){ accion='sorteo/operador/loteria_incentivo_ajax.php'; param2={accion:'mostrar_extracto',tipo:'ver_113'}; }
		else if(classN=='ver_114'){ accion='sorteo/operador/loteria_incentivo_ajax.php'; param2={accion:'mostrar_extracto',tipo:'ver_114'}; }
		else if(classN=='ver_115'){ accion='sorteo/operador/loteria_incentivo_ajax.php'; param2={accion:'mostrar_extracto',tipo:'ver_115'}; }
		else if(classN=='ver_116'){ accion='sorteo/operador/loteria_incentivo_ajax.php'; param2={accion:'mostrar_extracto',tipo:'ver_116'}; }
		else if(classN=='ver_117'){ accion='sorteo/operador/loteria_incentivo_ajax.php'; param2={accion:'mostrar_extracto',tipo:'ver_117'}; }
		else if(classN=='ver_118'){ accion='sorteo/operador/loteria_incentivo_ajax.php'; param2={accion:'mostrar_extracto',tipo:'ver_118'}; }
		else if(classN=='ver_119'){ accion='sorteo/operador/loteria_incentivo_ajax.php'; param2={accion:'mostrar_extracto',tipo:'ver_119'}; }
		else if(classN=='ver_120'){ accion='sorteo/operador/loteria_incentivo_ajax.php'; param2={accion:'mostrar_extracto',tipo:'ver_120'}; }
		else if(classN=='ver_121'){ accion='sorteo/operador/loteria_incentivo_ajax.php'; param2={accion:'mostrar_extracto',tipo:'ver_121'}; }
		else if(classN=='ver_122'){ accion='sorteo/operador/loteria_incentivo_ajax.php'; param2={accion:'mostrar_extracto',tipo:'ver_122'}; }
		else if(classN=='ver_123'){ accion='sorteo/operador/loteria_incentivo_ajax.php'; param2={accion:'mostrar_extracto',tipo:'ver_123'}; }
		else if(classN=='ver_124'){ accion='sorteo/operador/loteria_incentivo_ajax.php'; param2={accion:'mostrar_extracto',tipo:'ver_124'}; }
		else if(classN=='ver_125'){ accion='sorteo/operador/loteria_incentivo_ajax.php'; param2={accion:'mostrar_extracto',tipo:'ver_125'}; }
		else if(classN=='ver_126'){ accion='sorteo/operador/loteria_incentivo_ajax.php'; param2={accion:'mostrar_extracto',tipo:'ver_126'}; }
		else if(classN=='ver_127'){ accion='sorteo/operador/loteria_incentivo_ajax.php'; param2={accion:'mostrar_extracto',tipo:'ver_127'}; }
		else if(classN=='ver_128'){ accion='sorteo/operador/loteria_incentivo_ajax.php'; param2={accion:'mostrar_extracto',tipo:'ver_128'}; }
		else if(classN=='ver_129'){ accion='sorteo/operador/loteria_incentivo_ajax.php'; param2={accion:'mostrar_extracto',tipo:'ver_129'}; }
		else if(classN=='ver_130'){ accion='sorteo/operador/loteria_incentivo_ajax.php'; param2={accion:'mostrar_extracto',tipo:'ver_130'}; }
		
		
		else if(classN=='ver_131'){ accion='sorteo/operador/loteria_incentivo_ajax.php'; param2={accion:'mostrar_extracto',tipo:'ver_131'}; }
		else if(classN=='ver_132'){ accion='sorteo/operador/loteria_incentivo_ajax.php'; param2={accion:'mostrar_extracto',tipo:'ver_132'}; }
		else if(classN=='ver_133'){ accion='sorteo/operador/loteria_incentivo_ajax.php'; param2={accion:'mostrar_extracto',tipo:'ver_133'}; }
		else if(classN=='ver_134'){ accion='sorteo/operador/loteria_incentivo_ajax.php'; param2={accion:'mostrar_extracto',tipo:'ver_134'}; }
		else if(classN=='ver_135'){ accion='sorteo/operador/loteria_incentivo_ajax.php'; param2={accion:'mostrar_extracto',tipo:'ver_135'}; }
		
		else if(classN=='ver_136'){ accion='sorteo/operador/loteria_incentivo_ajax.php'; param2={accion:'mostrar_extracto',tipo:'ver_136'}; }
		else if(classN=='ver_137'){ accion='sorteo/operador/loteria_incentivo_ajax.php'; param2={accion:'mostrar_extracto',tipo:'ver_137'}; }
		else if(classN=='ver_138'){ accion='sorteo/operador/loteria_incentivo_ajax.php'; param2={accion:'mostrar_extracto',tipo:'ver_138'}; }
		else if(classN=='ver_139'){ accion='sorteo/operador/loteria_incentivo_ajax.php'; param2={accion:'mostrar_extracto',tipo:'ver_139'}; }
		else if(classN=='ver_140'){ accion='sorteo/operador/loteria_incentivo_ajax.php'; param2={accion:'mostrar_extracto',tipo:'ver_140'}; }
		
		else if(classN=='ver_141'){ accion='sorteo/operador/loteria_incentivo_ajax.php'; param2={accion:'mostrar_extracto',tipo:'ver_141'}; }
		else if(classN=='ver_142'){ accion='sorteo/operador/loteria_incentivo_ajax.php'; param2={accion:'mostrar_extracto',tipo:'ver_142'}; }
		else if(classN=='ver_143'){ accion='sorteo/operador/loteria_incentivo_ajax.php'; param2={accion:'mostrar_extracto',tipo:'ver_143'}; }
		else if(classN=='ver_144'){ accion='sorteo/operador/loteria_incentivo_ajax.php'; param2={accion:'mostrar_extracto',tipo:'ver_144'}; }
		else if(classN=='ver_145'){ accion='sorteo/operador/loteria_incentivo_ajax.php'; param2={accion:'mostrar_extracto',tipo:'ver_145'}; }
		
		else if(classN=='ver_146'){ accion='sorteo/operador/loteria_incentivo_ajax.php'; param2={accion:'mostrar_extracto',tipo:'ver_146'}; }
		else if(classN=='ver_147'){ accion='sorteo/operador/loteria_incentivo_ajax.php'; param2={accion:'mostrar_extracto',tipo:'ver_147'}; }
		else if(classN=='ver_148'){ accion='sorteo/operador/loteria_incentivo_ajax.php'; param2={accion:'mostrar_extracto',tipo:'ver_148'}; }
		else if(classN=='ver_149'){ accion='sorteo/operador/loteria_incentivo_ajax.php'; param2={accion:'mostrar_extracto',tipo:'ver_149'}; }
		else if(classN=='ver_150'){ accion='sorteo/operador/loteria_incentivo_ajax.php'; param2={accion:'mostrar_extracto',tipo:'ver_150'}; }
		
		
		else if(classN=='ver_151'){ accion='sorteo/operador/loteria_incentivo_ajax.php'; param2={accion:'mostrar_extracto',tipo:'ver_151'}; }
		else if(classN=='ver_152'){ accion='sorteo/operador/loteria_incentivo_ajax.php'; param2={accion:'mostrar_extracto',tipo:'ver_152'}; }
		else if(classN=='ver_153'){ accion='sorteo/operador/loteria_incentivo_ajax.php'; param2={accion:'mostrar_extracto',tipo:'ver_153'}; }
		else if(classN=='ver_154'){ accion='sorteo/operador/loteria_incentivo_ajax.php'; param2={accion:'mostrar_extracto',tipo:'ver_154'}; }
		else if(classN=='ver_155'){ accion='sorteo/operador/loteria_incentivo_ajax.php'; param2={accion:'mostrar_extracto',tipo:'ver_155'}; }
		else if(classN=='ver_156'){ accion='sorteo/operador/loteria_incentivo_ajax.php'; param2={accion:'mostrar_extracto',tipo:'ver_156'}; }
		else if(classN=='ver_157'){ accion='sorteo/operador/loteria_incentivo_ajax.php'; param2={accion:'mostrar_extracto',tipo:'ver_157'}; }
		else if(classN=='ver_158'){ accion='sorteo/operador/loteria_incentivo_ajax.php'; param2={accion:'mostrar_extracto',tipo:'ver_158'}; }
		else if(classN=='ver_159'){ accion='sorteo/operador/loteria_incentivo_ajax.php'; param2={accion:'mostrar_extracto',tipo:'ver_159'}; }
		else if(classN=='ver_160'){ accion='sorteo/operador/loteria_incentivo_ajax.php'; param2={accion:'mostrar_extracto',tipo:'ver_160'}; }
		else if(classN=='ver_161'){ accion='sorteo/operador/loteria_incentivo_ajax.php'; param2={accion:'mostrar_extracto',tipo:'ver_161'}; }
		else if(classN=='ver_162'){ accion='sorteo/operador/loteria_incentivo_ajax.php'; param2={accion:'mostrar_extracto',tipo:'ver_162'}; }
		else if(classN=='ver_163'){ accion='sorteo/operador/loteria_incentivo_ajax.php'; param2={accion:'mostrar_extracto',tipo:'ver_163'}; }
		else if(classN=='ver_164'){ accion='sorteo/operador/loteria_incentivo_ajax.php'; param2={accion:'mostrar_extracto',tipo:'ver_164'}; }
		else if(classN=='ver_165'){ accion='sorteo/operador/loteria_incentivo_ajax.php'; param2={accion:'mostrar_extracto',tipo:'ver_165'}; }
		else if(classN=='ver_166'){ accion='sorteo/operador/loteria_incentivo_ajax.php'; param2={accion:'mostrar_extracto',tipo:'ver_166'}; }
		else if(classN=='ver_167'){ accion='sorteo/operador/loteria_incentivo_ajax.php'; param2={accion:'mostrar_extracto',tipo:'ver_167'}; }
		else if(classN=='ver_168'){ accion='sorteo/operador/loteria_incentivo_ajax.php'; param2={accion:'mostrar_extracto',tipo:'ver_168'}; }
		else if(classN=='ver_169'){ accion='sorteo/operador/loteria_incentivo_ajax.php'; param2={accion:'mostrar_extracto',tipo:'ver_169'}; }
		else if(classN=='ver_170'){ accion='sorteo/operador/loteria_incentivo_ajax.php'; param2={accion:'mostrar_extracto',tipo:'ver_170'}; }
		
		
		
		
		
		
		
		
        if(typeof param2 === 'object' && (
		param2.tipo=='ver_alcanzan' 
		|| param2.tipo=='ver_superan_hasta_10' 
		|| param2.tipo=='ver_superan_10_hasta_20' 
		|| param2.tipo=='ver_superan_20' 
		|| param2.tipo=='ver_250_omas' 
		|| param2.tipo=='ver_100_249' 
		|| param2.tipo=='ver_20_99' 
		|| param2.tipo=='ver_kit_mundial'  
		
		|| param2.tipo=='ver_401_mas'  
		|| param2.tipo=='ver_201_400_1'
		|| param2.tipo=='ver_201_400_2'
		|| param2.tipo=='ver_201_400_3'
		|| param2.tipo=='ver_201_400_4'
		|| param2.tipo=='ver_201_400_5'

		
		|| param2.tipo=='ver_121_200_1' 
		|| param2.tipo=='ver_121_200_2' 
		|| param2.tipo=='ver_121_200_3' 
		|| param2.tipo=='ver_121_200_4' 

		
		|| param2.tipo=='ver_30_120_1'  
		|| param2.tipo=='ver_30_120_2'  
		|| param2.tipo=='ver_30_120_3'  
		|| param2.tipo=='ver_30_120_4'  
		|| param2.tipo=='ver_30_120_5'  
		|| param2.tipo=='ver_30_120_6'  
		|| param2.tipo=='ver_30_120_7'  
		|| param2.tipo=='ver_30_120_8'  
		|| param2.tipo=='ver_30_120_9'  
		|| param2.tipo=='ver_30_120_10'  
		|| param2.tipo=='ver_30_120_11'  
		|| param2.tipo=='ver_30_120_12'  
		|| param2.tipo=='ver_30_120_13'  
		|| param2.tipo=='ver_30_120_14'  
		|| param2.tipo=='ver_30_120_15'  
		
		
		
		|| param2.tipo=='ver_vuelta_heli_1'		|| param2.tipo=='ver_vuelta_heli_2'		|| param2.tipo=='ver_vuelta_auto'		|| param2.tipo=='ver_carpa_1'
		|| param2.tipo=='ver_carpa_2'		|| param2.tipo=='ver_carpa_3'		|| param2.tipo=='ver_carpa_4'		|| param2.tipo=='ver_carpa_5'		|| param2.tipo=='ver_carpa_6'
		
		
		|| param2.tipo=='ver_carpa_7'		|| param2.tipo=='ver_carpa_8'
		|| param2.tipo=='ver_carpa_9'		|| param2.tipo=='ver_carpa_10'		|| param2.tipo=='ver_carpa_11'		|| param2.tipo=='ver_carpa_12'		|| param2.tipo=='ver_carpa_13'
		|| param2.tipo=='ver_carpa_14'		|| param2.tipo=='ver_carpa_15'		|| param2.tipo=='ver_carpa_16'		|| param2.tipo=='ver_carpa_17'		|| param2.tipo=='ver_carpa_18'		
		|| param2.tipo=='ver_carpa_19'		|| param2.tipo=='ver_carpa_20'		|| param2.tipo=='ver_carpa_21'		|| param2.tipo=='ver_carpa_22'		|| param2.tipo=='ver_carpa_23'		
		|| param2.tipo=='ver_carpa_24'		|| param2.tipo=='ver_carpa_25'
		
		|| param2.tipo=='ver_62'		|| param2.tipo=='ver_64'		|| param2.tipo=='ver_66'		|| param2.tipo=='ver_67'
		|| param2.tipo=='ver_68'|| param2.tipo=='ver_69'|| param2.tipo=='ver_70'|| param2.tipo=='ver_71'|| param2.tipo=='ver_72'|| param2.tipo=='ver_73'|| param2.tipo=='ver_74'|| param2.tipo=='ver_75'|| param2.tipo=='ver_76'
		|| param2.tipo=='ver_77'|| param2.tipo=='ver_78'|| param2.tipo=='ver_79'|| param2.tipo=='ver_80'|| param2.tipo=='ver_81'|| param2.tipo=='ver_82'|| param2.tipo=='ver_83'|| param2.tipo=='ver_84'|| param2.tipo=='ver_85'
		|| param2.tipo=='ver_86'|| param2.tipo=='ver_87'|| param2.tipo=='ver_88'|| param2.tipo=='ver_89'
		|| param2.tipo=='ver_90'
		
		|| param2.tipo=='ver_91'|| param2.tipo=='ver_92'|| param2.tipo=='ver_93'|| param2.tipo=='ver_94'|| param2.tipo=='ver_95'
		|| param2.tipo=='ver_96'|| param2.tipo=='ver_97'|| param2.tipo=='ver_98'|| param2.tipo=='ver_99'|| param2.tipo=='ver_100'
		|| param2.tipo=='ver_101'|| param2.tipo=='ver_102'|| param2.tipo=='ver_103'|| param2.tipo=='ver_104'|| param2.tipo=='ver_105'
		|| param2.tipo=='ver_106'|| param2.tipo=='ver_107'|| param2.tipo=='ver_108'|| param2.tipo=='ver_109'|| param2.tipo=='ver_110'
		
		|| param2.tipo=='ver_111'|| param2.tipo=='ver_112'|| param2.tipo=='ver_113'|| param2.tipo=='ver_114'|| param2.tipo=='ver_115'
		|| param2.tipo=='ver_116'|| param2.tipo=='ver_117'|| param2.tipo=='ver_118'|| param2.tipo=='ver_119'|| param2.tipo=='ver_120'
		|| param2.tipo=='ver_121'|| param2.tipo=='ver_122'|| param2.tipo=='ver_123'|| param2.tipo=='ver_124'|| param2.tipo=='ver_125'
		|| param2.tipo=='ver_126'|| param2.tipo=='ver_127'|| param2.tipo=='ver_128'|| param2.tipo=='ver_129'|| param2.tipo=='ver_130'
		
		
		|| param2.tipo=='ver_131'|| param2.tipo=='ver_132'|| param2.tipo=='ver_133'|| param2.tipo=='ver_134'|| param2.tipo=='ver_135'
		|| param2.tipo=='ver_136'|| param2.tipo=='ver_137'|| param2.tipo=='ver_138'|| param2.tipo=='ver_139'|| param2.tipo=='ver_140'
		|| param2.tipo=='ver_141'|| param2.tipo=='ver_142'|| param2.tipo=='ver_143'|| param2.tipo=='ver_144'|| param2.tipo=='ver_145'
		|| param2.tipo=='ver_146'|| param2.tipo=='ver_147'|| param2.tipo=='ver_148'|| param2.tipo=='ver_149'|| param2.tipo=='ver_150'
		
		
		|| param2.tipo=='ver_151'
		|| param2.tipo=='ver_152'
		|| param2.tipo=='ver_153'
		|| param2.tipo=='ver_154'
		|| param2.tipo=='ver_155'
		|| param2.tipo=='ver_156'
		|| param2.tipo=='ver_157'
		|| param2.tipo=='ver_158'
		|| param2.tipo=='ver_159'
		|| param2.tipo=='ver_160'
		|| param2.tipo=='ver_161'
		|| param2.tipo=='ver_162'
		|| param2.tipo=='ver_163'
		|| param2.tipo=='ver_164'
		|| param2.tipo=='ver_165'
		|| param2.tipo=='ver_166'
		|| param2.tipo=='ver_167'
		|| param2.tipo=='ver_168'
		|| param2.tipo=='ver_169'
		|| param2.tipo=='ver_170'

		
		)){
          $.post( accion,
                  param2
          ).done(
                  function(data){
                    if(data.tipo=='info'){
                      $("#warning_juego.alert").slideDown("slow");  
                      $('#warning_juego > .contenido_error').html(data.mensaje);
                    }   
                    delete param2;                                                                                                   
                  }
          );

        }else
         cargar_juego(accion,param);
        $("ul.juegos > li.active").removeClass('active');
        $('.'+classN).parent().addClass('active');
      }


      function cargar_juego (accion,parametros){
        $.post( accion,
                parametros
              ).done(               
                function(data){
                  if(param.tipo!='ver_alcanzan' 
				  && param.tipo!='ver_superan_hasta_10' 
				  && param.tipo!='ver_superan_10_hasta_20' 
				  && param.tipo!='ver_superan_20' 
				  && param.tipo!='ver_250_omas' 
				  && param.tipo!='ver_100_249' 
				  && param.tipo!='ver_20_99' 
				  && param.tipo!='ver_kit_mundial'  
				  
				  && param.tipo!='ver_401_mas'  
				  
				  && param.tipo!='ver_201_400_1'  
				  && param.tipo!='ver_201_400_2'  
				  && param.tipo!='ver_201_400_3'  
				  && param.tipo!='ver_201_400_4'  
				  && param.tipo!='ver_201_400_5'  
				  
				  
				  && param.tipo!='ver_121_200_1'  
				  && param.tipo!='ver_121_200_2'  
				  && param.tipo!='ver_121_200_3'  
				  && param.tipo!='ver_121_200_4'  
				  
				  
				  && param.tipo!='ver_30_120_1' 
				  && param.tipo!='ver_30_120_2' 
				  && param.tipo!='ver_30_120_3' 
				  && param.tipo!='ver_30_120_4' 
				  && param.tipo!='ver_30_120_5' 
				  && param.tipo!='ver_30_120_6' 
				  && param.tipo!='ver_30_120_7' 
				  && param.tipo!='ver_30_120_8' 
				  && param.tipo!='ver_30_120_9' 
				  && param.tipo!='ver_30_120_10' 
				  && param.tipo!='ver_30_120_11' 
				  && param.tipo!='ver_30_120_12' 
				  && param.tipo!='ver_30_120_13' 
				  && param.tipo!='ver_30_120_14' 
				  && param.tipo!='ver_30_120_15' 
				  
				  
				  
				  && param.tipo!='ver_vuelta_heli_1' 
				  && param.tipo!='ver_vuelta_heli_2' 
				  && param.tipo!='ver_vuelta_auto' 
				  && param.tipo!='ver_carpa_1' 
				  && param.tipo!='ver_carpa_2' 
				  && param.tipo!='ver_carpa_3' 
				  && param.tipo!='ver_carpa_4' 
				  && param.tipo!='ver_carpa_5' 
				  && param.tipo!='ver_carpa_6' 
				  
				&& param.tipo!='ver_carpa_7'
				&& param.tipo!='ver_carpa_8'
				&& param.tipo!='ver_carpa_9'
				&& param.tipo!='ver_carpa_10'
				&& param.tipo!='ver_carpa_11'
				&& param.tipo!='ver_carpa_12'
				&& param.tipo!='ver_carpa_13'
				&& param.tipo!='ver_carpa_14'
				&& param.tipo!='ver_carpa_15'
				&& param.tipo!='ver_carpa_16'
				&& param.tipo!='ver_carpa_17'
				&& param.tipo!='ver_carpa_18'
				&& param.tipo!='ver_carpa_19'
				&& param.tipo!='ver_carpa_20'
				&& param.tipo!='ver_carpa_21'
				&& param.tipo!='ver_carpa_22'
				&& param.tipo!='ver_carpa_23'
				&& param.tipo!='ver_carpa_24'
				&& param.tipo!='ver_carpa_25' 
				
				&& param.tipo!='ver_62' 
				&& param.tipo!='ver_64' 
				&& param.tipo!='ver_66' 
				&& param.tipo!='ver_67' && param.tipo!='ver_68' && param.tipo!='ver_69' && param.tipo!='ver_70' && param.tipo!='ver_71' && param.tipo!='ver_72' && param.tipo!='ver_73' && param.tipo!='ver_74' && param.tipo!='ver_75' && param.tipo!='ver_76' && param.tipo!='ver_77' && param.tipo!='ver_78' && param.tipo!='ver_79' && param.tipo!='ver_80' && param.tipo!='ver_81' && param.tipo!='ver_82' && param.tipo!='ver_83' && param.tipo!='ver_84' && param.tipo!='ver_85' && param.tipo!='ver_86' && param.tipo!='ver_87' && param.tipo!='ver_88' && param.tipo!='ver_89' 
				&& param.tipo!='ver_90'
				
				&& param.tipo!='ver_91' && param.tipo!='ver_92' && param.tipo!='ver_93' && param.tipo!='ver_94' && param.tipo!='ver_95'
				&& param.tipo!='ver_96' && param.tipo!='ver_97' && param.tipo!='ver_98' && param.tipo!='ver_99' && param.tipo!='ver_100'
				&& param.tipo!='ver_101' && param.tipo!='ver_102' && param.tipo!='ver_103' && param.tipo!='ver_104' && param.tipo!='ver_105'
				&& param.tipo!='ver_106' && param.tipo!='ver_107' && param.tipo!='ver_108' && param.tipo!='ver_109' && param.tipo!='ver_110'
				
				
				&& param.tipo!='ver_111'
				&& param.tipo!='ver_112'
				&& param.tipo!='ver_113'
				&& param.tipo!='ver_114'
				&& param.tipo!='ver_115'
				&& param.tipo!='ver_116'
				&& param.tipo!='ver_117'
				&& param.tipo!='ver_118'
				&& param.tipo!='ver_119'
				&& param.tipo!='ver_120'
				&& param.tipo!='ver_121'
				&& param.tipo!='ver_122'
				&& param.tipo!='ver_123'
				&& param.tipo!='ver_124'
				&& param.tipo!='ver_125'
				&& param.tipo!='ver_126'
				&& param.tipo!='ver_127'
				&& param.tipo!='ver_128'
				&& param.tipo!='ver_129'
				&& param.tipo!='ver_130' 
				
				
				
				&& param.tipo!='ver_131' 
				&& param.tipo!='ver_132' 
				&& param.tipo!='ver_133' 
				&& param.tipo!='ver_134' 
				&& param.tipo!='ver_135'
				
				&& param.tipo!='ver_136' 
				&& param.tipo!='ver_137' 
				&& param.tipo!='ver_138' 
				&& param.tipo!='ver_139' 
				&& param.tipo!='ver_140' 
				
				&& param.tipo!='ver_141' 
				&& param.tipo!='ver_142' 
				&& param.tipo!='ver_143' 
				&& param.tipo!='ver_144' 
				&& param.tipo!='ver_145' 
				
				&& param.tipo!='ver_146' 
				&& param.tipo!='ver_147' 
				&& param.tipo!='ver_148' 
				&& param.tipo!='ver_149' 
				&& param.tipo!='ver_150' 
				
				
				&& param.tipo!='ver_151'
				&& param.tipo!='ver_152'
				&& param.tipo!='ver_153'
				&& param.tipo!='ver_154'
				&& param.tipo!='ver_155'
				&& param.tipo!='ver_156'
				&& param.tipo!='ver_157'
				&& param.tipo!='ver_158'
				&& param.tipo!='ver_159'
				&& param.tipo!='ver_160'
				&& param.tipo!='ver_161'
				&& param.tipo!='ver_162'
				&& param.tipo!='ver_163'
				&& param.tipo!='ver_164'
				&& param.tipo!='ver_165'
				&& param.tipo!='ver_166'
				&& param.tipo!='ver_167'
				&& param.tipo!='ver_168'
				&& param.tipo!='ver_169'
				&& param.tipo!='ver_170'
				
				  ){
                    $('#juego').html(data);
                  }                                                                                                     
                }
              );
       
      };

     var buscarGanadores1= function buscarGanadores(){
        param={accion:'mostrar',juego:param.juego};
        $.post('sorteo/operador/loteria_listado_ganadores.php?buscarGanadores='+parseInt(Math.random() * 1000000000),
                param
        ).done(
                function(data){
                  $('#ganadores').html(data);  
                    
                }
        );
      }
      

      var controlGanadores2= function controlGanadores(){
          param={
                  accion:'control_ganador',
                  juego:'incentivo'
                };
                Ajax2=  $.post('sorteo/operador/loteria_incentivo_ajax.php?controlGanador=1',
                   param               
                  ).done(
                    function(Ajax2){
                      for (var i=0 ; i<Ajax2.length ; i++){
                        if (Ajax2[i].sorteado > 0){
                          deshabilitarIncentivo(Ajax2[i].idIncentivo);
                          //console.log('DESHABILITAR');
                        }
                        else{
                          habilitarIncentivo(Ajax2[i].idIncentivo);
                          //console.log('HABILITAR');
                        }
                      }
                    }
                  );
      }

     
      $(document).ready(
        function() {
          cambiar_juego("incentivo");
          buscarGanadores1();
          controlGanadores2();
          setInterval(buscarGanadores1,2000);
          setInterval(controlGanadores2,2000);
        });
    </script>
  </head>
  <body>
      <div id="contenedor_general" class="container">
        <h3 class="titulo"><img width="40" border="0" src="img/logo_loteria_peque.png"><?php echo $_SESSION['juego'] ?> <?php echo $_SESSION['sorteo'] ?> <span style="font-size:11px"><i class="icon-user"></i><?php echo $_SESSION['nombre_usuario'].' ('.$_SESSION['tipo_usuario'].')'; ?></span></h3> 
        <div class="bar" style="width: 40%"></div>
          <div>
            <div class="row-fluid show-grid">
              <div class="navbar">
                  <div class="navbar-inner">
                    <ul class="nav juegos">
                      
						<li></li>
						<li></li>
						<li></li>
						<li></li>
						
                      <li class="divider-vertical"></li>
                    </ul>
                  </div>
                </div>
            
            </div>
          </div>
          <div id="juego"></div>
          <div id="error" class="alert alert-error" style="display:none">
            <button type="button" class="close" onclick="$('.alert').slideUp('slow');">x</button>
            <span><i class="icon-ok"></i></span>
            <span class="contenido_error"></span>
          </div>         
          <div id="ganadores"></div>
          <div id="pie"><img src="img/logo_loteria_peque.png" width="20" height="20"> Desarrollado por la Loteria de Córdoba 2013</div>
      </div>     
  </body>
</html>