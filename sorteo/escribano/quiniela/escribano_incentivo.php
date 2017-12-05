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
							intervalo_para_aleatorio = setInterval(animar_aleatorio, 50);
						}
					}else{
						if (typeof intervalo_para_aleatorio != 'undefined'){
								clearInterval(intervalo_para_aleatorio);
								intervalo_para_aleatorio = setInterval(animar_aleatorio, 50);
								$('#sucursal').html('');
								$('#agencia').html('');
								$('#localidad').html('');
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