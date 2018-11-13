<?php 
//$('#numero_1').attr('disabled','disabled');
//$('#numero_2').attr('disabled','disabled');
//$('#numero_3').attr('disabled','disabled');
//$('#numero_4').attr('disabled','disabled');
//$('#numero_5').attr('disabled','disabled');
//$('#numero_6').attr('disabled','disabled');
?>
<div class="row-fluid show-grid">		
	<div id="contendio_juego" class="well form-inline text-center">
    <form class="form-inline" id="sorteo_entero" name="sorteo_entero" action="#" style="position:relative" 
          onsubmit="$.post('sorteo/operador/loteria_sorteador_ajax.php',{'juego':3,'accion':'control_ingreso',posicion:21}, function(data){$('#numero_1').focus(); $('#juego4').html(data); }); return false;">
		<h4>NISSAN NOTE</h4>
		<input id="numero_1" name="numero" type="submit" size="8" value="Detener" style="text-align:center;width: 250px;height: 50px;font-size:36px">
	</form>
	
	<form class="form-inline" id="sorteo_entero" name="sorteo_entero" action="#" style="position:relative" 
          onsubmit="$.post('sorteo/operador/loteria_sorteador_ajax.php',{'juego':3,'accion':'control_ingreso',posicion:22}, function(data){$('#numero_2').focus(); $('#juego4').html(data); });  return false;">
		<h4>MOTO HONDA XR 150 </h4>
		<input id="numero_2" name="numero" type="submit" size="8" value="Detener" style="text-align:center;width: 250px;height: 50px;font-size:36px">
	</form>
	
	<form class="form-inline" id="sorteo_entero" name="sorteo_entero" action="#" style="position:relative" 
          onsubmit="$.post('sorteo/operador/loteria_sorteador_ajax.php',{'juego':3,'accion':'control_ingreso',posicion:23}, function(data){$('#numero_3').focus(); $('#juego4').html(data); });  return false;">
		<h4>MOTO HONDA XR 150 </h4>
		<input id="numero_3" name="numero" type="submit" size="8" value="Detener" style="text-align:center;width: 250px;height: 50px;font-size:36px">
	</form>
	
	<form class="form-inline" id="sorteo_entero" name="sorteo_entero" action="#" style="position:relative" 
          onsubmit="$.post('sorteo/operador/loteria_sorteador_ajax.php',{'juego':3,'accion':'control_ingreso',posicion:24}, function(data){$('#numero_4').focus(); $('#juego4').html(data); });  return false;">
		<h4>VIAJE A CUBA</h4>
		<input id="numero_4" name="numero" type="submit" size="8" value="Detener" style="text-align:center;width: 250px;height: 50px;font-size:36px">
	</form>
	
	<!--
	<form class="form-inline" id="sorteo_entero" name="sorteo_entero" action="#" style="position:relative" 
          onsubmit="$.post('sorteo/operador/loteria_sorteador_ajax.php',{'juego':3,'accion':'control_ingreso',posicion:25}, function(data){$('#numero_5').focus(); $('#juego4').html(data); }); return false;">
		<h4>MOTO HONDA XR 150 </h4>
		<input id="numero_5" name="numero" type="submit" size="8" value="Detener" style="text-align:center;width: 250px;height: 50px;font-size:36px">
	</form>
	
	<form class="form-inline" id="sorteo_entero" name="sorteo_entero" action="#" style="position:relative" 
          onsubmit="$.post('sorteo/operador/loteria_sorteador_ajax.php',{'juego':3,'accion':'control_ingreso',posicion:26}, function(data){$('#numero_6').focus(); $('#juego4').html(data); }); return false;">
		<h4>MOTO HONDA XR 150 </h4>
		<input id="numero_6" name="numero" type="submit" size="8" value="Detener" style="text-align:center;width: 250px;height: 50px;font-size:36px">
	</form>
	-->
	
	</div>
</div>
<div id="juego4"></div>
<script type="text/javascript">
$(function() {
  //cargarConfiguracion({accion: "configuracion",juego:"segundo_juego"}); 

});
</script>
