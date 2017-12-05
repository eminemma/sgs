<?php
@session_start();
include_once dirname(__FILE__).'/../../db.php';
include_once dirname(__FILE__).'/../../librerias/alambre/funcion.inc.php';

?>
<div class="row-fluid show-grid">		
	<div id="contendio_juego" class="well form-inline text-center">
		<h4>Incentivo a Agencieros</h4>
		<form class="form-inline" action="#">
		<table align="center">
        
		
<?php


try{
	//var_dump($_SESSION);
	//echo "SELECT ID_JUEGO,SORTEO,ID_INCENTIVO,DESCRIPCION,IMPORTE,FECHA_SORTEO FROM DESA.T_INCENTIVOS WHERE ID_JUEGO = ".$_SESSION['id_juego']." AND SORTEO = ".$_SESSION['sorteo']." ORDER BY ID_INCENTIVO";
	
	$lista_incentivo = sql("SELECT ID_JUEGO,SORTEO,ID_INCENTIVO,DESCRIPCION,IMPORTE,FECHA_SORTEO FROM SGS.T_INCENTIVOS WHERE ID_JUEGO = ? AND SORTEO = ? ORDER BY ID_INCENTIVO DESC",array($_SESSION['id_juego'], $_SESSION['sorteo']));
	//DESC
	$tabindex =1;
	$tabname = '';
	while($row_incentivo = siguiente($lista_incentivo)){
		if($tabname != $row_incentivo->DESCRIPCION){
			$tabname = $row_incentivo->DESCRIPCION;
			$tabindex =1;
			if(!empty($tabname)){
				echo "</tr>";
			}
			echo "<tr>";
		}
		if($tabindex%3 ==0)echo "</tr>";
?>
		<?php 
		if($tabindex == 1) {
		?>
		<td style="padding-top:45px;" colspan="3"></td></tr><tr>
		<td style="border-top:1px solid #e3e3e3;padding-top:5px;background-color:#e3e3e3;" colspan="2"><b><?php echo $row_incentivo->DESCRIPCION; ?></b> - PREMIO: <b>$ <?php echo $row_incentivo->IMPORTE; ?></b></td>
		<td style="border-top:1px solid #e3e3e3;background-color:#e3e3e3;" align="center"><a href="sorteo/acta/loteria_acta_incentivo_multiple.php?id_incentivo=<?php echo $row_incentivo->ID_INCENTIVO; ?>" target="_blank" id="Alcanzan Objetivo"><img src="img/printer.png " class="actas-print"/></a></td></tr><tr>
		<td align="center" width="33%" style="padding-top:15px;">
		<?php
		}else{
		?><td align="center" width="33%" style="padding-top:15px;"><?php
		}
		?>
		<a href="#" class="ver_<?php echo $row_incentivo->ID_INCENTIVO; ?>" onclick="cambiar_juego(this.className); return false;"><img src="img/icono_screen.png" width="25" height="25" border="0" style="vertical-align: middle;"><?php echo $tabindex; ?>: (mostrar <br><?php echo $row_incentivo->DESCRIPCION; ?>) </a><br />
		<input type="button" id="incentivo_<?php echo $row_incentivo->ID_INCENTIVO; ?>" name="incentivo_<?php echo $row_incentivo->ID_INCENTIVO; ?>" Value="<?php echo $tabindex . ': ' .$row_incentivo->DESCRIPCION; ?>" class="boton-sorteo" style="font-size:12px;" />
	</td>
<?php
		$tabindex++;
	}
	echo "</tr>";
	
}catch  (exception $e){ 

	$mensaje="No se encontraron incentivos para el sorteo " . $_SESSION['sorteo'];	
	
}

?>
		</table>
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
    preparaIncentivo();
});
</script>

<script type="text/javascript">
    $(document).ready(function(){
        $(".actas").click(function(){
            var url = $(this).attr('href');
            var windowName = $(this).attr('id');

            window.open(url, windowName, "height=300,width=400");
        });
    });
</script>
