<h3 class="titulo">Modificar Sorteo</h3>
<?php 
session_start();
include_once dirname(__FILE__).'/../../db.php';


$sql = "	SELECT ID_USUARIO ,DESCRIPCION FROM SUPERUSUARIO.usuarios
			WHERE area_id=135
			ORDER BY DESCRIPCION";
$res_usuario=sql($sql);

$sql = "	SELECT ID_ESCRIBANO, DESCRIPCION FROM sgs.T_ESCRIBANO 
			ORDER BY descripcion";
$res_escribano=sql($sql);

require_once 'encabezado.php';
?>
<div class="resultado">
	<div class="error alert alert-error" onclick="$(this).fadeOut()" style="display:none">
		
		<div class="contenido_error"></div>
	</div>

	<div class="ok alert alert-success" onclick="$(this).fadeOut()" style="display:none">
		<i class="icon-ok"></i>
		<div class="contenido"></div>
	</div>
</div>
<form method="post" action="#" onsubmit="$.post('administracion/administrar_incentivos/ajax.php',
													{ 
  													  accion:'nuevo',	
													  sorteo :$('#sorteo').val()													
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
			<label class="control-label" for="jefe_sorteo">Nro Incentivo</label>	
			<div class="controls">
				<input type="text" id="sorteo" placeholder="Nro Incentivo">
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