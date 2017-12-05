<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<title>Sorteador Loteria de Cordoba S.E.</title>
	<link href="escribano_incentivo_estilo.css" rel="stylesheet" type="text/css" />
	<script type="text/javascript" src="../../librerias/jquery/jquery-1.10.1.js"></script>
	<script type="text/javascript" src="../../js/funciones.js"></script>
</head>
<body>

	<div id="incentivo">
		<div id="tipo_incentivo"></div>
		<div id="aleatorio"></div>
		<div id="sucursal"></div>
		<div id="agencia"></div>
		<div id="localidad"></div>
	</div>
	
	<script type="text/javascript">
		$(document).ready(
			function(){
				buscar_informacion();
				setInterval(buscar_informacion, 1300);
			}
		);

		var datos_incentivo = [];

		var incentivoMostrando = '1';

		function buscar_informacion(){
			$.getJSON(
				'escribano_incentivo_ajax.php?ale='+parseInt(Math.random() * 1000000000),
				function(data){
					var descIncentivo=data.descIncentivo;
					var incentivoMostrando=data.incentivoMostrando;
					//modifico css en base al incentivo
					switch (incentivoMostrando){
						case(1):
							$('#tipo_incentivo').css("top","195px");
							$('#tipo_incentivo').css("left","290px");
							$('#tipo_incentivo').css("font-size","50px");
							break;
						case(2):
							$('#tipo_incentivo').css("top","195px");
							$('#tipo_incentivo').css("left","95px");
							$('#tipo_incentivo').css("font-size","50px");
							break;
						case(3):
							$('#tipo_incentivo').css("top","205px");
							$('#tipo_incentivo').css("left","95px");
							$('#tipo_incentivo').css("font-size","41px");
							break;
						case(4):
							$('#tipo_incentivo').css("top","195px");
							$('#tipo_incentivo').css("left","180px");
							$('#tipo_incentivo').css("font-size","50px");
							break;
							
							
						case(5):
							$('#tipo_incentivo').css("top","205px");
							$('#tipo_incentivo').css("left","110px");
							$('#tipo_incentivo').css("font-size","30px");
							break;
						case(6):
							$('#tipo_incentivo').css("top","205px");
							$('#tipo_incentivo').css("left","110px");
							$('#tipo_incentivo').css("font-size","30px");
							break;
						case(7):
							$('#tipo_incentivo').css("top","205px");
							$('#tipo_incentivo').css("left","110px");
							$('#tipo_incentivo').css("font-size","30px");
							break;
							
						case(8):
							$('#tipo_incentivo').css("top","205px");
							$('#tipo_incentivo').css("left","110px");
							$('#tipo_incentivo').css("font-size","30px");
							break;
						
						case(9):
							$('#tipo_incentivo').css("top","205px");
							$('#tipo_incentivo').css("left","110px");
							$('#tipo_incentivo').css("font-size","30px");
							break;
						case(10):
							$('#tipo_incentivo').css("top","208px");
							$('#tipo_incentivo').css("left","110px");
							$('#tipo_incentivo').css("font-size","25px");
							break;
						case(11):
							$('#tipo_incentivo').css("top","208px");
							$('#tipo_incentivo').css("left","110px");
							$('#tipo_incentivo').css("font-size","25px");
							break;
						
						case(12):$('#tipo_incentivo').css("top","208px");$('#tipo_incentivo').css("left","110px");$('#tipo_incentivo').css("font-size","25px");break;
							
						case(13): $('#tipo_incentivo').css("top","208px"); $('#tipo_incentivo').css("left","110px"); $('#tipo_incentivo').css("font-size","25px"); break;
						case(14): $('#tipo_incentivo').css("top","208px"); $('#tipo_incentivo').css("left","110px"); $('#tipo_incentivo').css("font-size","25px"); break;
						case(15): $('#tipo_incentivo').css("top","208px"); $('#tipo_incentivo').css("left","110px"); $('#tipo_incentivo').css("font-size","25px"); break;
						case(16): $('#tipo_incentivo').css("top","208px"); $('#tipo_incentivo').css("left","110px"); $('#tipo_incentivo').css("font-size","25px"); break;
						case(17): $('#tipo_incentivo').css("top","208px"); $('#tipo_incentivo').css("left","110px"); $('#tipo_incentivo').css("font-size","25px"); break;
						case(18): $('#tipo_incentivo').css("top","208px"); $('#tipo_incentivo').css("left","110px"); $('#tipo_incentivo').css("font-size","25px"); break;
						case(19): $('#tipo_incentivo').css("top","208px"); $('#tipo_incentivo').css("left","110px"); $('#tipo_incentivo').css("font-size","25px"); break;
						case(20): $('#tipo_incentivo').css("top","208px"); $('#tipo_incentivo').css("left","110px"); $('#tipo_incentivo').css("font-size","25px"); break;
						case(21): $('#tipo_incentivo').css("top","208px"); $('#tipo_incentivo').css("left","110px"); $('#tipo_incentivo').css("font-size","25px"); break;
						case(22): $('#tipo_incentivo').css("top","208px"); $('#tipo_incentivo').css("left","110px"); $('#tipo_incentivo').css("font-size","25px"); break;
						case(23): $('#tipo_incentivo').css("top","208px"); $('#tipo_incentivo').css("left","110px"); $('#tipo_incentivo').css("font-size","25px"); break;
						case(24): $('#tipo_incentivo').css("top","208px"); $('#tipo_incentivo').css("left","110px"); $('#tipo_incentivo').css("font-size","25px"); break;
						case(25): $('#tipo_incentivo').css("top","208px"); $('#tipo_incentivo').css("left","110px"); $('#tipo_incentivo').css("font-size","25px"); break;
						case(26): $('#tipo_incentivo').css("top","208px"); $('#tipo_incentivo').css("left","110px"); $('#tipo_incentivo').css("font-size","25px"); break;
						case(27): $('#tipo_incentivo').css("top","208px"); $('#tipo_incentivo').css("left","110px"); $('#tipo_incentivo').css("font-size","25px"); break;
						case(28): $('#tipo_incentivo').css("top","208px"); $('#tipo_incentivo').css("left","110px"); $('#tipo_incentivo').css("font-size","25px"); break;
						case(29): $('#tipo_incentivo').css("top","208px"); $('#tipo_incentivo').css("left","110px"); $('#tipo_incentivo').css("font-size","25px"); break;
						case(30): $('#tipo_incentivo').css("top","208px"); $('#tipo_incentivo').css("left","110px"); $('#tipo_incentivo').css("font-size","25px"); break;
						case(31): $('#tipo_incentivo').css("top","208px"); $('#tipo_incentivo').css("left","110px"); $('#tipo_incentivo').css("font-size","25px"); break;
						case(32): $('#tipo_incentivo').css("top","208px"); $('#tipo_incentivo').css("left","110px"); $('#tipo_incentivo').css("font-size","25px"); break; 
						case(33): $('#tipo_incentivo').css("top","208px"); $('#tipo_incentivo').css("left","110px"); $('#tipo_incentivo').css("font-size","25px"); break; 
						
						
						
						
						case(34): $('#tipo_incentivo').css("top","208px"); $('#tipo_incentivo').css("left","110px"); $('#tipo_incentivo').css("font-size","25px"); break; 
						case(35): $('#tipo_incentivo').css("top","208px"); $('#tipo_incentivo').css("left","110px"); $('#tipo_incentivo').css("font-size","25px"); break; 
						case(36): $('#tipo_incentivo').css("top","208px"); $('#tipo_incentivo').css("left","110px"); $('#tipo_incentivo').css("font-size","25px"); break; 
						case(37): $('#tipo_incentivo').css("top","208px"); $('#tipo_incentivo').css("left","110px"); $('#tipo_incentivo').css("font-size","25px"); break; 
						case(38): $('#tipo_incentivo').css("top","208px"); $('#tipo_incentivo').css("left","110px"); $('#tipo_incentivo').css("font-size","25px"); break; 
						case(39): $('#tipo_incentivo').css("top","208px"); $('#tipo_incentivo').css("left","110px"); $('#tipo_incentivo').css("font-size","25px"); break; 
							
						
						case(40): $('#tipo_incentivo').css("top","208px"); $('#tipo_incentivo').css("left","110px"); $('#tipo_incentivo').css("font-size","25px"); break;
						case(41): $('#tipo_incentivo').css("top","208px"); $('#tipo_incentivo').css("left","110px"); $('#tipo_incentivo').css("font-size","25px"); break;
						case(42): $('#tipo_incentivo').css("top","208px"); $('#tipo_incentivo').css("left","110px"); $('#tipo_incentivo').css("font-size","25px"); break;
						case(43): $('#tipo_incentivo').css("top","208px"); $('#tipo_incentivo').css("left","110px"); $('#tipo_incentivo').css("font-size","25px"); break;
						case(44): $('#tipo_incentivo').css("top","208px"); $('#tipo_incentivo').css("left","110px"); $('#tipo_incentivo').css("font-size","25px"); break;
						case(45): $('#tipo_incentivo').css("top","208px"); $('#tipo_incentivo').css("left","110px"); $('#tipo_incentivo').css("font-size","25px"); break;
						case(46): $('#tipo_incentivo').css("top","208px"); $('#tipo_incentivo').css("left","110px"); $('#tipo_incentivo').css("font-size","25px"); break;
						case(47): $('#tipo_incentivo').css("top","208px"); $('#tipo_incentivo').css("left","110px"); $('#tipo_incentivo').css("font-size","25px"); break;
						case(48): $('#tipo_incentivo').css("top","208px"); $('#tipo_incentivo').css("left","110px"); $('#tipo_incentivo').css("font-size","25px"); break;
						case(49): $('#tipo_incentivo').css("top","208px"); $('#tipo_incentivo').css("left","110px"); $('#tipo_incentivo').css("font-size","25px"); break;
						case(50): $('#tipo_incentivo').css("top","208px"); $('#tipo_incentivo').css("left","110px"); $('#tipo_incentivo').css("font-size","25px"); break;
						case(51): $('#tipo_incentivo').css("top","208px"); $('#tipo_incentivo').css("left","110px"); $('#tipo_incentivo').css("font-size","25px"); break;
						case(52): $('#tipo_incentivo').css("top","208px"); $('#tipo_incentivo').css("left","110px"); $('#tipo_incentivo').css("font-size","25px"); break;
						case(53): $('#tipo_incentivo').css("top","208px"); $('#tipo_incentivo').css("left","110px"); $('#tipo_incentivo').css("font-size","25px"); break;
						case(54): $('#tipo_incentivo').css("top","208px"); $('#tipo_incentivo').css("left","110px"); $('#tipo_incentivo').css("font-size","25px"); break;
						case(55): $('#tipo_incentivo').css("top","208px"); $('#tipo_incentivo').css("left","110px"); $('#tipo_incentivo').css("font-size","25px"); break;
						case(56): $('#tipo_incentivo').css("top","208px"); $('#tipo_incentivo').css("left","110px"); $('#tipo_incentivo').css("font-size","25px"); break;
						case(57): $('#tipo_incentivo').css("top","208px"); $('#tipo_incentivo').css("left","110px"); $('#tipo_incentivo').css("font-size","25px"); break;
						case(58): $('#tipo_incentivo').css("top","208px"); $('#tipo_incentivo').css("left","110px"); $('#tipo_incentivo').css("font-size","25px"); break;
						case(59): $('#tipo_incentivo').css("top","208px"); $('#tipo_incentivo').css("left","110px"); $('#tipo_incentivo').css("font-size","25px"); break;
						case(60): $('#tipo_incentivo').css("top","208px"); $('#tipo_incentivo').css("left","110px"); $('#tipo_incentivo').css("font-size","25px"); break;
						case(61): $('#tipo_incentivo').css("top","208px"); $('#tipo_incentivo').css("left","110px"); $('#tipo_incentivo').css("font-size","25px"); break; 
						
						
						case(91): $('#tipo_incentivo').css("top","208px"); $('#tipo_incentivo').css("left","130px"); $('#tipo_incentivo').css("font-size","27px"); break; 
						case(92): $('#tipo_incentivo').css("top","208px"); $('#tipo_incentivo').css("left","130px"); $('#tipo_incentivo').css("font-size","27px"); break; 
						case(93): $('#tipo_incentivo').css("top","208px"); $('#tipo_incentivo').css("left","130px"); $('#tipo_incentivo').css("font-size","27px"); break; 
						case(94): $('#tipo_incentivo').css("top","208px"); $('#tipo_incentivo').css("left","130px"); $('#tipo_incentivo').css("font-size","27px"); break; 
						case(95): $('#tipo_incentivo').css("top","208px"); $('#tipo_incentivo').css("left","130px"); $('#tipo_incentivo').css("font-size","27px"); break; 
						
						
						case(96): $('#tipo_incentivo').css("top","208px"); $('#tipo_incentivo').css("left","130px"); $('#tipo_incentivo').css("font-size","27px"); break; 
						case(97): $('#tipo_incentivo').css("top","208px"); $('#tipo_incentivo').css("left","130px"); $('#tipo_incentivo').css("font-size","27px"); break; 
						case(98): $('#tipo_incentivo').css("top","208px"); $('#tipo_incentivo').css("left","130px"); $('#tipo_incentivo').css("font-size","27px"); break; 
						case(99): $('#tipo_incentivo').css("top","208px"); $('#tipo_incentivo').css("left","130px"); $('#tipo_incentivo').css("font-size","27px"); break; 
						case(100): $('#tipo_incentivo').css("top","208px"); $('#tipo_incentivo').css("left","130px"); $('#tipo_incentivo').css("font-size","27px"); break; 
						
						
						case(101): $('#tipo_incentivo').css("top","208px"); $('#tipo_incentivo').css("left","130px"); $('#tipo_incentivo').css("font-size","27px"); break; 
						case(102): $('#tipo_incentivo').css("top","208px"); $('#tipo_incentivo').css("left","130px"); $('#tipo_incentivo').css("font-size","27px"); break; 
						case(103): $('#tipo_incentivo').css("top","208px"); $('#tipo_incentivo').css("left","130px"); $('#tipo_incentivo').css("font-size","27px"); break; 
						case(104): $('#tipo_incentivo').css("top","208px"); $('#tipo_incentivo').css("left","130px"); $('#tipo_incentivo').css("font-size","27px"); break; 
						case(105): $('#tipo_incentivo').css("top","208px"); $('#tipo_incentivo').css("left","130px"); $('#tipo_incentivo').css("font-size","27px"); break; 
						
						
						
						case(106): $('#tipo_incentivo').css("top","208px"); $('#tipo_incentivo').css("left","130px"); $('#tipo_incentivo').css("font-size","27px"); break; 
						case(107): $('#tipo_incentivo').css("top","208px"); $('#tipo_incentivo').css("left","130px"); $('#tipo_incentivo').css("font-size","27px"); break; 
						case(108): $('#tipo_incentivo').css("top","208px"); $('#tipo_incentivo').css("left","130px"); $('#tipo_incentivo').css("font-size","27px"); break; 
						case(109): $('#tipo_incentivo').css("top","208px"); $('#tipo_incentivo').css("left","130px"); $('#tipo_incentivo').css("font-size","27px"); break; 
						case(110): $('#tipo_incentivo').css("top","208px"); $('#tipo_incentivo').css("left","130px"); $('#tipo_incentivo').css("font-size","27px"); break; 
												
						
						case(111): $('#tipo_incentivo').css("top","208px"); $('#tipo_incentivo').css("left","130px"); $('#tipo_incentivo').css("font-size","27px"); break;
						case(112): $('#tipo_incentivo').css("top","208px"); $('#tipo_incentivo').css("left","130px"); $('#tipo_incentivo').css("font-size","27px"); break;
						case(113): $('#tipo_incentivo').css("top","208px"); $('#tipo_incentivo').css("left","130px"); $('#tipo_incentivo').css("font-size","27px"); break;
						case(114): $('#tipo_incentivo').css("top","208px"); $('#tipo_incentivo').css("left","130px"); $('#tipo_incentivo').css("font-size","27px"); break;
						case(115): $('#tipo_incentivo').css("top","208px"); $('#tipo_incentivo').css("left","130px"); $('#tipo_incentivo').css("font-size","27px"); break;
						case(116): $('#tipo_incentivo').css("top","208px"); $('#tipo_incentivo').css("left","130px"); $('#tipo_incentivo').css("font-size","27px"); break;
						case(117): $('#tipo_incentivo').css("top","208px"); $('#tipo_incentivo').css("left","130px"); $('#tipo_incentivo').css("font-size","27px"); break;
						case(118): $('#tipo_incentivo').css("top","208px"); $('#tipo_incentivo').css("left","130px"); $('#tipo_incentivo').css("font-size","27px"); break;
						case(119): $('#tipo_incentivo').css("top","208px"); $('#tipo_incentivo').css("left","130px"); $('#tipo_incentivo').css("font-size","27px"); break;
						case(120): $('#tipo_incentivo').css("top","208px"); $('#tipo_incentivo').css("left","130px"); $('#tipo_incentivo').css("font-size","27px"); break;
						case(121): $('#tipo_incentivo').css("top","208px"); $('#tipo_incentivo').css("left","130px"); $('#tipo_incentivo').css("font-size","27px"); break;
						case(122): $('#tipo_incentivo').css("top","208px"); $('#tipo_incentivo').css("left","130px"); $('#tipo_incentivo').css("font-size","27px"); break;
						case(123): $('#tipo_incentivo').css("top","208px"); $('#tipo_incentivo').css("left","130px"); $('#tipo_incentivo').css("font-size","27px"); break;
						case(124): $('#tipo_incentivo').css("top","208px"); $('#tipo_incentivo').css("left","130px"); $('#tipo_incentivo').css("font-size","27px"); break;
						case(125): $('#tipo_incentivo').css("top","208px"); $('#tipo_incentivo').css("left","130px"); $('#tipo_incentivo').css("font-size","27px"); break;
						case(126): $('#tipo_incentivo').css("top","208px"); $('#tipo_incentivo').css("left","130px"); $('#tipo_incentivo').css("font-size","27px"); break;
						case(127): $('#tipo_incentivo').css("top","208px"); $('#tipo_incentivo').css("left","130px"); $('#tipo_incentivo').css("font-size","27px"); break;
						case(128): $('#tipo_incentivo').css("top","208px"); $('#tipo_incentivo').css("left","130px"); $('#tipo_incentivo').css("font-size","27px"); break;
						case(129): $('#tipo_incentivo').css("top","208px"); $('#tipo_incentivo').css("left","130px"); $('#tipo_incentivo').css("font-size","27px"); break;
						case(130): $('#tipo_incentivo').css("top","208px"); $('#tipo_incentivo').css("left","130px"); $('#tipo_incentivo').css("font-size","27px"); break; 
						
						
						//incentivos navidad 2016
						case(131): $('#tipo_incentivo').css("top","208px"); $('#tipo_incentivo').css("left","130px"); $('#tipo_incentivo').css("font-size","27px"); break; 
						case(132): $('#tipo_incentivo').css("top","208px"); $('#tipo_incentivo').css("left","130px"); $('#tipo_incentivo').css("font-size","27px"); break; 
						case(133): $('#tipo_incentivo').css("top","208px"); $('#tipo_incentivo').css("left","130px"); $('#tipo_incentivo').css("font-size","27px"); break; 
						case(134): $('#tipo_incentivo').css("top","208px"); $('#tipo_incentivo').css("left","130px"); $('#tipo_incentivo').css("font-size","27px"); break; 
						case(135): $('#tipo_incentivo').css("top","208px"); $('#tipo_incentivo').css("left","130px"); $('#tipo_incentivo').css("font-size","27px"); break; 
						
						case(136): $('#tipo_incentivo').css("top","208px"); $('#tipo_incentivo').css("left","130px"); $('#tipo_incentivo').css("font-size","27px"); break; 
						case(137): $('#tipo_incentivo').css("top","208px"); $('#tipo_incentivo').css("left","130px"); $('#tipo_incentivo').css("font-size","27px"); break; 
						case(138): $('#tipo_incentivo').css("top","208px"); $('#tipo_incentivo').css("left","130px"); $('#tipo_incentivo').css("font-size","27px"); break; 
						case(139): $('#tipo_incentivo').css("top","208px"); $('#tipo_incentivo').css("left","130px"); $('#tipo_incentivo').css("font-size","27px"); break; 
						case(140): $('#tipo_incentivo').css("top","208px"); $('#tipo_incentivo').css("left","130px"); $('#tipo_incentivo').css("font-size","27px"); break; 
						
						case(141): $('#tipo_incentivo').css("top","208px"); $('#tipo_incentivo').css("left","130px"); $('#tipo_incentivo').css("font-size","27px"); break; 
						case(142): $('#tipo_incentivo').css("top","208px"); $('#tipo_incentivo').css("left","130px"); $('#tipo_incentivo').css("font-size","27px"); break; 
						case(143): $('#tipo_incentivo').css("top","208px"); $('#tipo_incentivo').css("left","130px"); $('#tipo_incentivo').css("font-size","27px"); break; 
						case(144): $('#tipo_incentivo').css("top","208px"); $('#tipo_incentivo').css("left","130px"); $('#tipo_incentivo').css("font-size","27px"); break; 
						case(145): $('#tipo_incentivo').css("top","208px"); $('#tipo_incentivo').css("left","130px"); $('#tipo_incentivo').css("font-size","27px"); break; 
						
						case(146): $('#tipo_incentivo').css("top","208px"); $('#tipo_incentivo').css("left","130px"); $('#tipo_incentivo').css("font-size","27px"); break; 
						case(147): $('#tipo_incentivo').css("top","208px"); $('#tipo_incentivo').css("left","130px"); $('#tipo_incentivo').css("font-size","27px"); break; 
						case(148): $('#tipo_incentivo').css("top","208px"); $('#tipo_incentivo').css("left","130px"); $('#tipo_incentivo').css("font-size","27px"); break; 
						case(149): $('#tipo_incentivo').css("top","208px"); $('#tipo_incentivo').css("left","130px"); $('#tipo_incentivo').css("font-size","27px"); break; 
						case(150): $('#tipo_incentivo').css("top","208px"); $('#tipo_incentivo').css("left","130px"); $('#tipo_incentivo').css("font-size","27px"); break; 
						
						
						
						
						
						case(151): 
						$('#tipo_incentivo').css("top","155px"); 
						//$('#tipo_incentivo').css("left","130px"); 
						$('#tipo_incentivo').css("font-size","27px"); 
						break; 
						case(152):  
						$('#tipo_incentivo').css("top","155px"); 
						//$('#tipo_incentivo').css("left","130px"); 
						$('#tipo_incentivo').css("font-size","27px"); 
						break; 
						case(153):  
						$('#tipo_incentivo').css("top","155px"); 
						//$('#tipo_incentivo').css("left","130px"); 
						$('#tipo_incentivo').css("font-size","27px"); 
						break;  
						case(154):  
						$('#tipo_incentivo').css("top","155px"); 
						//$('#tipo_incentivo').css("left","130px"); 
						$('#tipo_incentivo').css("font-size","27px"); 
						break;  
						case(155):  
						$('#tipo_incentivo').css("top","155px"); 
						//$('#tipo_incentivo').css("left","130px"); 
						$('#tipo_incentivo').css("font-size","27px"); 
						break; 
						
						case(156):  
						$('#tipo_incentivo').css("top","155px"); 
						//$('#tipo_incentivo').css("left","130px"); 
						$('#tipo_incentivo').css("font-size","26px"); 
						break; 
						case(157):  
						$('#tipo_incentivo').css("top","155px"); 
						//$('#tipo_incentivo').css("left","130px"); 
						$('#tipo_incentivo').css("font-size","26px"); 
						break;
						case(158):  
						$('#tipo_incentivo').css("top","155px"); 
						//$('#tipo_incentivo').css("left","130px"); 
						$('#tipo_incentivo').css("font-size","26px"); 
						break; 
						case(159):  
						$('#tipo_incentivo').css("top","155px"); 
						//$('#tipo_incentivo').css("left","130px"); 
						$('#tipo_incentivo').css("font-size","26px"); 
						break;
						case(160):  
						$('#tipo_incentivo').css("top","155px"); 
						//$('#tipo_incentivo').css("left","130px"); 
						$('#tipo_incentivo').css("font-size","26px"); 
						break; 
						
						case(161):  
						$('#tipo_incentivo').css("top","155px"); 
						//$('#tipo_incentivo').css("left","130px"); 
						$('#tipo_incentivo').css("font-size","26px"); 
						break; 
						case(162):  
						$('#tipo_incentivo').css("top","155px"); 
						//$('#tipo_incentivo').css("left","130px"); 
						$('#tipo_incentivo').css("font-size","26px"); 
						break; 
						case(163):  
						$('#tipo_incentivo').css("top","155px"); 
						//$('#tipo_incentivo').css("left","130px"); 
						$('#tipo_incentivo').css("font-size","26px"); 
						break;
						case(164):  
						$('#tipo_incentivo').css("top","155px"); 
						//$('#tipo_incentivo').css("left","130px"); 
						$('#tipo_incentivo').css("font-size","26px"); 
						break;
						case(165):  
						$('#tipo_incentivo').css("top","155px"); 
						//$('#tipo_incentivo').css("left","130px"); 
						$('#tipo_incentivo').css("font-size","26px"); 
						break;
						
						case(166):  
						$('#tipo_incentivo').css("top","155px"); 
						//$('#tipo_incentivo').css("left","130px"); 
						$('#tipo_incentivo').css("font-size","26px"); 
						break;
						case(167):  
						$('#tipo_incentivo').css("top","155px"); 
						//$('#tipo_incentivo').css("left","130px"); 
						$('#tipo_incentivo').css("font-size","26px"); 
						break;
						case(168): 
						$('#tipo_incentivo').css("top","155px"); 
						$('#tipo_incentivo').css("left","130px"); 
						$('#tipo_incentivo').css("font-size","26px"); 
						break; 
						case(169): 
						$('#tipo_incentivo').css("top","155px"); 
						$('#tipo_incentivo').css("left","130px"); 
						$('#tipo_incentivo').css("font-size","26px"); 
						break; 
						case(170): 
						$('#tipo_incentivo').css("top","155px"); 
						//$('#tipo_incentivo').css("left","130px"); 
						$('#tipo_incentivo').css("font-size","26px"); 
						break;  
						
						
						
						
							
					}

					$('#tipo_incentivo').html(data.descIncentivo);

					if (typeof data.datosIncentivo[0] != 'undefined'){
						if(data.datosIncentivo[0].aleatorio != ''){
							if (typeof intervalo_para_aleatorio != 'undefined'){
								clearInterval(intervalo_para_aleatorio);}
							$('#aleatorio').html(data.datosIncentivo[0].aleatorio);
							$('#sucursal').html(data.datosIncentivo[0].desc_sucursal);
							$('#agencia').html((data.datosIncentivo[0].id_agencia+' - '+data.datosIncentivo[0].desc_agencia).substr(1,32));
							$('#localidad').html(data.datosIncentivo[0].localidad);
						}else{
							intervalo_para_aleatorio = setInterval(animar_aleatorio, 100);
						}
					}else{
						if (typeof intervalo_para_aleatorio != 'undefined'){
								clearInterval(intervalo_para_aleatorio);
								intervalo_para_aleatorio = setInterval(animar_aleatorio, 100);
								$('#sucursal').html('');
								$('#agencia').html('');
								$('#localidad').html('');
						}
					}
					
					if (typeof intervalo_para_aleatorio == 'undefined'){
						intervalo_para_aleatorio = setInterval(animar_aleatorio, 100);
					}
					

					function animar_aleatorio(){
						var maximo=parseInt(data.maximoAleatorio);
         	   			$('#aleatorio').html(  pad(Math.floor((Math.random()*maximo)+1),5) );
        			}
					
				}
			);
		}

		function animar_aleatorio(){
            $('#aleatorio').html(  Math.floor((Math.random()*100500)+1)  );
        }
		
		function pad(n, width, z) {
			z = z || '0';
			n = n + '';
			return n.length >= width ? n : new Array(width - n.length + 1).join(z) + n;
		}
	</script>
</body>
</html>