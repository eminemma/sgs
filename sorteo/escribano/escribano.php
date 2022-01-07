<?php
/*

LEER COMENTARIO CODIGO PARA MOSTRAR GANADORES EN EL SORTEOS SALE O SALE

 */
session_start();
include_once '../../db.php';

try {
    $rs_sorteo = sql("  SELECT
                                                TO_CHAR(FECHA_SORTEO,'dd/mm/yyyy') AS FECHA_SORTEO
                                            FROM
                                                sgs.T_SORTEO
                                            WHERE
                                                sorteo = ?",
        array($_SESSION['sorteo']));
} catch (exception $e) {
    die($db->ErrorMsg());
}
$row_sorteo   = $rs_sorteo->FetchNextObject($toupper = true);
$fecha_sorteo = $row_sorteo->FECHA_SORTEO;
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Sorteador Loteria de Cordoba S.E.</title>
    <link href="w3.css" rel="stylesheet" type="text/css" />
    <link href="escribano_estilo.php" rel="stylesheet" type="text/css" />
    <script type="text/javascript" src="../../librerias/jquery/jquery-1.10.1.js"></script>
    <script type="text/javascript" src="../../js/funciones.js?17072017"></script>
    <style type="text/css">
        .w3-xlarge{
            font-size: 47px !important;
        }
        .w3-modal-content {
            width: 95% !important;
        }
        .w3-large{
            font-size: 35px !important;
        }
    </style>
</head>

<body>
    <div id="zona1">
      <!--   <div id="sorteo">
            <?php echo $_SESSION['sorteo']; ?>
        </div>-->
        <div id="fecha_sorteo">
            <?php echo $fecha_sorteo; ?>
        </div>

        <?php if (strtoupper($_SESSION['sale_o_sale']) == 'SI') {?>
        <div id="primer_premio" style="background-image:url('escribano_img/primer_premio_5062.png');"></div>
        <?php }?>

        <div id="resultado_primer_premio" ></div>
    </div>


    <div id="zona2">
        <div id="sorteo_2">
            <?php //echo $_SESSION['sorteo']; ?>
        </div>
        <div id="fecha_sorteo_2">
            <?php //echo $fecha_sorteo; ?>
        </div>
<span id="billete2_21" class="billete" style="top: 242px; left: 235px; height: 30px; font-size: 28.5px; width: 90px;"></span>
<span id="billete2_21_fraccion" class="billete" style="top: 242px; height: 30px; font-size: 28.5px; width: 90px; left: 445px;"></span>
<span id="billete2_22" class="billete" style="top: 301px; left: 235px; height: 30px; font-size: 28.5px; width: 90px;"></span>
<span id="billete2_22_fraccion" class="billete" style="top: 301px; left: 445px; height: 30px; font-size: 28.5px; width: 90px;"></span>
<span id="billete2_23" class="billete" style="top: 360px; left: 235px; height: 30px; font-size: 28.5px; width: 90px;"></span>
<span id="billete2_23_fraccion" class="billete" style="top: 360px; left: 445px; height: 30px; font-size: 28.5px; width: 90px;"></span>
<span id="billete2_24" class="billete" style="top: 419px; left: 235px; height: 30px; font-size: 28.5px; width: 90px;"></span>
<span id="billete2_24_fraccion" class="billete" style="top: 419px; left: 445px; height: 30px; font-size: 28.5px; width: 90px;"></span>
<span id="billete2_25" class="billete" style="top: 478px; left: 235px; height: 30px; font-size: 28.5px; width: 90px;"></span>
<span id="billete2_25_fraccion" class="billete" style="top: 478px; left: 445px; height: 30px; font-size: 28.5px; width: 90px;"></span>
<span id="billete2_26" class="billete" style="top: 537px; left: 235px; height: 30px; font-size: 28.5px; width: 90px;"></span>
<span id="billete2_26_fraccion" class="billete" style="top: 537px; left: 445px; height: 30px; font-size: 28.5px; width: 90px;"></span>

    </div>


    <div id="zona3">
        <!-- <div id="sorteo"><?php echo $_SESSION['sorteo']; ?></div>-->
            <div id="fecha_sorteo" style="left: 1177px;top: 70px;"><?php echo $fecha_sorteo; ?></div>
    </div>



    <!--
        <div id="zona1"><div id="primer_premio"></div></div>
        <div id="zona2"></div>
        <div id="zona3"></div> -->
        <div id="zona4"><!-- <div id="sorteo"><?php echo $_SESSION['sorteo']; ?></div>
            <div id="fecha_sorteo"><?php echo $fecha_sorteo ?></div> --></div>
    <!-- The Modal -->



    <div id="ventana_primer_premio_vendido" class="w3-modal">
        <div class="w3-modal-content">
            <header class="w3-container" style="background-color: #881518;color: #fff!important;">
                <h2 style="font-size: 17px;">Sorteo <?php echo $_SESSION['sorteo'] ?> - Vendido En Agencia  </h2>
            </header>
            <div class="w3-container">
                <table id="ganadores_primer_premio" class="w3-table w3-bordered w3-striped w3-border test w3-hoverable">
                    <thead>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>
            <footer class="w3-container" style="background-color: #881518;color: #fff!important;">
                <div id="tiempo_cierre" style="text-align: right;"></div>
                <br>
            </footer>
        </div>
    </div>
    <div id="ventana_primer_premio_no_vendido" class="w3-modal w3-animate-opacity">
        <div class="w3-modal-content">
            <header class="w3-container w3-red">
                <h2 style="font-size: 17px;">Sorteo Reyes 2017 - Resultado</h2>
            </header>
            <div class="w3-container w3-center" style="background-color: #ed2a2a;">
                <img src="escribano_img/no_vendido_4813.png" style="width: 600px;" class="w3-round w3-margin" alt="Norway">
            </div>
            <footer class="w3-container w3-red">
                <br>
            </footer>
        </div>
    </div>



        <script type="text/javascript">
                        var i = null;
            $(document).ready(
                function() {
                    buscar_informacion();
                    //i = setInterval(buscar_informacion, 1300);

                }
            );

            var posicionZona1 = {
                '01': {
                    inicial: {
                        top: '275px',
                        left: '320px'
                    },
                    final: {
                        height: '135px'
                    }
                },
                '02': {
                    inicial: {
                        top: '494px',
                        left: '185px'
                    },
                    final: {
                        height: '71px'
                    }
                },
                '03': {
                    inicial: {
                        top: '494px',
                        left: '460px'
                    },
                    final: {
                        height: '71px'
                    }
                },
                '04': {
                    inicial: {
                        top: '686px',
                        left: '185px'
                    },
                    final: {
                        height: '71px'
                    }
                },
                '05': {
                    inicial: {
                        top: '686px',
                        left: '460px'
                    },
                    final: {
                        height: '71px'
                    }
                },
                '06': {
                    inicial: {
                        top: '167px',
                        left: '810px'
                    },
                    final: {
                        height: '45px'
                    }
                },
                '07': {
                    inicial: {
                        top: '203px',
                        left: '810px'
                    },
                    final: {
                        height: '45px'
                    }
                },
                '08': {
                    inicial: {
                        top: '241px',
                        left: '810px'
                    },
                    final: {
                        height: '45px'
                    }
                },
                '09': {
                    inicial: {
                        top: '276px',
                        left: '810px'
                    },
                    final: {
                        height: '45px'
                    }
                },
                '10': {
                    inicial: {
                        top: '316px',
                        left: '810px'
                    },
                    final: {
                        height: '45px'
                    }
                },
                '11': {
                    inicial: {
                        top: '410px',
                        left: '823px'
                    },
                    final: {
                        height: '45px'
                    }
                },
                '12': {
                    inicial: {
                        top: '450px',
                        left: '823px'
                    },
                    final: {
                        height: '45px'
                    }
                },
                '13': {
                    inicial: {
                        top: '490px',
                        left: '823px'
                    },
                    final: {
                        height: '45px'
                    }
                },
                '14': {
                    inicial: {
                        top: '527px',
                        left: '823px'
                    },
                    final: {
                        height: '45px'
                    }
                },
                '15': {
                    inicial: {
                        top: '570px',
                        left: '823px'
                    },
                    final: {
                        height: '45px'
                    }
                },
                '16': {
                    inicial: {
                        top: '410px',
                        left: '1130px'
                    },
                    final: {
                        height: '45px'
                    }
                },
                '17': {
                    inicial: {
                        top: '450px',
                        left: '1130px'
                    },
                    final: {
                        height: '45px'
                    }
                },
                '18': {
                    inicial: {
                        top: '490px',
                        left: '1130px'
                    },
                    final: {
                        height: '45px'
                    }
                },
                '19': {
                    inicial: {
                        top: '527px',
                        left: '1130px'
                    },
                    final: {
                        height: '45px'
                    }
                },
                '20': {
                    inicial: {
                        top: '570px',
                        left: '1130px'
                    },
                    final: {
                        height: '45px'
                    }
                },
                'progresion': {
                    top: '273px',
                    left: '1056px',
                    height: '65px'
                }
            };

            var posicionZona2 = {
                '21': {
                    billete: {
                        inicial: {
                            top: '257px',
                            left: '280px'
                        },
                        final: {
                            height: '30px'
                        }
                    },
                    fraccion: {
                        inicial: {
                            top: '257px',
                            left: '490px;'
                        },
                        final: {
                            height: '30px'
                        }
                    }
                },
                '22': {
                    billete: {
                        inicial: {
                            top: '316px',
                            left: '280px'
                        },
                        final: {
                            height: '30px'
                        }
                    },
                    fraccion: {
                        inicial: {
                            top: '316px',
                            left: '490px'
                        },
                        final: {
                            height: '30px'
                        }
                    }
                },
                '23': {
                    billete: {
                        inicial: {
                            top: '375px',
                            left: '280px'
                        },
                        final: {
                            height: '30px'
                        }
                    },
                    fraccion: {
                        inicial: {
                            top: '375px',
                            left: '490px'
                        },
                        final: {
                            height: '30px'
                        }
                    }
                },
                '24': {
                    billete: {
                        inicial: {
                            top: '434px',
                            left: '280px'
                        },
                        final: {
                            height: '30px'
                        }
                    },
                    fraccion: {
                        inicial: {
                            top: '434px',
                            left: '490px'
                        },
                        final: {
                            height: '30px'
                        }
                    }
                },
                '25': {
                    billete: {
                        inicial: {
                            top: '493px',
                            left: '280px'
                        },
                        final: {
                            height: '30px'
                        }
                    },
                    fraccion: {
                        inicial: {
                            top: '493px',
                            left: '490px'
                        },
                        final: {
                            height: '30px'
                        }
                    }
                }
				,'26': {
                    billete: {
                        inicial: {
                            top: '552px',
                            left: '280px'
                        },
                        final: {
                            height: '30px'
                        }
                    },
                    fraccion: {
                        inicial: {
                            top: '552px',
                            left: '490px'
                        },
                        final: {
                            height: '30px'
                        }
                    }
                }

            };

            var posicionZona3 = {
                '01': {
                    inicial: {
                        top: '565px',
                        left: '460px'
                    },
                    final: {
                        height: '230px'
                    }
                }
            };

            var posicionZona4 = {
                '01': {
                    inicial: {
                        top: '395px',
                        left: '350px'
                    },
                    final: {
                        height: '160px'
                    }
                }
            };

            var billetesZona1 = [];
            var billetesZona2 = [];
            var billetesZona3 = [];
            var billetesZona4 = [];

            var zonaMostrando = 'zona1';
            var intervaloSorteoEntero = null;
            var intervaloSorteoEnteroFinalizo = false;

            var sortea_hasta_sale_resumenContador = 0;
            var sortea_hasta_sale_ganadores = null;
            var intervalo_sortea_hasta_sale = false;
            var animarGanadoresSorteaHastaQueSale = true;
            var tiempoPrimerPremio = null;

			var intervalo_para_aleatorio = false;

			var intervalo_para_aleatorio_21 = null;
			var flag_intervalo_para_aleatorio_21 = false;
			var intervalo_para_aleatorio_22 = null;
			var flag_intervalo_para_aleatorio_22 = false;
			var intervalo_para_aleatorio_23 = null;
			var flag_intervalo_para_aleatorio_23 = false;
			var intervalo_para_aleatorio_24 = null;
			var flag_intervalo_para_aleatorio_24 = false;
			var intervalo_para_aleatorio_25 = null;
			var flag_intervalo_para_aleatorio_25 = false;
			var intervalo_para_aleatorio_26 = null;
			var flag_intervalo_para_aleatorio_26 = false;

            var intervalPrimeroPremio = null;

            var primerElementoZona4 = null;
            var ultimoElementoZona4 = null;

            function buscar_informacion() {
                $.getJSON(
                    'escribano_ajax.php?ale=' + parseInt(Math.random() * 1000000000),
                    function(data) {

                        if (data.zonaMostrando == 'zona1' && zonaMostrando != 'zona1') {
                            $('#zona2, #zona3, #zona4').hide();
                            $('#zona1').fadeIn();
                            zonaMostrando = 'zona1';

                        } else if (data.zonaMostrando == 'zona2' && zonaMostrando != 'zona2') {
                            $('#zona1, #zona3, #zona4').hide();
                            $('#zona2').fadeIn();
                            zonaMostrando = 'zona2';

                        } else if (data.zonaMostrando == 'zona3' && zonaMostrando != 'zona3') {
                            $('#zona1, #zona2, #zona4').hide();
                            $('#zona3').fadeIn();
                            sortea_hasta_sale_resumenContador = 0;
                            zonaMostrando = 'zona3';
                        } else if (data.zonaMostrando == 'zona4' && zonaMostrando != 'zona4') {
                            $('#zona1, #zona2, #zona3').hide();
                            $('#zona4').fadeIn();
                            zonaMostrando = 'zona4';
                        }

                        if (zonaMostrando == 'zona1') {
                            //$('#primer_premio').fadeIn();
                            animarZona1(data);
                        } else if (zonaMostrando == 'zona2'){

							if (intervalo_para_aleatorio_21 == null){
                                intervalo_para_aleatorio_21 = setInterval("animar_aleatorio_21()", 100);
							}
							if (intervalo_para_aleatorio_22 == null){
								intervalo_para_aleatorio_22 = setInterval("animar_aleatorio_x(22)", 100);
							}
							if (intervalo_para_aleatorio_23 == null){
								intervalo_para_aleatorio_23 = setInterval("animar_aleatorio_x(23)", 100);
							}
							if (intervalo_para_aleatorio_24 == null){
								intervalo_para_aleatorio_24 = setInterval("animar_aleatorio_x(24)", 100);
							}
							if (intervalo_para_aleatorio_25 == null){
								intervalo_para_aleatorio_25 = setInterval("animar_aleatorio_x(25)", 100);
							}
							if (intervalo_para_aleatorio_26 == null){
								intervalo_para_aleatorio_26 = setInterval("animar_aleatorio_x(26)", 100);
							}

							animarZona2(data);

						}

                        else if (zonaMostrando == 'zona3') {

							animarZona3(data);

                        } else if (zonaMostrando == 'zona4') {
                            primerElementoZona4 = data.parametroZona4.primer_elemento;
                            ultimoElementoZona4 = data.parametroZona4.ultimo_elemento;

                            if (intervaloSorteoEntero == null)
                                intervaloSorteoEntero = setInterval("dibujarSorteoEntero()", 100);
                            animarZona4(data);
                        }


                        // modificacion para optimizacion de rendimiento en navegador
                       setTimeout(buscar_informacion, 1300);



                    }
                );
            }

            function animarZona1(data) {
                var posicionesEncontradas = [];

                $(data.billetesZona1).each(
                    function() {
                        if (billetesZona1[this.posicion] == undefined || billetesZona1[this.posicion].numero != this.numero) {
                            billetesZona1[this.posicion] = this;

                            crearAnimacionNuevoBillete(1, this, posicionZona1);

                            if (this.posicion == '01' && this.sale_o_sale == 'SI') {

                                if (billetesZona1[this.posicion].vendido == 'NO') {

                                    $('#resultado_primer_premio').fadeOut('slow',
                                        function() {
                                            //CODIGO PARA MOSTRAR GANADORES EN EL SORTEOS SALE O SALE
                                             $('#primer_premio').fadeOut();
                                             $('#resultado_primer_premio').css('background-size', '375px 82px');
                                            $('#resultado_primer_premio').css('top', '131px');
                                            $('#resultado_primer_premio').css('height', '71px');
                                            $('#resultado_primer_premio').css('left', '142px');
                                            $('#resultado_primer_premio').css('width', '384px');
                                            $('#resultado_primer_premio').css('background-image', 'url(escribano_img/no_vendido_<?php echo $_SESSION['sorteo']; ?>.png)');
                                            $('#resultado_primer_premio').fadeIn();



                                        }
                                    );
                                    $( "body").unbind( "keydown" );

                                } else {
                                    $('#resultado_primer_premio').fadeOut('slow',
                                        function() {
                                        //CODIGO PARA MOSTRAR GANADORES EN EL SORTEOS SALE O SALE
                                            /*$('#primer_premio').fadeOut();
                                            $('#resultado_primer_premio').css('background-size', '250px 50px');
                                            $('#resultado_primer_premio').css('top', '145px');
                                            $('#resultado_primer_premio').css('height', '50px');
                                            $('#resultado_primer_premio').css('left', '211px');
                                            $('#resultado_primer_premio').css('background-image', 'url(escribano_img/vendido_<?php echo $_SESSION['sorteo']; ?>.png)');
                                            $('#resultado_primer_premio').fadeIn();*/
                                            mostrarPrimerPremio();
                                        }
                                    );


                                //CODIGO PARA MOSTRAR GANADORES EN EL SORTEOS SALE O SALE
                                   $("body").keydown(function(event) {
                                        if (event.ctrlKey && data.zonaMostrando == 'zona1') {
                                        clearInterval(tiempoPrimerPremio);
                                        setTimeout(function() { $('#ventana_primer_premio_vendido').fadeIn("slow"); }, 3000);

                                        var j = 0;

                                        $(data.billetesZona1).each(

                                            function() {
                                                if(this['posicion'] == '01' && this['vendido'] == 'SI'){
                                                    $( "#ganadores_primer_premio" ).removeClass( "w3-xlarge w3-large w3-tiny" );
                                                    if(this['localidad'].length<=6){
                                                        $( "#ganadores_primer_premio" ).addClass( "w3-xlarge" );
                                                    }

                                                    if(this['localidad'].length>6 && this['localidad'].length<=12){
                                                        $( "#ganadores_primer_premio" ).addClass( "w3-large" );
                                                    }
                                                    if(this['localidad'].length > 12){
                                                        $( "#ganadores_primer_premio" ).css("font-size" ,"17px!important" );
                                                    }

                                                    $('#ganadores_primer_premio > tbody').empty();
                                                    for (j = 0; j < this['localidad'].length; j++) {
                                                        var elemento = '<tr><td>' + this['localidad'][j] + '</td></tr>';
                                                        $('#ganadores_primer_premio > tbody').append(elemento);

                                                    }

                                                }
                                            }
                                        );

                                        if((j * 5000) < 15000){
                                            j = 5;
                                        }
                                        var tiempo = j * 5000 * 0.001;

                                        display = document.querySelector('#tiempo_cierre');
                                        startTimer(tiempo,display);

                                        setTimeout(function() {

                                            $('#ventana_primer_premio_vendido').fadeOut("slow");
                                        }, j * 5000);


                                        }
                                    });

                                }




                            }

                            if (this.posicion == '01'){
                                 $('#zona1 #progresion').remove();

                                var estilo = posicionZona1['progresion'];
                                estilo.width = getAnchoByAlto(estilo.height);
                                estilo['font-size'] = getTamanioTextoByAlto(estilo.height);
                                var progresion = (this.numero % 11) + 1;
                                progresion = progresion < 10 ? '0' + progresion : progresion;

                                var elemento = $('<span id="progresion" class="billete">' + progresion + '</span>').css(estilo);
                                setTimeout(function() {
                                    $('#zona1').append(elemento)
                                }, 1000);
                            }

                            return false;
                        }
                    }
                );

                $(data.billetesZona1).each(
                    function() {
                        posicionesEncontradas.push(this.posicion);
                    }
                );


                // Aca vemos si se borró alguno.
                // Si algun valor de "billeteZona1" no está en el array que llegó en el DATA, hay que borrarlo.
                for (indice in billetesZona1) {
                    if (posicionesEncontradas.indexOf(indice) == -1) {
                        billetesZona1[indice] = undefined;
                        $('#zona1 #billete1_' + indice).remove();

                        if (indice == '01') {
                            $('#zona1 #progresion').remove();
                            $('#resultado_primer_premio').css('background-image','url()');
                            $('#primer_premio').fadeIn();
                            clearInterval(intervalPrimeroPremio);
                           // $('#primer_premio').css('background-image','url(escribano_img/primer_premio_4910.png)');
                        }
                    }
                }
            }



			function animar_aleatorio_21(){

				var maximo=Math.floor((Math.random() * 50999) + 1);
				var maximof=Math.floor((Math.random() * 10) + 1);

				$('#billete2_21').remove();
				$('#billete2_21_fraccion').remove();

				var elemento_21 = $('<span id="billete2_21" class="billete" style="top: 242px; left: 235px; height: 30px; font-size: 28.5px; width: 90px;">' + pad(maximo, 5) + '</span>');
                $('#zona2').append(elemento_21);
				var elemento_21_f = $('<span id="billete2_21_fraccion" class="billete" style="top: 242px; height: 30px; font-size: 28.5px; width: 90px; left: 445px;">' + pad(maximof, 2) + '</span>');
                $('#zona2').append(elemento_21_f);

			}
			function animar_aleatorio_x(x){

				var maximo=Math.floor((Math.random() * 50999) + 1);
				var maximof=Math.floor((Math.random() * 10) + 1);

				$('#billete2_'+x).remove();
				$('#billete2_'+x+'_fraccion').remove();

				var contenido = contenidof = '';

				if(x==21)contenido = '<span id="billete2_21" class="billete" style="top: 242px; left: 235px; height: 30px; font-size: 28.5px; width: 90px;">' + pad(maximo, 5) + '</span>';
				if(x==22)contenido = '<span id="billete2_22" class="billete" style="top: 301px; left: 235px; height: 30px; font-size: 28.5px; width: 90px;">' + pad(maximo, 5) + '</span>';
				if(x==23)contenido = '<span id="billete2_23" class="billete" style="top: 360px; left: 235px; height: 30px; font-size: 28.5px; width: 90px;">' + pad(maximo, 5) + '</span>';
				if(x==24)contenido = '<span id="billete2_24" class="billete" style="top: 419px; left: 235px; height: 30px; font-size: 28.5px; width: 90px;">' + pad(maximo, 5) + '</span>';
				if(x==25)contenido = '<span id="billete2_25" class="billete" style="top: 478px; left: 235px; height: 30px; font-size: 28.5px; width: 90px;">' + pad(maximo, 5) + '</span>';
				if(x==26)contenido = '<span id="billete2_26" class="billete" style="top: 537px; left: 235px; height: 30px; font-size: 28.5px; width: 90px;">' + pad(maximo, 5) + '</span>';

				if(x==21)contenidof = '<span id="billete2_21_fraccion" class="billete" style="top: 242px; height: 30px; font-size: 28.5px; width: 90px; left: 445px;">' + pad(maximof, 2) + '</span>';
				if(x==22)contenidof = '<span id="billete2_22_fraccion" class="billete" style="top: 301px; left: 445px; height: 30px; font-size: 28.5px; width: 90px;">' + pad(maximof, 2) + '</span>';
				if(x==23)contenidof = '<span id="billete2_23_fraccion" class="billete" style="top: 360px; left: 445px; height: 30px; font-size: 28.5px; width: 90px;">' + pad(maximof, 2) + '</span>';
				if(x==24)contenidof = '<span id="billete2_24_fraccion" class="billete" style="top: 419px; left: 445px; height: 30px; font-size: 28.5px; width: 90px;">' + pad(maximof, 2) + '</span>';
				if(x==25)contenidof = '<span id="billete2_25_fraccion" class="billete" style="top: 478px; left: 445px; height: 30px; font-size: 28.5px; width: 90px;">' + pad(maximof, 2) + '</span>';
				if(x==26)contenidof = '<span id="billete2_26_fraccion" class="billete" style="top: 537px; left: 445px; height: 30px; font-size: 28.5px; width: 90px;">' + pad(maximof, 2) + '</span>';

				var elemento = $(contenido);
                $('#zona2').append(elemento);

				var elemento_f = $(contenidof);
                $('#zona2').append(elemento_f);

			}


            function animarZona2(data) {

                var posicionesEncontradas = [];
				/*
				function animar_aleatorio(){
					var maximo=parseInt('50999');
					$('#aleatorio').html(  pad(Math.floor((Math.random()*maximo)+1),5) );
				}
				intervalo_para_aleatorio = setInterval(animar_aleatorio, 100);
				*/



				/*
                $(data.billetesZona2).each(
                    function() {
                        if (billetesZona2[this.posicion] == undefined || billetesZona2[this.posicion].numero != this.numero || billetesZona2[this.posicion].fraccion != this.fraccion) {
                            billetesZona2[this.posicion] = this;
                            crearAnimacionNuevoBillete(2, this, posicionZona2);
							if(parseInt(this.posicion)==21){
								flag_intervalo_para_aleatorio_21 == true;
							}
                            return false;
                        }
                    }
                );
				*/

                $(data.billetesZona2).each(
                    function() {
                        posicionesEncontradas.push(this.posicion);
                    }
                );

				var elemento = '';

                /*
				for (indice in billetesZona2) {
					elemento = $('<span id="bill_'+ indice +'" class="billete">' + indice + '<br> </span>');
                    if (posicionesEncontradas.indexOf(indice) == -1) {
						billetesZona2[indice] = undefined;
                        //$('#zona2 #billete2_' + indice + ', #zona2 #billete2_' + indice + '_fraccion').remove();
						//intervalo_para_aleatorio_21 = setInterval(animar_aleatorio_21, 100);
                    }
                }
				*/

				var x21 = x22 = x23 = x24 = x25 = x26 = false;


				for (i = 21; i < 27; i++) {
					$(data.billetesZona2).each(
							function() {

								if (billetesZona2[this.posicion] == undefined || billetesZona2[this.posicion].numero != this.numero || billetesZona2[this.posicion].fraccion != this.fraccion) {

									if (parseInt(this.posicion) == parseInt(i) ){

										if(parseInt(i) == 21){
											x21 = true;
											clearInterval(intervalo_para_aleatorio_21);
											flag_intervalo_para_aleatorio_21 =  true;
											billetesZona2[this.posicion] = this;
											crearAnimacionNuevoBillete(2, this, posicionZona2);
										}
										if(parseInt(i) == 22){
											x22 = true;
											clearInterval(intervalo_para_aleatorio_22);
											flag_intervalo_para_aleatorio_22 =  true;
											billetesZona2[this.posicion] = this;
											crearAnimacionNuevoBillete(2, this, posicionZona2);
										}
										if(parseInt(i) == 23){
											x23 = true;
											clearInterval(intervalo_para_aleatorio_23);
											flag_intervalo_para_aleatorio_23 =  true;
											billetesZona2[this.posicion] = this;
											crearAnimacionNuevoBillete(2, this, posicionZona2);
										}
										if(parseInt(i) == 24){
											x24 = true;
											clearInterval(intervalo_para_aleatorio_24);
											flag_intervalo_para_aleatorio_24 =  true;
											billetesZona2[this.posicion] = this;
											crearAnimacionNuevoBillete(2, this, posicionZona2);
										}
										if(parseInt(i) == 25){
											x25 = true;
											clearInterval(intervalo_para_aleatorio_25);
											flag_intervalo_para_aleatorio_25 =  true;
											billetesZona2[this.posicion] = this;
											crearAnimacionNuevoBillete(2, this, posicionZona2);
										}
										if(parseInt(i) == 26){
											x26 = true;
											clearInterval(intervalo_para_aleatorio_26);
											flag_intervalo_para_aleatorio_26 =  true;
											billetesZona2[this.posicion] = this;
											crearAnimacionNuevoBillete(2, this, posicionZona2);
										}

										return false;


									}

								}
							}
						);

				}

				/*
				if(flag_intervalo_para_aleatorio_21 == false){
					intervalo_para_aleatorio_21 = setInterval(animar_aleatorio_21, 100);
				}else{
				}
				*/

				for (indice in billetesZona2) {
					elemento = $('<span id="bill_'+ indice +'" class="billete">' + indice + '<br> </span>');
                    if (posicionesEncontradas.indexOf(indice) == -1) {
						billetesZona2[indice] = undefined;
                        //$('#zona2 #billete2_' + indice + ', #zona2 #billete2_' + indice + '_fraccion').remove();
						//intervalo_para_aleatorio_21 = setInterval(animar_aleatorio_21, 100);
                    }
                }

            }

            function animarZona3(data) {
                var posicionesEncontradas = [];

                $(data.billetesZona3).each(
                    function() {
                        if (billetesZona3[this.posicion] == undefined || billetesZona3[this.posicion].numero != this.numero) {
                            billetesZona3[this.posicion] = this;

                            if(this.extracciones != "0"){
                                crearAnimacionNuevoBillete(3, this, posicionZona3);

                                //Localidad
                                $('#zona3 #localidad').remove();
                                sortea_hasta_sale_ganadores = this['localidad'];
                                intervalo_sortea_hasta_sale = setInterval("animarZona3Ganador();", 3000);
                            }


                            return false;
                        }
                    }
                );

                $(data.billetesZona3).each(
                    function() {
                        posicionesEncontradas.push(this.posicion);
                    }
                );

                for (indice in billetesZona3) {
                    if (posicionesEncontradas.indexOf(indice) == -1) {
                        billetesZona3[indice] = undefined;
                        $('#zona3 #billete3_' + indice).remove();
                        sortea_hasta_sale_ganadores= [];
                        sortea_hasta_sale_resumenContador = 0;
                        animarGanadoresSorteaHastaQueSale = true;
                        clearInterval(intervalo_sortea_hasta_sale);
                        if (indice == '01')
                            $('#zona3 #id_agencia, #zona3 #localidad').remove();
                    }
                }
            }

            function animarZona3Ganador() {

                $('#zona3 #localidad').remove();
                if(sortea_hasta_sale_ganadores[sortea_hasta_sale_resumenContador] != undefined){
                    var estilo = { top: '500px', left: '940px', width: '254px', height: '220px', 'font-size': '30px', 'text-align': 'left' };
                    var elemento = $('<span id="localidad" class="billete2">' + sortea_hasta_sale_ganadores[sortea_hasta_sale_resumenContador] + '<br> </span>').css(estilo);
                    $('#zona3').append(elemento);

                    sortea_hasta_sale_resumenContador++;

                    if(animarGanadoresSorteaHastaQueSale && (sortea_hasta_sale_resumenContador >= sortea_hasta_sale_ganadores.length)){

                        setTimeout(function() { $('#ventana_primer_premio_vendido').fadeIn("slow"); }, 3000);

                        var j = 0;


                        $( "#ganadores_primer_premio" ).removeClass( "w3-xlarge w3-large w3-tiny" );
                        if(sortea_hasta_sale_ganadores.length<=6){
                            $( "#ganadores_primer_premio" ).addClass( "w3-xlarge" );
                        }
                        if(sortea_hasta_sale_ganadores.length>6 && sortea_hasta_sale_ganadores.length<=12){
                            $( "#ganadores_primer_premio" ).addClass( "w3-large" );
                        }
                        if(sortea_hasta_sale_ganadores.length > 12){
                            $( "#ganadores_primer_premio" ).css("font-size" ,"17px!important" );
                        }

                        $('#ganadores_primer_premio > tbody').empty();
                        for (j = 0; j < sortea_hasta_sale_ganadores.length; j++) {
                            var elemento = '<tr><td>' + sortea_hasta_sale_ganadores[j] + '</td></tr>';
                            $('#ganadores_primer_premio > tbody').append(elemento);

                        }


                        if((j * 5000) < 15000){
                            j = 5;
                        }
                        var tiempo = j * 5000 * 0.001;
                        display = document.querySelector('#tiempo_cierre');
                        startTimer(tiempo,display);



                        setTimeout(function() {

                            $('#ventana_primer_premio_vendido').fadeOut("slow");

                        }, j * 5000);

                         animarGanadoresSorteaHastaQueSale = false;
                    }

                     if (sortea_hasta_sale_resumenContador >= sortea_hasta_sale_ganadores.length)
                        sortea_hasta_sale_resumenContador = 0;
                }

            }

            function animarZona4(data) {
                var posicionesEncontradas = [];
                $(data.billetesZona4).each(
                    function() {
                        if (typeof this == 'object') {
                            //billetesZona4[this.posicion] = this;
                            clearInterval(intervaloSorteoEntero);
                            intervaloSorteoEnteroFinalizo = true;

                            $('#premio_sorteo_entero').remove();
                            var elemento = $('<span id="premio_sorteo_entero" class="billete">' + this.numero + '</span>');
                            $('#zona4').append(elemento);

                            /* $('#agencia_sorteo_entero').remove();
                             var elemento = $('<span id="agencia_sorteo_entero" class="billete">'+this.agencia+'</span>');
                             $('#zona4').append(elemento);

                             $('#delegacion_sorteo_entero').remove();
                             var elemento = $('<span id="delegacion_sorteo_entero" class="billete">'+this.sucursal+'</span>');
                             $('#zona4').append(elemento);

                             $('#localidad_sorteo_entero').remove();
                             var elemento = $('<span id="localidad_sorteo_entero" class="billete">'+this.localidad+'</span>');
                             $('#zona4').append(elemento);*/
                            // crearAnimacionNuevoBillete(3, this, posicionZona4);

                            //Localidad
                            /*$('#zona4 #localidad').remove();
                            var estilo = {top: '305px', left: '656px', width: '366px', height: '100px', 'font-size': '17px'};

                            var elemento = $('<span id="localidad" class="billete">' + this['localidad'].join('<br><br>') + '</span>').css(estilo);
                            $('#zona4').append(elemento);*/

                            return false;
                        }
                    }
                );

                $(data.billetesZona4).each(
                    function() {
                        posicionesEncontradas.push(this.posicion);
                    }
                );
                // console.log(typeof posicionesEncontradas);
                if (posicionesEncontradas.length == 0) {
                    $('#premio_sorteo_entero').remove();
                    /* $('#agencia_sorteo_entero').remove();
                     $('#delegacion_sorteo_entero').remove();
                     $('#localidad_sorteo_entero').remove();*/

                }
                if (intervaloSorteoEnteroFinalizo == true) {
                    clearInterval(intervaloSorteoEntero);
                    intervaloSorteoEntero = null;
                    intervaloSorteoEnteroFinalizo = false;
                }
            }

            function dibujarSorteoEntero() {
                var numero = Math.floor((Math.random() * ultimoElementoZona4) + primerElementoZona4);
                $('#premio_sorteo_entero').remove();
                var elemento = $('<span id="premio_sorteo_entero" class="billete">' + pad(numero, 5) + '</span>');
                $('#zona4').append(elemento);
            }

            function crearAnimacionNuevoBillete(zona, billete, posiciones) {
                if (zona == 2) {

                    //BILLETE
                    var id = 'billete' + zona + '_' + billete.posicion;
                    $('#' + id).remove();

                    var elemento = $('<span id="' + id + '" class="billete">' + billete.numero + '</span>');

                    $('#zona' + zona).append(elemento);
                    var inicial = posiciones[billete.posicion].billete.inicial;
                    inicial.height = '0px';
                    inicial['font-size'] = '0px';

                    $('#' + id).css(inicial);

                    var final = posiciones[billete.posicion].billete.final;
                    final.width = getAnchoByAlto(final.height);
                    final.top = (parseInt(inicial.top) - (parseInt(final.height) / 2)) + 'px';
                    final.left = (parseInt(inicial.left) - (parseInt(final.width) / 2)) + 'px';
                    final['font-size'] = getTamanioTextoByAlto(final.height);

                    $('#' + id).animate(final, 1000);

                    //FRACCION
                    id = 'billete' + zona + '_' + billete.posicion + '_fraccion';
                    $('#' + id).remove();

                    elemento = $('<span id="' + id + '" class="billete">' + billete.fraccion + '</span>');

                    $('#zona' + zona).append(elemento);

                    inicial = posiciones[billete.posicion].fraccion.inicial;
                    inicial.height = '0px';
                    inicial['font-size'] = '0px';

                    $('#' + id).css(inicial);

                    final = posiciones[billete.posicion].fraccion.final;
                    final.width = getAnchoByAlto(final.height);
                    final.top = (parseInt(inicial.top) - (parseInt(final.height) / 2)) + 'px';
                    final.left = (parseInt(inicial.left) - (parseInt(final.width) / 2)) + 'px';
                    final['font-size'] = getTamanioTextoByAlto(final.height);

                    $('#' + id).animate(final, 1000);
                } else {
                    var classB = 'class="billete"';
                    if(billete.posicion >=6 && billete.posicion <=20)
                        var classB = 'class="billete2"';
                    var id = 'billete' + zona + '_' + billete.posicion;
                    $('#' + id).remove();

                    var elemento = $('<span id="' + id + '" '+classB+'>' + billete.numero + '</span>');

                    $('#zona' + zona).append(elemento);

                    var inicial = posiciones[billete.posicion].inicial;
                    inicial.height = '0px';
                    inicial['font-size'] = '0px';

                    $('#' + id).css(inicial);

                    var final = posiciones[billete.posicion].final;
                    final.width = getAnchoByAlto(final.height);
                    final.top = (parseInt(inicial.top) - (parseInt(final.height) / 2)) + 'px';
                    final.left = (parseInt(inicial.left) - (parseInt(final.width) / 2)) + 'px';
                    final['font-size'] = getTamanioTextoByAlto(final.height);

                    $('#' + id).animate(final, 1000);
                }
            }

            function getAnchoByElement(elemento) {
                return (parseInt($(elemento).css('height')) * 3) + 'px';
            }

            function getAnchoByAlto(alto) {
                return (parseInt(alto) * 3) + 'px';
            }

            function getTamanioTextoByElement(elemento) {
                var h = parseInt($(elemento).css('height'));
                var tamanioTexto = h * 0.95;
                tamanioTexto = tamanioTexto.length > 5 ? tamanioTexto.substring(0, 5) : tamanioTexto;
                return tamanioTexto + 'px';
            }

            function getTamanioTextoByAlto(alto) {
                var h = parseInt(alto);
                var tamanioTexto = h * 0.95;
                tamanioTexto = tamanioTexto.length > 5 ? tamanioTexto.substring(0, 5) : tamanioTexto;
                return tamanioTexto + 'px';
            }

            function pad(n, width, z) {
                z = z || '0';
                n = n + '';
                return n.length >= width ? n : new Array(width - n.length + 1).join(z) + n;
            }

            function startTimer(duration, display) {
                var timer = duration, minutes, seconds;
                tiempoPrimerPremio = setInterval(function () {
                    minutes = parseInt(timer / 60, 10);
                    seconds = parseInt(timer % 60, 10);
                    minutes = minutes < 10 ? "0" + minutes : minutes;
                    seconds = seconds < 10 ? "0" + seconds : seconds;
                    display.textContent = 'Se cerrará en ' + minutes + ":" + seconds;

                    if (--timer < 0) {
                        timer = duration;
                        clearInterval(tiempoPrimerPremio);
                    }
                }, 1000);
            }

            function mostrarPrimerPremio(){
                primer_premio = true;
                intervalPrimeroPremio = setInterval(function(data){
                    if(primer_premio == true){
                        $('#primer_premio').fadeOut();
                        $('#resultado_primer_premio').css('background-size', '375px 82px');
                        $('#resultado_primer_premio').css('top', '131px');
                        $('#resultado_primer_premio').css('height', '71px');
                        $('#resultado_primer_premio').css('left', '142px');
                        $('#resultado_primer_premio').css('width', '384px');
                        $('#resultado_primer_premio').css('background-image', 'url(escribano_img/vendido_<?php echo $_SESSION['sorteo']; ?>.png)');
                        $('#resultado_primer_premio').fadeIn();
                        primer_premio = false;
                    }else{
                        $('#primer_premio').fadeIn();
                        $('#resultado_primer_premio').fadeOut();
                        primer_premio = true;
                    }
                },2000);


            }


            function existePosicion(extracciones,posicion){
                for (var i = 0; i < extracciones.length; i++) {
                    if(extracciones[i].posicion == posicion)
                        return true;
                }
                return false;
            }
        </script>
    </body>
</html>