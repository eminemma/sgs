<?php 
session_start();
?>
<div class="row-fluid show-grid">		
	<div id="contendio_juego" class="well form-inline text-center">
		<h4>Juego <?php echo $_SESSION['juego_tipo']; ?></h4>
    <div class="subtitulo_juego label label-info" style="display:none">Primer Premio</div>
		<form class="form-inline" action="#">
		  <span class="texto_sorteo">Posicion</span> 
        <input type="text" id="posicion" name="posicion" class="input-small bola" size="1" tabindex="0"> 
      <span id="entero_div" style="display:none">
        <span class="texto_sorteo">Numero</span> 
          <input type="text" id="entero" name="entero" class="input-medium bola" size="1" tabindex="1">
      </span>
	  <span id="fraccion_div" style="display:none">
        <span class="texto_sorteo">Fraccion</span> 
          <input type="text" id="fraccion" name="fraccion" class="input-small bola" size="1" tabindex="2">
      </span>    
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
loteria_tradicional={};
$(function() {
    cargarConfiguracion({accion: "configuracion",juego:"primer_juego"}); 

});
</script>
