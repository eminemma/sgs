<script type="text/javascript">
function inicarCombos(){
  $.get('juego/ajax.php',{'accion':'listar_tipos_juegos','id_juego':32},
        function(data){
          $.each(data,
            function(i, item) {
              $('#programa').append('<option value="'+item.ID_PROGRAMA+'">'+item.DESCRIPCION+'</option>');
          }
        );
      }
    );
}

$(document).ready(function() {
    inicarCombos();
});
</script>
<h3 class="titulo">Nuevo Sorteo</h3>
<?php
session_start();
include_once dirname(__FILE__) . '/../../db.php';
include_once dirname(__FILE__) . '/ajax.php';

$res_sorteo = sql(sql_sorteo(), array($id_sorteo));
$row_sorteo = siguiente($res_sorteo);

$res_usuario   = sql(sql_operador());
$res_escribano = sql(sql_escribano());

?>

<div class="navbar">
  <div class="navbar-inner">
    <a class="brand" href="#" onclick="g('administracion/administrar_sorteos/quiniela_poceada_administrar_sorteos.php')">Sorteo</a>
    <a class="brand" href="#" onclick="g('administracion/administrar_sorteos/quiniela_poceada_nuevo_sorteo.php')">Nuevo</a>

  </div>
</div>
<div class="resultado">
	<div class="error alert alert-error" onclick="$(this).fadeOut()" style="display:none">

		<div class="contenido_error"></div>
	</div>

	<div class="ok alert alert-success" onclick="$(this).fadeOut()" style="display:none">
		<i class="icon-ok"></i>
		<span  class="contenido"></div>
	</div>
</div>
<form method="post" action="#" onsubmit="$.post('administracion/administrar_sorteos/ajax.php',
													{
  													  accion:'nuevo',
													  sorteo :$('#sorteo').val(),
													  id_programa :$('#programa option:selected').val()
													  },
														function(data){
															$('.error').fadeOut()
														    $('.error > .contenido_error').html('');
														    $('.ok').fadeOut()
														    $('.ok > .contenido').html('');
															if(data.tipo){
																if(data.tipo=='error'){
																	$('.error').fadeIn('slow', function() {
																    	 $('.error > .contenido_error').html(data.mensaje);
																    });
																}

																if(data.tipo=='success'){
																	$('.ok').fadeIn('slow', function() {
																	    	 $('.ok > .contenido').html(data.mensaje);
																	    });
																}
															}else{
																$('.error').fadeIn('slow', function() {
																    	 $('.error > .contenido_error').html(data);
																});
															}
														}
												); return false;" class="form-horizontal">

		<h4>
			<label class="control-label" for="jefe_sorteo">Sorteo</label>
			<div class="controls">
				<input type="text" id="sorteo" placeholder="Sorteo">
			</div>
		</h4>
		<h4>
			<label class="control-label" for="jefe_sorteo">Programa</label>
			<div class="controls">
				<select id="programa">
					<option value="-1">Sin Programa</option>
				</select>
			</div>
		</h4>

	<div class="control-group">
    <div class="controls">
      <input type="hidden" id="id_sorteo" name="id_sorteo" value="<?php echo $id_sorteo ?>">
		<button type="submit" class="btn" >Guardar</button>
		<button type="button" class="btn">Cancelar</button>
    </div>
  </div>

<script type="text/javascript">
$(function(){
	$('#fecha_sorteo_cal').datetimepicker({
		pickTime: false,
	});
});
</script>

</form>