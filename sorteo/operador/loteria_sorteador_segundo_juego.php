<div class="row-fluid show-grid">		
	<div id="contendio_juego" class="well form-inline text-center">
		<h4>Sale o Sale</h4>
		<form class="form-inline">
		 <span class="texto_sorteo">Posicion</span><input type="text" class="input-small bola" id="posicion" name="posicion" maxlength="2" size="1">  <span class="texto_sorteo">Numero</span>  <input type="text" id="entero" name="entero" class="input-medium bola" maxlength="5" size="1"> 
		</form>
	</div>
</div>
<div id="error_juego" class="alert alert-error" style="display:none">
    <button type="button" class="close" onclick="$('#error_juego').slideUp('slow');">x</button>
    <span><i class="icon-remove"></i></span>
    <span class="contenido_error"></span>
</div>
<div id="success_juego" class="alert alert-success" style="display:none">
    <button type="button" class="close" onclick="$('#success_juego.alert').slideUp('slow');">x</button>
    <span><i class="icon-ok"></i></span>
    <span class="contenido_error"></span>
</div>    
<div id="warning_juego" class="alert alert-info" style="display:none">
      <button type="button" class="close" onclick="$('#warning_juego.alert').slideUp('slow');">×</button> 
      <span><i class="icon-info-sign"></i></span>
      <span class="contenido_error"></span>     
</div>
<div id="warning_juego2" class="alert alert-info" style="display:none">
      <button type="button" class="close" onclick="$('#warning_juego2.alert').slideUp('slow');">×</button> 
      <span><i class="icon-info-sign"></i></span>
      <span class="contenido_error"></span>     
</div>
<script type="text/javascript">
$(function() {
  cargarConfiguracion({accion: "configuracion",juego:"segundo_juego"}); 

});
</script>
