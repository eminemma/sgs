<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<title>Sorteador Loteria de Cordoba S.E.</title>
	<link href="escribano_estilo.css" rel="stylesheet" type="text/css" />
	<script type="text/javascript" src="../../../librerias/jquery/jquery-1.10.1.js"></script>
	<script type="text/javascript" src="../../../js/funciones.js"></script>
</head>
<body>

	<div id="zona1">
		<div id="c1"></div>
	</div>
	<div id="zona2">
		<div id="c2"></div>
	</div>
	<div id="zona3">
		<div id="c3"></div>
		<div id="cantidad_ganadores_8_aciertos" class="cantidad_ganadores">Cantidad Ganadores </div>
	</div>
	<div id="zona4">
		<div id="c4"></div>
		<div id="cantidad_ganadores_7_aciertos" class="cantidad_ganadores">Cantidad Ganadores </div>
		<div id="cantidad_ganadores_6_aciertos" class="cantidad_ganadores">Cantidad Ganadores </div>
		<div id="cantidad_ganadores_5_aciertos" class="cantidad_ganadores">Cantidad Ganadores </div>
	</div>
	<script type="text/javascript">
		$(document).ready(
			function(){
				buscar_informacion();
				setInterval(buscar_informacion, 1300);
			}
		);

		var posicionZona1 =	{
								'01':	{
											inicial:	{
															top: '280px',
															left: '280px'
														},
											final:		{
															height: '75px'
														}
										},

								'02':	{
											inicial:	{
															top: '360px',
															left: '280px'
														},
											final:		{
															height: '75px'
														}
										},

								'03':	{
											inicial:	{
															top: '445px',
															left: '280px'
														},
											final:		{
															height: '75px'
														}
										},

								'04':	{
											inicial:	{
															top: '520px',
															left: '280px'
														},
											final:		{
															height: '75px'
														}
										},

								'05':	{
											inicial:	{
															top: '600px',
															left: '280px'
														},
											final:		{
															height: '75px'
														}
										},

								'06':	{
											inicial:	{
															top: '280px',
															left: '560px'
														},
											final:		{
															height: '75px'
														}
										},

								'07':	{
											inicial:	{
															top: '360px',
															left: '560px'
														},
											final:		{
															height: '75px'
														}
										},

								'08':	{
											inicial:	{
															top: '445px',
															left: '560px'
														},
											final:		{
															height: '75px'
														}
										},

								'09':	{
											inicial:	{
															top: '520px',
															left: '560px'
														},
											final:		{
															height: '75px'
														}
										},

								'10':	{
											inicial:	{
															top: '600px',
															left: '560px'
														},
											final:		{
															height: '75px'
														}
										},

								'11':	{
											inicial:	{
															top: '280px',
															left: '850px'
														},
											final:		{
															height: '75px'
														}
										},

								'12':	{
											inicial:	{
															top: '360px',
															left: '850px'
														},
											final:		{
															height: '75px'
														}
										},

								'13':	{
											inicial:	{
															top: '445px',
															left: '850px'
														},
											final:		{
															height: '75px'
														}
										},

								'14':	{
											inicial:	{
															top: '520px',
															left: '850px'
														},
											final:		{
															height: '75px'
														}
										},

								'15':	{
											inicial:	{
															top: '600px',
															left: '850px'
														},
											final:		{
															height: '75px'
														}
										},

								'16':	{
											inicial:	{
															top: '280px',
															left: '1140px'
														},
											final:		{
															height: '75px'
														}
										},

								'17':	{
											inicial:	{
															top: '360px',
															left: '1140px'
														},
											final:		{
															height: '75px'
														}
										},

								'18':	{
											inicial:	{
															top: '445px',
															left: '1140px'
														},
											final:		{
															height: '75px'
														}
										},

								'19':	{
											inicial:	{
															top: '520px',
															left: '1140px'
														},
											final:		{
															height: '75px'
														}
										},

								'20':	{
											inicial:	{
															top: '600px',
															left: '1140px'
														},
											final:		{
															height: '75px'
														}
										},
							};

	  var posicionZona3 =	{
								'PRIMER_PREMIO':	{
									inicial:	{
										top: '430px',
										left: '690px'
									},
									final:	{
										height: '140px'
									}
								}
							};

		 var posicionZona4 =	{
								'SEGUNDO_PREMIO':	{
									inicial:	{
										top: '450px',
										left: '220px'
									},
									final:	{
										height: '90px'
									}
								},
								'TERCER_PREMIO':	{
									inicial:	{
										top: '450px',
										left: '690px'
									},
									final:	{
										height: '90px'
									}
								},
								'CUARTO_PREMIO':	{
									inicial:	{
										top: '450px',
										left: '1140px'
									},
									final:	{
										height: '90px'
									}
								}
							};

		var billetesZona1 = [];
		var pozoZona4 = false;

		var zonaMostrando = 'zona1';

		function buscar_informacion(){
			$.getJSON(
				'escribano_ajax.php?ale='+parseInt(Math.random() * 1000000000),
				function(data){

					if(data.zonaMostrando == 'zona1'){
						$('#zona2, #zona3, #zona4').hide();
						$('#zona1').fadeIn();
						$('#c2, #c3, #c4').empty();
						zonaMostrando = 'zona1';
					}
					if(data.zonaMostrando == 'zona2'){
						$('#zona1, #zona3, #zona4').hide();
						$('#zona2').fadeIn();
						$('#c1, #c3, #c4').empty();
						zonaMostrando = 'zona2';
					}
					if(data.zonaMostrando == 'zona3'){
						$('#zona1, #zona2, #zona4').hide();
						$('#zona3').fadeIn();
						$('#c1, #c2, #c4').empty();
						zonaMostrando = 'zona3';
					}
					if(data.zonaMostrando == 'zona4'){
						$('#zona1, #zona2, #zona3').hide();
						$('#zona4').fadeIn();
						$('#c1, #c2, #c3').empty();
						zonaMostrando = 'zona4';
					}
					if(zonaMostrando == 'zona1'){
						primerVez2 = true;
						pozoPrimerPremioEncontrado = null;
						pozoSegundoPremioEncontrado = null;
						pozoTercerPremioEncontrado = null;
						cantidadPrimerPremioEncontrado = null;
						animarZona1(data);
					}
					if(zonaMostrando == 'zona2'){
						primerVez = true;
						pozoPrimerPremioEncontrado = null;
						pozoSegundoPremioEncontrado = null;
						pozoTercerPremioEncontrado = null;
						cantidadPrimerPremioEncontrado = null;
						//billetesZona1 = [];
						animarZona2(data);
					}
					if(zonaMostrando == 'zona3'){
						primerVez2 = true;
						primerVez = true;
						pozoSegundoPremioEncontrado = null;
						pozoTercerPremioEncontrado = null;

						//billetesZona1 = [];
						animarZona3(data);
					}
					if(zonaMostrando == 'zona4'){
						primerVez2 = true;
						primerVez = true;
						pozoPrimerPremioEncontrado = null;
						cantidadPrimerPremioEncontrado = null;
						//billetesZona1 = [];
						animarZona4(data);
					}
				}
			);
		}
		var primerVez = true;
		var primerVez2 = true;
		function datosCompartidos(zona,data){
			if(zona==1){
			var elemento = $('<div class="escribano">'+data.escribano+'</div>');
				$('#c'+zona).append(elemento);
				elemento = $('<div class="jefe">'+data.jefe+'</div>');
				$('#c'+zona).append(elemento);
				}
				elemento = $('<div class="nrosorteo">'+data.sorteo+'</div>');
				$('#c'+zona).append(elemento);
				elemento = $('<div class="fechasorteo">'+data.fecha_sorteo+'</div>');
				$('#c'+zona).append(elemento);
		}
		function animarZona1(data){
			var posicionesEncontradas = [];
			if(primerVez){
				datosCompartidos(1,data);
				primerVez = false;
			}
			$(data.billetesZona1).each(
				function(){
					if(billetesZona1[this.posicion] == undefined || billetesZona1[this.posicion].numero != this.numero){
						billetesZona1[this.posicion] = this;

						crearAnimacionNuevoBillete(1, this, posicionZona1);


						return false;
					}
				}
			);

			$(data.billetesZona1).each(
				function(){
					posicionesEncontradas.push(this.posicion);
				}
			);


			// Aca vemos si se borró alguno.
			// Si algun valor de "billeteZona1" no está en el array que llegó en el DATA, hay que borrarlo.
			for(indice in billetesZona1){
				if(posicionesEncontradas.indexOf(indice) == -1){
					billetesZona1[indice] = undefined;
					$('#zona1 #billete1_'+indice).remove();


				}
			}
		}

		function animarZona2(data){
			if(primerVez2){
				datosCompartidos(2,data);
				primerVez2 = false;
			}
		}

		var pozoPrimerPremioEncontrado = null;
		var cantidadPrimerPremioEncontrado = null;
		function animarZona3(data){
			if(cantidadPrimerPremioEncontrado == null){
				datosCompartidos(3,data);
			}
			if(data.pozos[0].pozo_8_aciertos != pozoPrimerPremioEncontrado || data.pozos[0].cantidad_ganadores_8_aciertos != cantidadPrimerPremioEncontrado){
				pozoPrimerPremioEncontrado = data.pozos[0].pozo_8_aciertos;
				cantidadPrimerPremioEncontrado = data.pozos[0].cantidad_ganadores_8_aciertos;
				crearAnimacionPrimerPremio(3, data.pozos,posicionZona3);
			}
		}

		var pozoSegundoPremioEncontrado = null;
		var pozoTercerPremioEncontrado = null;
		var cantidadSegundoPremioEncontrado = null;
		var cantidadTercerPremioEncontrado = null;
		function animarZona4(data){
			if(pozoSegundoPremioEncontrado == null){
				datosCompartidos(4,data);
			}
			if(data.pozos[1].pozo_7_aciertos != pozoSegundoPremioEncontrado || data.pozos[2].pozo_6_aciertos != pozoTercerPremioEncontrado || data.pozos[1].cantidad_ganadores_7_aciertos != cantidadSegundoPremioEncontrado || data.pozos[2].cantidad_ganadores_6_aciertos != cantidadTercerPremioEncontrado){
				pozoSegundoPremioEncontrado = data.pozos[1].pozo_7_aciertos;
				pozoTercerPremioEncontrado = data.pozos[2].pozo_6_aciertos;
				cantidadSegundoPremioEncontrado = data.pozos[1].cantidad_ganadores_7_aciertos;
				cantidadTercerPremioEncontrado = data.pozos[2].cantidad_ganadores_6_aciertos;
				crearAnimacionPozos67(4, data.pozos,posicionZona4);
			}
		}

		function crearAnimacionPozos67(zona, pozos, posiciones){
			var id = 'pozo'+zona+'_7_aciertos';
			$('#'+id).remove();
			elemento = $('<span id="'+id+'" class="pozo_7_aciertos">'+pozos[1].pozo_7_aciertos+'</span>');

			$('#zona'+zona).append(elemento);
			var inicial =  posiciones['SEGUNDO_PREMIO'].inicial;
					inicial.height = '0px';
					inicial['font-size'] = '0px';

			$('#'+id).css(inicial);

			var final =  posiciones['SEGUNDO_PREMIO'].final;
			final.width = getAnchoByAlto2(final.height);
			final.top = (parseInt(inicial.top) - (parseInt(final.height) / 2))+'px';
			final.left = (parseInt(inicial.left) - (parseInt(final.width) / 2))+'px';
			final['font-size'] = getTamanioTextoByAlto(final.height);

			$('#'+id).animate(final, 1000);
			if(pozos[1].cantidad_ganadores_7_aciertos == '0')
				$('#cantidad_ganadores_7_aciertos').html('Pozo Vacante ');
			else
				$('#cantidad_ganadores_7_aciertos').html(pozos[1].cantidad_ganadores_7_aciertos);


			var id = 'pozo'+zona+'_6_aciertos';
			$('#'+id).remove();
			elemento = $('<span id="'+id+'" class="pozo_6_aciertos">'+pozos[2].pozo_6_aciertos+'</span>');

			$('#zona'+zona).append(elemento);
			var inicial =  posiciones['TERCER_PREMIO'].inicial;
					inicial.height = '0px';
					inicial['font-size'] = '0px';

			$('#'+id).css(inicial);

			var final =  posiciones['TERCER_PREMIO'].final;
			final.width = getAnchoByAlto2(final.height);
			final.top = (parseInt(inicial.top) - (parseInt(final.height) / 2))+'px';
			final.left = (parseInt(inicial.left) - (parseInt(final.width) / 2))+'px';
			final['font-size'] = getTamanioTextoByAlto(final.height);

			$('#'+id).animate(final, 1000);
			if(pozos[2].cantidad_ganadores_6_aciertos == '0')
				$('#cantidad_ganadores_6_aciertos').html('Pozo Vacante ');
			else
				$('#cantidad_ganadores_6_aciertos').html(pozos[2].cantidad_ganadores_6_aciertos);


			//5 ACIERTOS ANIMACION
			var id = 'pozo'+zona+'_5_aciertos';
			$('#'+id).remove();
			elemento = $('<span id="'+id+'" class="pozo_5_aciertos">'+pozos[3].pozo_5_aciertos+'</span>');

			$('#zona'+zona).append(elemento);
			var inicial =  posiciones['CUARTO_PREMIO'].inicial;
					inicial.height = '0px';
					inicial['font-size'] = '0px';

			$('#'+id).css(inicial);

			var final =  posiciones['CUARTO_PREMIO'].final;
			final.width = getAnchoByAlto2(final.height);
			final.top = (parseInt(inicial.top) - (parseInt(final.height) / 2))+'px';
			final.left = (parseInt(inicial.left) - (parseInt(final.width) / 2))+'px';
			final['font-size'] = getTamanioTextoByAlto(final.height);

			$('#'+id).animate(final, 1000);
			if(pozos[3].cantidad_ganadores_5_aciertos == '0')
				$('#cantidad_ganadores_5_aciertos').html('Pozo Vacante ');
			else
				$('#cantidad_ganadores_5_aciertos').html(pozos[3].cantidad_ganadores_5_aciertos);

		}

		function crearAnimacionPrimerPremio(zona, pozos, posiciones){
			var id = 'pozo'+zona+'_8_aciertos';
			$('#'+id).remove();
			elemento = $('<span id="'+id+'" class="pozo_8_aciertos">'+pozos[0].pozo_8_aciertos+'</span>');

			$('#zona'+zona).append(elemento);
			var inicial =  posiciones['PRIMER_PREMIO'].inicial;
					inicial.height = '0px';
					inicial['font-size'] = '0px';

			$('#'+id).css(inicial);

			var final =  posiciones['PRIMER_PREMIO'].final;
			final.width = getAnchoByAlto2(final.height);
			final.top = (parseInt(inicial.top) - (parseInt(final.height) / 2))+'px';
			final.left = (parseInt(inicial.left) - (parseInt(final.width) / 2))+'px';
			final['font-size'] = getTamanioTextoByAlto(final.height);

			$('#'+id).animate(final, 1000);

			if(pozos[0].cantidad_ganadores_8_aciertos == '0')
				$('#cantidad_ganadores_8_aciertos').html('Pozo Vacante ');
			else
				$('#cantidad_ganadores_8_aciertos').html(pozos[0].cantidad_ganadores_8_aciertos);
		}
		function crearAnimacionNuevoBillete(zona, billete, posiciones){

				var id = 'billete'+zona+'_'+billete.posicion;
				$('#'+id).remove();
				var elemento = null;
				if(billete.estado == '2')
					elemento = $('<span id="'+id+'" class="billete" >'+billete.numero+'</span>');
				else if(billete.estado == '1')
					elemento = $('<span id="'+id+'" class="billete" style="color:red">'+billete.numero+'</span>');
				else
					elemento = $('<span id="'+id+'" class="billete">'+billete.numero+'</span>');


				$('#zona'+zona).append(elemento);

				var inicial = posiciones[billete.posicion].inicial;
					inicial.height = '0px';
					inicial['font-size'] = '0px';

				$('#'+id).css(inicial);

				var final = posiciones[billete.posicion].final;
					final.width = getAnchoByAlto(final.height);
					final.top = (parseInt(inicial.top) - (parseInt(final.height) / 2))+'px';
					final.left = (parseInt(inicial.left) - (parseInt(final.width) / 2))+'px';
					final['font-size'] = getTamanioTextoByAlto(final.height);

				$('#'+id).animate(final, 1000);

		}

		function getAnchoByElement(elemento){
			return (parseInt($(elemento).css('height')) * 3) + 'px';
		}

		function getAnchoByAlto2(alto){
			return (parseInt(alto) * 5) + 'px';
		}

		function getAnchoByAlto(alto){
			return (parseInt(alto) * 3) + 'px';
		}

		function getTamanioTextoByElement(elemento){
			var h = parseInt($(elemento).css('height'));
			var tamanioTexto = h * 0.95;
			tamanioTexto = tamanioTexto.length > 5 ? tamanioTexto.substring(0,5) : tamanioTexto;
			return tamanioTexto + 'px';
		}

		function getTamanioTextoByAlto(alto){
			var h = parseInt(alto);
			var tamanioTexto = h * 0.95;
			tamanioTexto = tamanioTexto.length > 5 ? tamanioTexto.substring(0,5) : tamanioTexto;
			return tamanioTexto + 'px';
		}
	</script>
</body>
</html>
