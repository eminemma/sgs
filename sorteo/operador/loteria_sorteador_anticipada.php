<?php
@session_start();
include_once dirname(__FILE__) . '/../../db.php';

?>
    <div class="row-fluid show-grid">
        <div id="contendio_juego" class="well form-inline text-center">
            <h4 style="margin-bottom:50px;">Lotería - sorteo anticipada</h4>
            <?php

try {
    $today            = date("d/m/Y");
    $lista_anticipada = sql("SELECT ID_JUEGO,SORTEO,SEMANA,PREMIO,ID_JEFE,
                                    ID_ESCRIBANO,PRESCRIPCION,PROX_SORTEO,
                                    PREMIO_PROX_SORTEO,to_char(FECHA_SORTEO,'dd/mm/yyyy') as FECHA_SORTEO,
                                    IMPORTE,ORDEN
                            FROM SGS.T_ANTICIPADA
                            WHERE ID_JUEGO = ?
                                AND SORTEO = ?
                                and FECHA_SORTEO = to_date(?,'dd/mm/yyyy')
                            ORDER BY SEMANA,ORDEN asc", array($_SESSION['id_juego'], $_SESSION['sorteo'], $today));
    $tabindex = 1;
    $cantidad = $lista_anticipada->RecordCount();
    while ($row_anticipada = siguiente($lista_anticipada)) {
        ?>
                <form class="form-inline" action="#" id="fanticipada_<?php echo $row_anticipada->SEMANA; ?>_<?php echo $row_anticipada->ORDEN; ?>">
                    <table align="center" width="90%">
                        <tr>
                            <td align="left" style="padding-right:40px;" width="60%">
                                <a href="#" class="ver_semana_<?php echo $row_anticipada->SEMANA; ?>" onclick="cambiar_juego(this.className,<?php echo $row_anticipada->ORDEN; ?>); return false;"><img src="img/icono_screen.png" width="25" height="25" border="0" style="vertical-align: middle;">Semana <b><?php echo $row_anticipada->SEMANA; ?> Premio <?php echo $row_anticipada->ORDEN; ?></b> (
                                    <?php echo date('d/m/Y', strtotime(str_replace("/", "-", $row_anticipada->FECHA_SORTEO))); ?>)
                                    <?php echo $row_anticipada->PREMIO; ?>
                                </a>
                            </td>
                            <td align="left">
                            </td>
                            <td align="left">
                                <input type="button" id="semana_<?php echo $row_anticipada->SEMANA; ?>_<?php echo $row_anticipada->ORDEN; ?>" name="semana_<?php echo $row_anticipada->SEMANA; ?>_<?php echo $row_anticipada->ORDEN; ?>" Value="Sortear" <?php if ($row_anticipada->FECHA_SORTEO != $today) {;?>disabled
                                <?php }
        ?> />
                                <input type="hidden" id="semana" name="semana" value="<?php echo $row_anticipada->SEMANA; ?>" />
                                <input type="hidden" id="orden" name="orden" value="<?php echo $row_anticipada->ORDEN; ?>" />
                                <input type="hidden" id="accion" name="accion" value="control_ingreso" />
                                <input type="hidden" id="posicion" name="posicion" value="25" />
                                <input type="hidden" id="juego" name="juego" value="<?php echo $row_anticipada->SEMANA; ?>" />
                            </td>
                            <td>
                            <?php
if ($cantidad == $lista_anticipada->CurrentRow()) {
            ?>
<a href="#" onclick="mostrar_resumen('<?php echo $row_anticipada->SEMANA; ?>'); return false;"><img src="img/icono_screen.png" width="25" height="25" border="0" style="vertical-align: middle;">Resumen Semana <b><?php echo $row_anticipada->SEMANA; ?></a>
<?php
}
        ?>
        </td>
                        </tr>
                    </table>
                </form>
                <?php
}

} catch (exception $e) {
    $mensaje = "No se encontraron sorteos anticipados";
}

?>
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
    loteria_anticipada = {};
    $(function() {
        cargarConfiguracion({
            accion: "configuracion",
            juego: "anticipados"
        });
        $(".actas").click(function() {
            var url = $(this).attr('href');
            var windowName = $(this).attr('id');
            window.open(url, windowName, "height=300,width=400");
        });
    });
    </script>
