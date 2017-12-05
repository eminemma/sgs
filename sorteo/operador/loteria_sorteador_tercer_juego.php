<div class="row-fluid show-grid">
	<div id="contendio_juego" class="well form-inline text-center">
    <form class="form-inline" id="sorteo_entero" name="sorteo_entero" action="#" style="position:relative"
          onsubmit="$.post('sorteo/operador/loteria_sorteador_ajax.php',{'juego':4,'accion':'control_ingreso',posicion:21, billete:$('#numero').val()}, function(data){$('#numero').focus(); $('#numero').attr('disabled',true); $('#juego4').html(data); }); return false;">
		<h4>Sorteo por Entero</h4>
		 <input id="numero" name="numero" type="submit" size="8" value="Detener" style="text-align:center;width: 250px;height: 50px;font-size:36px">
         <!-- <input id="numero" name="numero" type="text" size="5" maxlength="5" value="" style="text-align:center;width: 250px;height: 50px;font-size:36px"> -->
		</form>
	</div>
</div>
<div id="juego4"></div>
<script type="text/javascript">
$(function() {
//cargarConfiguracion({accion: "configuracion",juego:"segundo_juego"});

});
</script>
