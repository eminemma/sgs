<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<title>Sorteador Loteria de Cordoba S.E.</title>
	<link href="escribano_anticipada_estilo.css" rel="stylesheet" type="text/css" />
	<script type="text/javascript" src="../../librerias/jquery/jquery-1.10.1.js"></script>
	<script type="text/javascript" src="../../js/funciones.js"></script>
</head>
<body>

	<div id="incentivo">
		<div id="tipo_incentivo"></div>
		<div id="aleatorio"></div>
		<div id="sucursal"></div>
		<div id="agencia"></div>
		<div id="localidad_d"></div>
		<div id="nombre_d"></div>
		
		<div id="billete_d"></div>
		<div id="fraccion_d"></div>
		<div id="prescripcion_d"></div>
		<div id="prox_sorteo_d"></div>
		<div id="premio_prox_sorteo_d"></div>
		
		
		<div id="escribano_d"></div>
		<div id="jefe_sorteo_d"></div>
		
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
				'escribano_anticipada_ajax.php?ale='+parseInt(Math.random() * 1000000000),
				function(data){
					var descIncentivo=data.descIncentivo;
					var incentivoMostrando=data.incentivoMostrando;
					//modifico css en base al incentivo
					switch (incentivoMostrando){
						
						case(1): 
							$('#incentivo').css("background-image","url(escribano_img/gordito_invierno_2015_semana1_blanco.png)"); 
							break; 
						case(2): 
							$('#incentivo').css("background-image","url(escribano_img/gordito_invierno_2015_semana2_blanco.png)"); 
							break; 
						case(3): 
							$('#incentivo').css("background-image","url(escribano_img/gordito_invierno_2015_semana3_blanco.png)"); 
							break; 
						case(4): 
							$('#incentivo').css("background-image","url(escribano_img/gordito_invierno_2015_semana4_blanco.png)"); 
							break; 
						case(5): 
							$('#incentivo').css("background-image","url(escribano_img/gordito_invierno_2015_semana5_blanco.png)"); 
							break; 
						case(6): 
							$('#incentivo').css("background-image","url(escribano_img/gordito_invierno_2015_semana6_blanco.png)"); 
							break; 
						case(7): 
							$('#incentivo').css("background-image","url(escribano_img/gordito_invierno_2015_semana7_blanco.png)"); 
							break; 
						case(8): 
							$('#incentivo').css("background-image","url(escribano_img/gordito_invierno_2015_semana8_blanco.png)"); 
							break; 
						case(9): 
							$('#incentivo').css("background-image","url(escribano_img/gordito_invierno_2015_semana9_blanco.png)"); 
							break; 
							
							
					}
					
					$('#tipo_incentivo').html(data.descIncentivo);
					$('#prescripcion_d').html(data.prescripcion);
					$('#prox_sorteo_d').html(data.prox_sorteo);
					$('#premio_prox_sorteo_d').html(data.premio_prox_sorteo);
					$('#escribano_d').html(data.escribano);
					$('#jefe_sorteo_d').html(data.jefe_sorteo);
					
					
					if (typeof data.datosIncentivo[0] != 'undefined'){
						//if(data.datosIncentivo[0].aleatorio != ''){
							if (typeof intervalo_para_aleatorio != 'undefined'){
								clearInterval(intervalo_para_aleatorio);}
								
							//$('#aleatorio').html(data.datosIncentivo[0].aleatorio);
							
							
							$('#agencia').html(data.datosIncentivo[0].id_agencia);
							$('#localidad_d').html(data.datosIncentivo[0].localidad);
							
							$('#nombre_d').html(data.datosIncentivo[0].nombre);
							
							$('#billete_d').html(data.datosIncentivo[0].billete);
							$('#fraccion_d').html(data.datosIncentivo[0].fraccion);
														
							$('#sucursal').html(data.datosIncentivo[0].desc_sucursal);
							$('#agencia').html((data.datosIncentivo[0].desc_agencia).substr(0,32));
							
							
						//}else{
						//	intervalo_para_aleatorio = setInterval(animar_aleatorio, 50);
						//}
					}else{
						if (typeof intervalo_para_aleatorio != 'undefined'){
								clearInterval(intervalo_para_aleatorio);
								intervalo_para_aleatorio = setInterval(animar_aleatorio, 50);
								$('#sucursal').html('');
								$('#agencia').html('');
								$('#localidad_d').html('');
								$('#nombre_d').html('');
								$('#billete_d').html('');
								$('#fraccion_d').html('');
						}
					}
					
					if (typeof intervalo_para_aleatorio == 'undefined'){
						intervalo_para_aleatorio = setInterval(animar_aleatorio, 50);
					}

					function animar_aleatorio(){
						var maximo=parseInt(data.maximoAleatorio);
         	   			$('#aleatorio').html(Math.floor((Math.random()*maximo)+1));
        			}
				}
			);
		}

		function animar_aleatorio(){
            $('#aleatorio').html(Math.floor((Math.random()*100500)+1));
        }
	</script>
</body>
</html>