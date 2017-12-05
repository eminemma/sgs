<h3 class="titulo">Modificar Incentivo</h3>
<?php 
session_start();
include_once dirname(__FILE__).'/../../db.php';
$id_sorteo=isset($_GET['id_sorteo']) ? $_GET['id_sorteo'] : '';

$sql = "	SELECT 	ts.DESCRIPCION,
					to_char(TS.FECHA_SORTEO,'dd/mm/yyyy') as fecha_sorteo,
					to_char(TS.fecha_hasta_pago_premios,'dd/mm/yyyy') as fecha_hasta_pago_premios,
					ts.id_jefe,
					ts.id_operador,
					ts.id_escribano,
					TS.SORTEO,
					ts.ID_INCENTIVO_SORTEO
			FROM T_INCENTIVO_SORTEO TS
			WHERE TS.ID_INCENTIVO_SORTEO = ?";

$res_sorteo=sql($sql, array($id_sorteo));
$row_sorteo=siguiente($res_sorteo);


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
  													  accion:'modificar',	
													  id_sorteo :'<?php echo $id_sorteo; ?>',
													  jefe:$('#jefe_sorteo').val(),
													  operador:$('#operador').val(),
													  escribano:$('#escribano').val(),
													  fecha_sorteo:$('#fecha_sorteo').val(),
													  fecha_prescripcion:$('#fecha_prescripcion').val()
													  },
														function(data){
															if(data.tipo){
																$('.error').fadeOut()
															    $('.error > .contenido_error').html('');
															    $('.ok').fadeOut()
															    $('.ok > .contenido').html('');
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
			Sorteo <?php echo $row_sorteo->SORTEO ?>			
		</h4>
		<div class="control-group">
			<label class="control-label" for="jefe_sorteo">Jefe de Sorteo</label>
			<div class="controls">
				<select class="filter-option pull-left" id="jefe_sorteo" name="jefe_sorteo">
					<option value="-1" >Sin Jefe</option>
					<?php while($row = siguiente($res_usuario)) {?>
	    				<option value="<?php echo $row->ID_USUARIO ?>" <?php echo ($row->ID_USUARIO==$row_sorteo->ID_JEFE) ? 'selected' : ''; ?>><?php echo $row->DESCRIPCION ?></option>
	    			<?php } ?>	
	  			</select>
  			</div>
		</div>
		<div class="control-group">
			<label class="control-label" for="operador">Operador de Sorteo</label>
			<div class="controls">		
				<select class="filter-option pull-left" id="operador" name="operador">
					<option value="-1" <?php echo ($row->ID_USUARIO==null) ? 'selected' : ''; ?>>Sin Operador</option>
					<?php 
					$res_usuario->MoveFirst(); 
					
					while($row = siguiente($res_usuario)) {?>
						<option value="<?php echo $row->ID_USUARIO ?>" <?php echo ($row->ID_USUARIO==$row_sorteo->ID_OPERADOR) ? 'selected' : ''; ?>><?php echo $row->DESCRIPCION ?></option>
	    			<?php } ?>	
	  			</select>
  			</div>
		</div>
		<div class="control-group">
			<label class="control-label" for="escribano">Escribano de Sorteo</label>		
			<div class="controls">	
				<select class="filter-option pull-left" id="escribano" name="escribano">
					<option value="-1" <?php echo ($row->ID_ESCRIBANO==null) ? 'selected' : ''; ?>>Sin Escribano</option>
					<?php 
					while($row = siguiente($res_escribano)) {?>
	    				<option value="<?php echo $row->ID_ESCRIBANO ?>" <?php echo ($row->ID_ESCRIBANO==$row_sorteo->ID_ESCRIBANO) ? 'selected' : '' ?> ><?php echo $row->DESCRIPCION ?></option>
	    			<?php } ?>	
	  			</select>
  			</div>
		</div>
		<div class="control-group">
			<label class="control-label" for="fecha_sorteo_cal">Fecha de Sorteo</label>
			<div class="controls">	
				<div id="fecha_sorteo_cal" class="input-append">
					<input id="fecha_sorteo" data-format="dd/MM/yyyy" value="<?php echo $row_sorteo->FECHA_SORTEO ?>" type="text" class="input-small recordar" placeholder="obligatorio">
					<span class="add-on">
						<i data-time-icon="icon-time" data-date-icon="icon-calendar" class="icon-calendar"></i>
					</span>

				</div>

			</div>
		</div>
		<div class="control-group">
			<label class="control-label" for="fecha_prescripcion_cal">Fecha Prescripci&oacute;n</label>
			<div class="controls">	
				<div id="fecha_prescripcion_cal" class="input-append">
					<input id="fecha_prescripcion" data-format="dd/MM/yyyy" value="<?php echo $row_sorteo->FECHA_HASTA_PAGO_PREMIOS ?>" type="text" class="input-small recordar" placeholder="obligatorio">
					<span class="add-on">
						<i data-time-icon="icon-time" data-date-icon="icon-calendar" class="icon-calendar"></i>
					</span>

				</div>

			</div>
		</div>
	
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

<script type="text/javascript">
$(function(){
	$('#fecha_prescripcion_cal').datetimepicker({
		pickTime: false,
	});
});
</script>
		
</form>		