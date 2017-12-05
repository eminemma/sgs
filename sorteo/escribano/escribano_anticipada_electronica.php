<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Sorteador Loteria de Cordoba S.E.</title>
    <link href="escribano_anticipada_electronica_estilo.css" rel="stylesheet" type="text/css" />
    <script type="text/javascript" src="../../librerias/jquery/jquery-1.10.1.js"></script>
    <script type="text/javascript" src="../../js/funciones.js"></script>
</head>

<body>
    <div id="incentivo">
         <div id="fecha_sorteo"></div>
        <div id="orden"></div>
        <div id="premio"></div>
        <div id="tipo_incentivo"></div>
        <div id="aleatorio_entero"></div>
        <div id="aleatorio_fraccion"></div>
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
    <div id="resumen">
        <div id="sorteo_r"></div>
        <div id="fecha_sorteo_r"></div>
        <div id="escribano_r"></div>
        <div id="jefe_sorteo_r"></div>
        <div id="prox_sorteo_r"></div>
        <div id="premio_prox_sorteo_r"></div>
        <div id="prescripcion_r"></div>
        <div id="ganadores"></div>
        <div id="premios" class="premios"></div>
    </div>
    <script type="text/javascript">
    $(document).ready(
        function() {
            buscar_informacion();
         setInterval(buscar_informacion, 1300);
        }
    );
    var datos_incentivo = [];
    var incentivoMostrando = '1';
    var aleatorio = null;

    function buscar_informacion() {
        $.getJSON(
            'escribano_anticipada_electronica_ajax.php?ale=' + parseInt(Math.random() * 1000000000),
            function(data) {
                var descIncentivo = data.descIncentivo;
                var incentivoMostrando = data.incentivoMostrando;
                aleatorio = data.cantFracciones;
                switch (incentivoMostrando) {
                    case ('1'):
                        $('#incentivo').css("background-image", "url(escribano_img/gordo_navidad_2017_semana1_blanco.jpg)");
                        buscar_ganador(data);
                        break;
                    case ('2'):
                        $('#incentivo').css("background-image", "url(escribano_img/gordo_navidad_2017_semana2_blanco.jpg)");
                        buscar_ganador(data);
                        break;
                    case ('3'):
                        $('#incentivo').css("background-image", "url(escribano_img/gordo_navidad_2017_semana3_blanco.jpg)");
                        buscar_ganador(data);
                        break;
                    case ('4'):
                        $('#incentivo').css("background-image", "url(escribano_img/gordo_navidad_2017_semana4_blanco.jpg)");
                        buscar_ganador(data);
                        break;
                    case ('5'):
                        $('#incentivo').css("background-image", "url(escribano_img/gordo_navidad_2017_semana5_blanco.jpg)");
                        buscar_ganador(data);
                        break;
                    case ('6'):
                        $('#incentivo').css("background-image", "url(escribano_img/gordo_navidad_2017_semana6_blanco.jpg)");
                        buscar_ganador(data);
                        break;
                    case ('7'):
                        $('#incentivo').css("background-image", "url(escribano_img/gordo_navidad_2017_semana7_blanco.jpg)");
                        buscar_ganador(data);
                        break;
                    case ('8'):
                        $('#incentivo').css("background-image", "url(escribano_img/gordo_navidad_2017_semana8_blanco.jpg)");
                        buscar_ganador(data);
                        break;
                    case ('9'):
                        $('#incentivo').css("background-image", "url(escribano_img/gordo_navidad_2017_semana9_blanco.jpg)");
                        buscar_ganador(data);
                        break;
                    case ('resumen'):
                        $('#resumen').css("background-image", "url(escribano_img/gordo_navidad_2017_semana"+data.semana+"_resumen.jpg)");
                        mostrar_resumen(data);
                        break;
                }

            }
        );
    }

    function buscar_ganador(data) {

        $('#resumen').hide();
        $('#incentivo').show();
        $('#resumen div').html('');


        $('#orden').html(pad(data.orden,2));
        $('#fecha_sorteo').html(data.fecha_sorteo);
        $('#premio').html(data.descIncentivo);
        $('#escribano_d').html(data.escribano);
        $('#jefe_sorteo_d').html(data.jefe_sorteo);

        if (typeof data.datosIncentivo[0] != 'undefined') {
            if (typeof intervalo_para_aleatorio != 'undefined') {
                clearInterval(intervalo_para_aleatorio);
            }

            $('#aleatorio_entero').html('');
            $('#aleatorio_fraccion').html('');

            /*$('#agencia').html(data.datosIncentivo[0].id_agencia);*/
            if(data.datosIncentivo[0].nombre == 'VENTA CONTADO CASA CENTRAL')
                $('#localidad_d').html('CORDOBA');
            else if(data.datosIncentivo[0].nombre == 'VENTA CONTADO'){
                if(data.datosIncentivo[0].desc_sucursal == 'CASA CENTRAL')
                    $('#localidad_d').html('CORDOBA');
                else
                    $('#localidad_d').html(data.datosIncentivo[0].desc_sucursal);
            }else  if(data.datosIncentivo[0].id_agencia != null){
                $('#localidad_d').html(data.datosIncentivo[0].localidad);
            }

            $('#nombre_d').html(data.datosIncentivo[0].nombre);

            $('#billete_d').html(data.datosIncentivo[0].billete);
            $('#fraccion_d').html(data.datosIncentivo[0].fraccion);

            /*$('#sucursal').html(data.datosIncentivo[0].desc_sucursal);
            $('#agencia').html((data.datosIncentivo[0].desc_agencia));*/


            if(data.datosIncentivo[0].nombre == 'VENTA CONTADO CASA CENTRAL'){
               $('#agencia').html('');
               $('#nombre_d').html('9001 CORDOBA');
            }
            else if(data.datosIncentivo[0].nombre == 'VENTA CONTADO'){
               $('#agencia').html('');
               $('#nombre_d').html('9001 '+data.datosIncentivo[0].desc_sucursal);
            }else  if(data.datosIncentivo[0].id_agencia != null){

               $('#agencia').html(pad(data.datosIncentivo[0].id_agencia,4));
               $('#nombre_d').html(data.datosIncentivo[0].nombre);
            }

            intervalo_para_aleatorio = undefined;
        } else {
            if (typeof intervalo_para_aleatorio != 'undefined') {

                $('#sucursal').html('');
                $('#agencia').html('');
                $('#localidad_d').html('');
                $('#nombre_d').html('');
                $('#billete_d').html('');
                $('#fraccion_d').html('');
            }
            if (typeof intervalo_para_aleatorio == 'undefined') {
                $('#sucursal').html('');
                $('#agencia').html('');
                $('#localidad_d').html('');
                $('#nombre_d').html('');
                $('#billete_d').html('');
                $('#fraccion_d').html('');
                intervalo_para_aleatorio = setInterval(animar_aleatorio, 150);
            }
        }
    }

    function mostrar_resumen(data) {
    	if (typeof intervalo_para_aleatorio != 'undefined') {
            intervalo_para_aleatorio = clearInterval(intervalo_para_aleatorio);
        }
        $('#incentivo').hide();
        $('#resumen').show();
        $('#incentivo div').html('');
        $('#jefe_sorteo_r').html(data.jefe_sorteo);
        $('#escribano_r').html(data.escribano);
/*        $('#prescripcion_r').html(data.prescripcion);*/
/*        $('#premio_prox_sorteo_r').html(data.prox_sorteo);*/

        $('#sorteo_r').html(data.sorteo);
        $('#fecha_sorteo_r').html(data.fecha_sorteo);
        $('#ganadores').html('');
        $('#premios').html('');
        for (var i = 0; i < data.ganadores.length; i++) {

            if(data.ganadores[i].NOMBRE == 'VENTA CONTADO CASA CENTRAL')
               $('#ganadores').append('<div class="ganador"><div class="billete">'+pad(data.ganadores[i].BILLETE,5)+'</div><div class="fraccion">'+pad(data.ganadores[i].FRACCION,2)+'</div><div class="premio_r">'+data.ganadores[i].PREMIO+'</div><div class="agencia">9001</div><div class="sucursal">CORDOBA</div></div>');
            else if(data.ganadores[i].NOMBRE == 'VENTA CONTADO'){
               var localidad;
               if(data.ganadores[i].SUCURSAL == 'CASA CENTRAL'){
                   localidad = 'CORDOBA';
               } else{
                   localidad = data.ganadores[i].SUCURSAL;
               }
               $('#ganadores').append('<div class="ganador"><div class="billete">'+pad(data.ganadores[i].BILLETE,5)+'</div><div class="fraccion">'+pad(data.ganadores[i].FRACCION,2)+'</div><div class="premio_r">'+data.ganadores[i].PREMIO+'</div><div class="agencia">9001</div><div class="sucursal">'+localidad+'</div></div>');
            }
           else if(data.ganadores[i].AGENCIA != null)
                $('#ganadores').append('<div class="ganador"><div class="billete">'+pad(data.ganadores[i].BILLETE,5)+'</div><div class="fraccion">'+pad(data.ganadores[i].FRACCION,2)+'</div><div class="premio_r">'+data.ganadores[i].PREMIO+'</div><div class="agencia">'+pad(data.ganadores[i].AGENCIA,4)+'</div><div class="sucursal">'+data.ganadores[i].LOCALIDAD+'</div></div>');
        }

/*        for (var i = 0; i < data.premios.length; i++) {
            $('#premios').append('<div class="premio">'+data.premios[i].PREMIO+'</div></br>');
        }*/
    }

    function animar_aleatorio() {
        var maximo = parseInt(99999);
        $('#aleatorio_entero').html(pad(Math.floor((Math.random() * maximo) + 1), 5));
        var maximo_fraccion = parseInt(aleatorio);
        $('#aleatorio_fraccion').html(pad(Math.floor((Math.random() * maximo_fraccion) + 1), 2));
    }

    function pad(n, width, z) {
        z = z || '0';
        n = n + '';
        return n.length >= width ? n : new Array(width - n.length + 1).join(z) + n;
    }
    </script>
</body>

</html>
