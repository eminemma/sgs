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

	<div id="zona1"><div id="primer_premio"></div></div>
	<div id="zona2"></div>
	<div id="zona3"></div>

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
															top: '292px',
															left: '200px'
														},
											final:		{
															height: '60px'
														}
										},

								'02':	{
											inicial:	{
															top: '360px',
															left: '200px'
														},
											final:		{
															height: '60px'
														}
										},

								'03':	{
											inicial:	{
															top: '428px',
															left: '200px'
														},
											final:		{
															height: '60px'
														}
										},

								'04':	{
											inicial:	{
															top: '496px',
															left: '200px'
														},
											final:		{
															height: '60px'
														}
										},

								'05':	{
											inicial:	{
															top: '564px',
															left: '200px'
														},
											final:		{
															height: '60px'
														}
										},

								'06':	{
											inicial:	{
															top: '292px',
															left: '425px'
														},
											final:		{
															height: '60px'
														}
										},

								'07':	{
											inicial:	{
															top: '360px',
															left: '425px'
														},
											final:		{
															height: '60px'
														}
										},

								'08':	{
											inicial:	{
															top: '428px',
															left: '425px'
														},
											final:		{
															height: '60px'
														}
										},

								'09':	{
											inicial:	{
															top: '496px',
															left: '425px'
														},
											final:		{
															height: '60px'
														}
										},

								'10':	{
											inicial:	{
															top: '564px',
															left: '425px'
														},
											final:		{
															height: '60px'
														}
										},

								'11':	{
											inicial:	{
															top: '292px',
															left: '650px'
														},
											final:		{
															height: '60px'
														}
										},

								'12':	{
											inicial:	{
															top: '360px',
															left: '650px'
														},
											final:		{
															height: '60px'
														}
										},

								'13':	{
											inicial:	{
															top: '428px',
															left: '650px'
														},
											final:		{
															height: '60px'
														}
										},

								'14':	{
											inicial:	{
															top: '496px',
															left: '650px'
														},
											final:		{
															height: '60px'
														}
										},

								'15':	{
											inicial:	{
															top: '564px',
															left: '650px'
														},
											final:		{
															height: '60px'
														}
										},

								'16':	{
											inicial:	{
															top: '292px',
															left: '875px'
														},
											final:		{
															height: '60px'
														}
										},

								'17':	{
											inicial:	{
															top: '360px',
															left: '875px'
														},
											final:		{
															height: '60px'
														}
										},

								'18':	{
											inicial:	{
															top: '428px',
															left: '875px'
														},
											final:		{
															height: '60px'
														}
										},

								'19':	{
											inicial:	{
															top: '496px',
															left: '875px'
														},
											final:		{
															height: '60px'
														}
										},

								'20':	{
											inicial:	{
															top: '564px',
															left: '875px'
														},
											final:		{
															height: '60px'
														}
										},

								'progresion':	{
													top: '460px',
													left: '427px',
													height: '50px'
												}
							};

		var posicionZona2 =	{
								'21':	{
											billete : 	{

															inicial:	{
																			top: '285px',
																			left: '302px'
																		},
															final:		{
																			height: '70px'
																		}
														},
											fraccion : 	{

															inicial:	{
																			top: '285px',
																			left: '632px;'
																		},
															final:		{
																			height: '70px'
																		}
														}
										},

								'22':	{
											billete : 	{

															inicial:	{
																			top: '397px',
																			left: '302px'
																		},
															final:		{
																			height: '70px'
																		}
														},
											fraccion : 	{

															inicial:	{
																			top: '397px',
																			left: '632px'
																		},
															final:		{
																			height: '70px'
																		}
														}
										},

								'23':	{
											billete : 	{

															inicial:	{
																			top: '509px',
																			left: '302px'
																		},
															final:		{
																			height: '70px'
																		}
														},
											fraccion : 	{

															inicial:	{
																			top: '509px',
																			left: '632px'
																		},
															final:		{
																			height: '70px'
																		}
														}
										},
								'24':	{
											billete : 	{

															inicial:	{
																			top: '454px',
																			left: '250px'
																		},
															final:		{
																			height: '70px'
																		}
														},
											fraccion : 	{

															inicial:	{
																			top: '456px',
																			left: '580px'
																		},
															final:		{
																			height: '70px'
																		}
														}
										},
								'25':	{
											billete : 	{

															inicial:	{
																			top: '526px',
																			left: '250px'
																		},
															final:		{
																			height: '70px'
																		}
														},
											fraccion : 	{

															inicial:	{
																			top: '526px',
																			left: '580px'
																		},
															final:		{
																			height: '70px'
																		}
														}
										}
							};

		var posicionZona3 =	{
								'01':	{
											inicial:	{
															top: '395px',
															left: '350px'
														},
											final:		{
															height: '160px'
														}
										}
							};

		var billetesZona1 = [];
		var billetesZona2 = [];
		var billetesZona3 = [];

		var zonaMostrando = 'zona1';

		function buscar_informacion(){
			$.getJSON(
				'escribano_ajax.php?ale='+parseInt(Math.random() * 1000000000),
				function(data){

					if(data.zonaMostrando == 'zona1' && zonaMostrando != 'zona1'){
						$('#zona2, #zona3').hide();
						$('#zona1').fadeIn();
						zonaMostrando = 'zona1';

					}else if(data.zonaMostrando == 'zona2' && zonaMostrando != 'zona2'){
						$('#zona1, #zona3').hide();
						$('#zona2').fadeIn();
						zonaMostrando = 'zona2';

					}else if(data.zonaMostrando == 'zona3' && zonaMostrando != 'zona3'){
						$('#zona1, #zona2').hide();
						$('#zona3').fadeIn();
						zonaMostrando = 'zona3';
					}

					if(zonaMostrando == 'zona1'){
						$('#primer_premio').fadeIn();
						animarZona1(data);
					}

					else if(zonaMostrando == 'zona2')
						animarZona2(data);
					else if(zonaMostrando == 'zona3')
						animarZona3(data);
				}
			);
		}
		var primerVez = true;

		function animarZona1(data){
			var posicionesEncontradas = [];
			if(primerVez){
				var elemento = $('<div class="escribano">'+data.escribano+'</div>');
				$('#zona1').append(elemento);
				elemento = $('<div class="jefe">'+data.jefe+'</div>');
				$('#zona1').append(elemento);
				elemento = $('<div class="tipo_juego">CONCURSO '+data.juego+'</div>');
				$('#zona1').append(elemento);
				elemento = $('<div class="nrosorteo">'+data.sorteo+'</div>');
				$('#zona1').append(elemento);
				elemento = $('<div class="lfechasorteo">FECHA</div>');
				$('#zona1').append(elemento);
				elemento = $('<div class="fechasorteo">'+data.fecha_sorteo+'</div>');
				$('#zona1').append(elemento);
				elemento = $('<div class="lhorasorteo">HORA</div>');
				$('#zona1').append(elemento);
				elemento = $('<div class="horasorteo">'+data.hora_sorteo+'</div>');
				$('#zona1').append(elemento);
				elemento = $('<div class="tipo_juego_lado"> '+data.juego+'</div>');
				$('#zona1').append(elemento);
				elemento = $('<div class="tipo_juego_lado_1"> '+data.juego+'</div>');
				$('#zona1').append(elemento);
				primerVez = false;
			}
			$(data.billetesZona1).each(
				function(){
					if(billetesZona1[this.posicion] == undefined || billetesZona1[this.posicion].numero != this.numero){
						billetesZona1[this.posicion] = this;

						crearAnimacionNuevoBillete(1, this, posicionZona1);

						/*if(this.posicion == '01'){
							//if(billetesZona1[this.posicion].vendido=='NO'){
							//	$('#primer_premio').fadeOut('slow',
							//		function() {
	   						//			$('#primer_premio').css('background-image','url(escribano_img/no_vendido.png)');
							//			$('#primer_premio').fadeIn();
							//		}
							//	);
							//}


							$('#zona1 #progresion').remove();

							var estilo = posicionZona1['progresion'];
								estilo.width = getAnchoByAlto(estilo.height);
								estilo['font-size'] = getTamanioTextoByAlto(estilo.height);
							var progresion = (this.numero % 11) + 1;
								progresion = progresion < 10 ? '0' + progresion : progresion;

							var elemento = $('<span id="progresion" class="billete">'+progresion+'</span>').css(estilo);


							setTimeout(function(){$('#zona1').append(elemento)}, 1000);
						}*/

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

					if(indice == '01'){
						$('#zona1 #progresion').remove();
						//$('#primer_premio').css('background-image','url(escribano_img/primero_premio.png)');
					}
				}
			}
		}

		function animarZona2(data){
			var posicionesEncontradas = [];

			$(data.billetesZona2).each(
				function(){
					if(billetesZona2[this.posicion] == undefined || billetesZona2[this.posicion].numero != this.numero || billetesZona2[this.posicion].fraccion != this.fraccion){
						billetesZona2[this.posicion] = this;

						crearAnimacionNuevoBillete(2, this, posicionZona2);

						return false;
					}
				}
			);

			$(data.billetesZona2).each(
				function(){
					posicionesEncontradas.push(this.posicion);
				}
			);

			for(indice in billetesZona2){
				if(posicionesEncontradas.indexOf(indice) == -1){
					billetesZona2[indice] = undefined;
					$('#zona2 #billete2_'+indice+', #zona2 #billete2_'+indice+'_fraccion').remove();
				}
			}
		}

		function animarZona3(data){
			var posicionesEncontradas = [];

			$(data.billetesZona3).each(
				function(){
					if(billetesZona3[this.posicion] == undefined || billetesZona3[this.posicion].numero != this.numero){
						billetesZona3[this.posicion] = this;

						crearAnimacionNuevoBillete(3, this, posicionZona3);

						//Localidad
						$('#zona3 #localidad').remove();
						var estilo = { top:'305px', left:'656px', width: '366px', height: '100px', 'font-size': '17px' };

						var elemento = $('<span id="localidad" class="billete">'+this['localidad'].join('<br><br>')+'</span>').css(estilo);
						$('#zona3').append(elemento);

						return false;
					}
				}
			);

			$(data.billetesZona3).each(
				function(){
					posicionesEncontradas.push(this.posicion);
				}
			);

			for(indice in billetesZona3){
				if(posicionesEncontradas.indexOf(indice) == -1){
					billetesZona3[indice] = undefined;
					$('#zona3 #billete3_'+indice).remove();

					if(indice == '01')
						$('#zona3 #id_agencia, #zona3 #localidad').remove();
				}
			}
		}

		function crearAnimacionNuevoBillete(zona, billete, posiciones){
			if(zona == 2){

				//BILLETE
				var id = 'billete'+zona+'_'+billete.posicion;
				$('#'+id).remove();

				var elemento = $('<span id="'+id+'" class="billete">'+billete.numero+'</span>');

				$('#zona'+zona).append(elemento);
				var inicial = posiciones[billete.posicion].billete.inicial;
					inicial.height = '0px';
					inicial['font-size'] = '0px';

				$('#'+id).css(inicial);

				var final = posiciones[billete.posicion].billete.final;
					final.width = getAnchoByAlto(final.height);
					final.top = (parseInt(inicial.top) - (parseInt(final.height) / 2))+'px';
					final.left = (parseInt(inicial.left) - (parseInt(final.width) / 2))+'px';
					final['font-size'] = getTamanioTextoByAlto(final.height);

				$('#'+id).animate(final, 1000);

				//FRACCION
				id = 'billete'+zona+'_'+billete.posicion+'_fraccion';
				$('#'+id).remove();

				elemento = $('<span id="'+id+'" class="billete">'+billete.fraccion+'</span>');

				$('#zona'+zona).append(elemento);

				inicial = posiciones[billete.posicion].fraccion.inicial;
				inicial.height = '0px';
				inicial['font-size'] = '0px';

				$('#'+id).css(inicial);

				final = posiciones[billete.posicion].fraccion.final;
				final.width = getAnchoByAlto(final.height);
				final.top = (parseInt(inicial.top) - (parseInt(final.height) / 2))+'px';
				final.left = (parseInt(inicial.left) - (parseInt(final.width) / 2))+'px';
				final['font-size'] = getTamanioTextoByAlto(final.height);

				$('#'+id).animate(final, 1000);
			}else{
				var id = 'billete'+zona+'_'+billete.posicion;
				$('#'+id).remove();

				var elemento = $('<span id="'+id+'" class="billete">'+billete.numero+'</span>');

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
		}

		function getAnchoByElement(elemento){
			return (parseInt($(elemento).css('height')) * 3) + 'px';
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
