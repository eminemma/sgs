<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<title>Sorteador Loteria de Cordoba S.E.</title>
	<link href="loteria_ordinaria_escribano_estilo.css" rel="stylesheet" type="text/css" />
	<script type="text/javascript" src="../../librerias/jquery/jquery-1.10.1.js"></script>
	<script type="text/javascript" src="../../js/funciones.js"></script>
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

		var datosSorteo = {
		};

		var posicionZona1 =	{
								'01':	{
											inicial:	{
															top: '252px',
															left: '317px'
														},
											final:		{
															height: '150px'
														}
										},

								'02':	{
											inicial:	{
															top: '200px',
															left: '781px'
														},
											final:		{
															height: '70px'
														}
										},

								'03':	{
											inicial:	{
															top: '200px',
															left: '1140px'
														},
											final:		{
															height: '70px'
														}
										},

								'04':	{
											inicial:	{
															top: '330px',
															left: '781px'
														},
											final:		{
															height: '70px'
														}
										},

								'05':	{
											inicial:	{
															top: '330px',
															left: '1140px'
														},
											final:		{
															height: '70px'
														}
										},

								'06':	{
											inicial:	{
															top: '472px',
															left: '229px'
														},
											final:		{
															height: '55px'
														}
										},

								'07':	{
											inicial:	{
															top: '534px',
															left: '229px'
														},
											final:		{
															height: '55px'
														}
										},

								'08':	{
											inicial:	{
															top: '596px',
															left: '229px'
														},
											final:		{
															height: '55px'
														}
										},

								'09':	{
											inicial:	{
															top: '656px',
															left: '229px'
														},
											final:		{
															height: '55px'
														}
										},

								'10':	{
											inicial:	{
															top: '718px',
															left: '229px'
														},
											final:		{
															height: '55px'
														}
										},

								'11':	{
											inicial:	{
															top: '472px',
															left: '583px'
														},
											final:		{
															height: '55px'
														}
										},

								'12':	{
											inicial:	{
															top: '534px',
															left: '583px'
														},
											final:		{
															height: '55px'
														}
										},

								'13':	{
											inicial:	{
															top: '596px',
															left: '583px'
														},
											final:		{
															height: '55px'
														}
										},

								'14':	{
											inicial:	{
															top: '656px',
															left: '583px'
														},
											final:		{
															height: '55px'
														}
										},

								'15':	{
											inicial:	{
															top: '718px',
															left: '583px'
														},
											final:		{
															height: '55px'
														}
										},

								'16':	{
											inicial:	{
															top: '472px',
															left: '930px'
														},
											final:		{
															height: '55px'
														}
										},

								'17':	{
											inicial:	{
															top: '534px',
															left: '930px'
														},
											final:		{
															height: '55px'
														}
										},

								'18':	{
											inicial:	{
															top: '596px',
															left: '930px'
														},
											final:		{
															height: '55px'
														}
										},

								'19':	{
											inicial:	{
															top: '656px',
															left: '930px'
														},
											final:		{
															height: '55px'
														}
										},

								'20':	{
											inicial:	{
															top: '718px',
															left: '930px'
														},
											final:		{
															height: '55px'
														}
										},

								'progresion':	{
													top: '449px',
													left: '1073px',
													height: '90px'
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
				'loteria_ordinaria_escribano_ajax.php?ale='+parseInt(Math.random() * 1000000000),
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
		var i = 0;
		function animarZona1(data){
			var posicionesEncontradas = [];
			if(i==0){
				datosSorteo = data.datosSorteo;
				crearDatosSorteo();
				i=1;
			}
			$(data.billetesZona1).each(
				function(){


					if(billetesZona1[this.posicion] == undefined || billetesZona1[this.posicion].numero != this.numero){
						billetesZona1[this.posicion] = this;

						crearAnimacionNuevoBillete(1, this, posicionZona1);



						if(this.posicion == '01'){
							/*if(billetesZona1[this.posicion].vendido=='NO'){
								$('#primer_premio').fadeOut('slow',
									function() {
	   									$('#primer_premio').css('background-image','url(escribano_img/no_vendido.png)');
										$('#primer_premio').fadeIn();
									}
								);
							}*/


							$('#zona1 #progresion').remove();

							var estilo = posicionZona1['progresion'];
								estilo.width = getAnchoByAlto(estilo.height);
								estilo['font-size'] = getTamanioTextoByAlto(estilo.height);
							var progresion = (this.numero % 11) + 1;
								progresion = progresion < 10 ? '0' + progresion : progresion;

							var elemento = $('<span id="progresion" class="billete">'+progresion+'</span>').css(estilo);
							setTimeout(function(){$('#zona1').append(elemento)}, 1000);
						}

						return false;
					}
				}
			);

			$(data.billetesZona1).each(
				function(){
					posicionesEncontradas.push(this.posicion);
				}
			);


			// Aca vemos si se borr?? alguno.
			// Si algun valor de "billeteZona1" no est?? en el array que lleg?? en el DATA, hay que borrarlo.
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

		function crearDatosSorteo(){
			var elemento = $('<span id="sorteo" class="sorteo">'+datosSorteo.sorteo+'</span>');
			$('#zona1').append(elemento);
			var elemento = $('<span id="fecha_sorteo" class="fecha_sorteo">'+datosSorteo.fecha_sorteo+'</span>');
			$('#zona1').append(elemento);
			var elemento = $('<span id="hora_sorteo" class="hora_sorteo">'+datosSorteo.hora_sorteo+'</span>');
			$('#zona1').append(elemento);
			for(i=0;i<Object.keys(datosSorteo.programa).length;i++){
				var elemento = $('<span id="premio'+(i+1)+'" class="premio'+(i+1)+'">'+datosSorteo.programa[i].tipo_premio+' '+datosSorteo.programa[i].monto+'</span>');
				$('#zona1').append(elemento);
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
