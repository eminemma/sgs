
<h3 class="titulo">Parametros Generales</h3>
<?php
session_start();
include_once dirname(__FILE__) . '/db.php';
conectar_db();
$res = sql("SELECT
    VALOR
FROM
    T_PARAMETRO_COMPARTIDO
    where parametro='CARGADOBLE'");
$row        = siguiente($res);
$valor      = $row->VALOR;
$rs_usuario = sql("SELECT TS.ID_JEFE,TS.ID_OPERADOR,S.DESCRIPCION AS USUARIO,E.DESCRIPCION AS USUARIO1
                      FROM SGS.T_SORTEO TS,
                           SUPERUSUARIO.USUARIOS S,
                          SUPERUSUARIO.USUARIOS E
                      WHERE TS.SORTEO = ?
                        AND TS.ID_JUEGO=?
                        AND TS.ID_OPERADOR = S.ID_USUARIO(+)
                        AND TS.ID_JEFE = E.ID_USUARIO(+)", array($_SESSION['sorteo'], $_SESSION['id_juego']));
if (!$row = siguiente($rs_usuario)) {
    die(error('Sorteo inexistente, debe seleccionar el sorteo'));
}
if ($row->ID_JEFE != 'DU' . $_SESSION['dni']) {
    die(error('Solo el jefe de sorteo esta habilitado para acceder a esta funcion'));
}
?>
<div class="resultado">
	<div class="error alert alert-error" onclick="$(this).fadeOut()" style="display:none">

		<div class="contenido_error"></div>
	</div>

	<div class="ok alert alert-success" onclick="$(this).fadeOut()" style="display:none">
		<i class="icon-ok"></i>
		<span  class="contenido"></div>
	</div>
</div>
<form method="post" action="#" onsubmit="$.post('procesar_parametros_generales.php',
													$('form').serialize(),
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

		 <fieldset>
          <legend>Tipo de Carga Sorteador:</legend>
            <div class="control-group">
    <div class="controls">

          <div class="checkbox">
    	  <input type="radio" id="opcion1" name="opciones" <?php echo ($valor == 'N' ? 'checked' : '') ?> value="uno"
                   checked>
            Carga un operador
          </div>
    	</div>
	</div>
          <div class="control-group">
    <div class="controls">
          <div class="checkbox">
         	<input type="radio" id="opcion2" name="opciones" <?php echo ($valor == 'S' ? 'checked' : '') ?> value="dos">
         	Carga dos operadores
          </div>
</div>
</div>
      </fieldset>

	<div class="control-group">
    <div class="controls">
      <input type="hidden" id="id_sorteo" name="id_sorteo" value="<?php echo $id_sorteo ?>">
		<button type="submit" class="btn" >Guardar</button>
		<button type="button" class="btn">Cancelar</button>
    </div>
  </div>

</form>