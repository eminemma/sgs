<?php
session_start();
include_once dirname(__FILE__) . '/../../db.php';
include_once dirname(__FILE__) . '/ajax.php';
$id_sorteo = isset($_GET['id_sorteo']) ? $_GET['id_sorteo'] : '';

$res_sorteo           = sql(sql_sorteo(), array($id_sorteo));
$row_sorteo           = siguiente($res_sorteo);
$deshabilitarPrograma = false;
if ($row_sorteo->IMPORTADO == 'SI') {
    $deshabilitarPrograma = true;
}
?>

<h3 class="titulo">Modificar Sorteo</h3>
<?php

$res_usuario   = sql(sql_operador());
$res_escribano = sql(sql_escribano());

$rs_programa = sql(sql_programa(), array($_SESSION['id_juego']));

$rs_quinielas_asociadas = sql(sql_quiniela_asociadas(), array($row_sorteo->QUINIELA_ASOC));

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
			<span  class="contenido"></span>
		</div>
	</div>
<form method="post" action="#" onsubmit="$.post('administracion/administrar_sorteos/ajax.php',
													{
  													  accion:'modificar',
													  id_sorteo :'<?php echo $id_sorteo; ?>',
													  jefe:$('#jefe_sorteo').val(),
													  operador:$('#operador').val(),
													  escribano:$('#escribano').val(),
													  fecha_sorteo:$('#fecha_sorteo').val(),
													    id_programa :$('#programa option:selected').val(),
													   programa :$('#programa option:selected').val(),
													   quiniela_asoc :$('#quiniela_asoc option:selected').val()
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
			<label class="control-label" for="programa">Programa Premio</label>
			<div class="controls">
				<select class="filter-option pull-left"  id="programa" name="programa" <?php echo ($deshabilitarPrograma == true) ? 'disabled="disbaled"' : '' ?>>
					<option value="-1" <?php echo ($row->ID_PROGRAMA == null) ? 'selected' : ''; ?>>Seleccionar</option>
					<?php
while ($row = siguiente($rs_programa)) {?>
	    				<option value="<?php echo $row->ID_PROGRAMA ?>" <?php echo ($row->ID_PROGRAMA == $row_sorteo->ID_PROGRAMA) ? 'selected' : '' ?> ><?php echo $row->DESCRIPCION ?></option>
	    			<?php }?>
	  			</select>
	  			<a href="javascript:void(0)" onclick="abrirPrograma();" title="Programa de Premios"><div class="fa fa-print fa-2x"></div></a>
  			</div>
		</div>
		<div class="control-group">
			<label class="control-label" for="jefe_sorteo">Jefe de Sorteo</label>
			<div class="controls">
				<select class="filter-option pull-left" id="jefe_sorteo" name="jefe_sorteo">
					<option value="-1" >Sin Jefe</option>
					<?php while ($row = siguiente($res_usuario)) {?>
	    				<option value="<?php echo $row->ID_USUARIO ?>" <?php echo ($row->ID_USUARIO == $row_sorteo->ID_JEFE) ? 'selected' : ''; ?>><?php echo $row->DESCRIPCION ?></option>
	    			<?php }?>
	  			</select>
  			</div>
		</div>
		<div class="control-group">
			<label class="control-label" for="operador">Operador de Sorteo</label>
			<div class="controls">
				<select class="filter-option pull-left" id="operador" name="operador">
					<option value="-1" <?php echo ($row->ID_USUARIO == null) ? 'selected' : ''; ?>>Sin Operador</option>
					<?php
$res_usuario->MoveFirst();

while ($row = siguiente($res_usuario)) {?>
						<option value="<?php echo $row->ID_USUARIO ?>" <?php echo ($row->ID_USUARIO == $row_sorteo->ID_OPERADOR) ? 'selected' : ''; ?>><?php echo $row->DESCRIPCION ?></option>
	    			<?php }?>
	  			</select>
  			</div>
		</div>
		<div class="control-group">
			<label class="control-label" for="escribano">Escribano de Sorteo</label>
			<div class="controls">
				<select class="filter-option pull-left" id="escribano" name="escribano">
					<option value="-1" <?php echo ($row->ID_ESCRIBANO == null) ? 'selected' : ''; ?>>Sin Escribano</option>
					<?php
while ($row = siguiente($res_escribano)) {?>
	    				<option value="<?php echo $row->ID_ESCRIBANO ?>" <?php echo ($row->ID_ESCRIBANO == $row_sorteo->ID_ESCRIBANO) ? 'selected' : '' ?> ><?php echo $row->DESCRIPCION ?></option>
	    			<?php }?>
	  			</select>
  			</div>
		</div>
		<div class="control-group">
			<label class="control-label" for="fecha_sorteo_cal">Fecha de Sorteo</label>
			<div class="controls">
				<div id="fecha_sorteo_cal" class="input-append">
					<input id="fecha_sorteo" style="width:200px" data-format="dd/MM/yyyy hh:mm:ss" type="text" class="input-small recordar" placeholder="obligatorio">
					<span class="add-on">
						<i data-time-icon="icon-time" data-date-icon="icon-calendar" class="icon-calendar"></i>
					</span>

				</div>

			</div>
		</div>

		<div class="control-group">
			<label class="control-label" for="escribano">Quiniela Asociada</label>
			<div class="controls">
				<select class="filter-option pull-left" id="quiniela_asoc" name="quiniela_asoc">
					<option value="0" >Seleccionar</option>
					<?php

while ($row = siguiente($rs_quinielas_asociadas)) {

    ?>
	    				<option value="<?php echo $row->SORTEO ?>" <?php echo ($row->SORTEO == $row_sorteo->QUINIELA_ASOC) ? 'selected' : '' ?> ><?php echo $row->SORTEO ?></option>
	    			<?php }?>
	  			</select>
  			</div>
		</div>

	<div class="control-group">
    <div class="controls">
      <input type="hidden" id="id_sorteo" name="id_sorteo" value="<?php echo $id_sorteo ?>">
		<button type="submit" class="btn" >Guardar</button>
		<button type="button" class="btn">Cancelar</button>
    </div>
  </div>
</form>
<script type="text/javascript">
function abrirPrograma(){
	window.open("administracion/administrar_programa/pdf_programa_poceada.php?id_programa="+$( "#programa option:selected" ).val(), "_blank");
	return false;
}
function inicarCombos(){
  $.get('juego/ajax.php',{'accion':'listar_tipos_juegos','id_juego':32},
        function(data){
        $.each(data,
            function(i, item) {
              $('#tipo_juego').append('<option value="'+item.ID_JUEGO_TIPO+'">'+item.DESCRIPCION+'</option>');
          	}
        );
        $('#tipo_juego option[value="<?php echo $row_sorteo->ID_TIPO_JUEGO ?>"]').attr("selected",true);
      }
    );

}

$(function(){
	$('#fecha_sorteo_cal').datetimepicker({
		pickDate: true,
		format: 'dd/MM/yyyy hh:mm:ss'
	});


	$('.add-on').on('click', function(){
		var offset = $("#fecha_sorteo_cal").offset();
		$('.bootstrap-datetimepicker-widget').css({
			top: offset.top -150,
			left: offset.left + $("#fecha_sorteo_cal").width()
		})});
	$("#fecha_sorteo_cal").datetimepicker("setDate","<?php echo $row_sorteo->FECHA_SORTEO ?>" );
	inicarCombos();
});
</script>