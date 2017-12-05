<h3 class="titulo">Administracion de Sorteo</h3>
<?php
session_start();
include_once dirname(__FILE__) . '/../../db.php';
include_once dirname(__FILE__) . '/ajax.php';
conectar_db();
$sorteo     = $_SESSION['sorteo'];
$id_juego   = $_SESSION['id_juego'];
$res_sorteo = sql(sql_administracion_sorteo(), array($sorteo, $id_juego));

$res_anticipados = sql(sql_anticipados_sorteo(), array($sorteo, $id_juego));

$res_usuario   = sql(sql_operador());
$res_escribano = sql(sql_escribano());

require_once 'loteria_encabezado.php';
?>
<div class="resultado">
		<div class="error alert alert-error" onclick="$(this).fadeOut()" style="display:none">

			<div class="contenido_error"></div>
		</div>

		<div class="ok alert alert-success" onclick="$(this).fadeOut()" style="display:none">
			<i class="icon-ok"></i>
			<span  class="contenido"></span>
		</div>
</div>

<table class="table table-bordered">
	<thead>
		<tr>
			<th>#</th>
			<th>Sorteo</th>
			<th class="centerCell">Denominacion</th>
			<th class="centerCell">Fecha Sorteo</th>
			<th class="centerCell">Fecha Prescripcion</th>
			<th class="centerCell">Jefe</th>
			<th class="centerCell">Operador</th>
			<th class="centerCell">Escribano</th>
			<th class="centerCell">Accion</th>
		</tr>
	</thead>
	<tbody>
			<?php while ($row = siguiente($res_sorteo)) {?>
			<tr>
				<td class="centerCell"><?php echo $row->RNUM ?></td>
				<td class="centerCell"><?php echo $row->SORTEO ?></td>
				<td class="centerCell"><?php echo $row->DESCRIPCION ?></td>
				<td class="centerCell"><?php echo $row->FECHA_SORTEO ?></td>
				<td class="centerCell"><?php echo $row->FECHA_HASTA_PAGO_PREMIO ?></td>
				<td class="centerCell"><?php echo $row->JEFE_SORTEO ?></td>
				<td class="centerCell"><?php echo $row->OPERADOR ?></td>
				<td class="centerCell"><?php echo $row->ESCRIBANO ?></td>
				<td class="centerCell"><a href="#" onclick="g('administracion/administrar_sorteos/loteria_modificar_sorteo.php?id_sorteo=<?php echo $row->ID_SORTEO ?>')" title="Modificar Sorteo"><div class="fa fa-pencil-square-o fa-2x"></div></a></td>
			</tr>
		<?php }?>
	</tbody>
</table>
<?php
if ($res_anticipados->RecordCount() > 0) {
    ?>
<h4 class="titulo alert alert-info">ANTICIPADOS</h4>
<h4 class="titulo alert alert-info">SEMANA 1</h4>
<?php
}
?>
<h4 class="titulo">Administracion de Sorteo Anticipados</h4>
<table class="table table-bordered">
	<thead>
		<tr>
			<th>#</th>
			<th class="centerCell">Semana</th>
			<th class="centerCell">Orden</th>
			<th class="centerCell">Premio</th>
			<th class="centerCell">Fecha Sorteo</th>
			<th class="centerCell">Jefe</th>
			<th class="centerCell">Escribano</th>
			<th class="centerCell">Accion</th>
		</tr>
	</thead>
	<tbody>
			<?php
$i = 1;
while ($row_anticipada = siguiente($res_anticipados)) {

    if ($res_anticipados->CurrentRow() == 1) {
        $semana = $row_anticipada->SEMANA;
    }
    if ($semana != $row_anticipada->SEMANA) {
        $semana = $row_anticipada->SEMANA;
        $i      = 1;
        ?>
</tbody></table>
<h4 class="titulo alert alert-info">SEMANA <?php echo $semana ?></h4>

<table class="table table-bordered">
<thead>
		<tr>
			<th>#</th>
			<th class="centerCell">Semana</th>
			<th class="centerCell">Orden</th>
			<th class="centerCell">Premio</th>
			<th class="centerCell">Fecha Sorteo</th>
			<th class="centerCell">Jefe</th>
			<th class="centerCell">Escribano</th>
			<th class="centerCell">Accion</th>
		</tr>
	</thead>
	<tbody>
<?php

    }
    ?>
			<tr>
				<td class="centerCell"><?php echo $i; ?></td>
				<td class="centerCell"><?php echo $row_anticipada->SEMANA ?></td>
				<td class="centerCell"><?php echo $row_anticipada->ORDEN ?></td>
				<td class="leftCell"><?php echo $row_anticipada->PREMIO ?></td>
				<td class="centerCell">
					<div class="control-group">
						<div class="controls">
						<div id="fecha_sorteo_cal_<?php echo $row_anticipada->RNUM ?>" class="fecha_sorteo_cal input-append">
							<input id="fecha_sorteo_<?php echo $row_anticipada->RNUM ?>" style="width:100px"  data-format="dd/MM/yyyy" value="<?php echo $row_anticipada->FECHA_SORTEO ?>" type="text" class="input-small recordar" placeholder="obligatorio">
							<span class="add-on">
								<i data-time-icon="icon-time" data-date-icon="icon-calendar" class="icon-calendar"></i>
							</span>

						</div>

					</div>
		</div>
				</td>
				<td class="centerCell">
					<div class="controls">
						<select class="filter-option pull-left" id="jefe_sorteo<?php echo $row_anticipada->RNUM ?>" name="jefe_sorteo<?php echo $row_anticipada->RNUM ?>">
							<option value="-1" >Seleccionar</option>
							<?php
$res_usuario->MoveFirst();
    while ($row = siguiente($res_usuario)) {?>
			    				<option value="<?php echo $row->ID_USUARIO ?>" <?php echo ($row->ID_USUARIO == $row_anticipada->ID_JEFE) ? 'selected' : ''; ?>><?php echo $row->DESCRIPCION ?></option>
			    			<?php }?>
			  			</select>
  					</div>
  				</td>
				<td class="centerCell">
					<div class="controls">
						<select class="filter-option pull-left" id="escribano<?php echo $row_anticipada->RNUM ?>" name="escribano<?php echo $row_anticipada->RNUM ?>">
							<option value="-1" >Seleccionar</option>
							<?php
$res_escribano->MoveFirst();
    while ($row = siguiente($res_escribano)) {?>
			    				<option value="<?php echo $row->ID_ESCRIBANO ?>" <?php echo ($row->ID_ESCRIBANO == $row_anticipada->ID_ESCRIBANO) ? 'selected' : ''; ?>><?php echo $row->DESCRIPCION ?></option>
			    			<?php }?>
			  			</select>
  					</div>
				</td>

				<td><a href="" onclick="$.post('administracion/administrar_sorteos/ajax.php',
													{
  													  accion:'modificar_anticipada',
													  semana: <?php echo $row_anticipada->SEMANA ?>,
													  jefe:$('#jefe_sorteo<?php echo $row_anticipada->RNUM ?> option:selected').val(),
													  escribano:$('#escribano<?php echo $row_anticipada->RNUM ?> option:selected').val(),
													  fecha:$('#fecha_sorteo_<?php echo $row_anticipada->RNUM ?>').val()
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
												); return false;"><div class="fa fa-check fa-2x"></div></a> </td>
			</tr>
		<?php
$i += 1;
}
?>
	</tbody>
</table>
<script type="text/javascript">
$(function(){
	$('.fecha_sorteo_cal').datetimepicker({
	//	pickDate: true,
		format: 'dd/MM/yyyy'
	});
	//$("#fecha_sorteo_cal").datetimepicker("setDate","<?php echo $row_sorteo->FECHA_SORTEO ?>" );

});
</script>

