<?php
require_once 'db.php';
if ($_SESSION['dni'] != '32389001' && $_SESSION['dni'] != '29964128') {
    error('Usted no tiene permiso para acceder');
    exit;
}

$accion = $_GET['accion'];
if ($accion == 'generar') {
    conectar_db();
    $exito = true;
    if (isset($_GET['version']) && strlen(trim($_GET['version'])) == 0) {
        error('Es necesario ingresar version de software');
        $exito = false;
    }
    if (isset($_GET['detalle']) && strlen(trim($_GET['detalle'])) == 0) {
        error('Es necesario ingresar el detalle de software');
        $exito = false;
    }

    if ($exito) {
        $db->StartTrans();

        $rs       = sql("SELECT SGS.F_COMPROBAR_MD5() as MD5_BASE_DATOS FROM DUAL");
        $row      = siguiente($rs);
        $md5_base = $row->MD5_BASE_DATOS;
        $rs       = sql("SELECT SEC_T_VERSION.NEXTVAL AS ID FROM DUAL");
        $row      = siguiente($rs);
        $id       = $row->ID;
        sql("INSERT
        INTO SGS.T_VERSION
          (
            ID,
            VERSION,
            DETALLE
          )
          VALUES
          (
            ?,
            ?,
            ?
          )", array($id, $_GET['version'], $_GET['detalle']));

        sql("INSERT
                INTO SGS.T_VERSION_DETALLE
                  (
                    ID_VERSION,
                    HASH,
                    ARCHIVO,
                    OBJETO
                  )
                  VALUES
                  (
                    ?,
                    ?,
                    ?,
                    ?
                  )", array($id, $md5_base, null, 'BASE DE DATOS'));

        //MD5 DE TODOS LOS ARCHIVOS CONCATENADOS Y UN MD5 UNICO
        $donde       = str_replace('\\', '/', realpath(dirname(__FILE__) . '/../sgs'));
        $encontrados = 0;
        $md5_files   = array();
        $ritit       = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($donde, RecursiveIteratorIterator::LEAVES_ONLY));
        foreach ($ritit as $item) {
            $file = str_replace('\\', '/', $item->getPathName());
            if (strpos($file, '.svn') === false && is_file($file)) {
                $md5_files[] = md5_file($file);
                $encontrados++;
                sql("INSERT
                INTO SGS.T_VERSION_DETALLE
                  (
                    ID_VERSION,
                    HASH,
                    ARCHIVO,
                    OBJETO
                  )
                  VALUES
                  (
                    ?,
                    ?,
                    ?,
                    ?
                  )", array($id, md5_file($file), $file, 'ARCHIVO'));
            }
        }
        $md5_concat = null;
        foreach ($md5_files as $md5) {
            $md5_concat .= $md5;
        }

        $md5_codigo = md5($md5_concat);

        sql("INSERT
                INTO SGS.T_VERSION_DETALLE
                  (
                    ID_VERSION,
                    HASH,
                    ARCHIVO,
                    OBJETO
                  )
                  VALUES
                  (
                    ?,
                    ?,
                    ?,
                    ?
                  )", array($id, $md5_codigo, null, 'CODIGO FUENTE'));

        $md5_software = md5($md5_base . $md5_codigo);

        sql("UPDATE SGS.T_VERSION
            SET HASH     = ?
            WHERE ID     = ?", array($md5_software, $id));
        $db->CompleteTrans(true);
    }
} else if ($accion == 'eliminar') {
    conectar_db();
    $id = $_GET['id'];
    sql("DELETE FROM SGS.T_VERSION_DETALLE WHERE ID_VERSION = ?", array($id));
    sql("DELETE FROM SGS.T_VERSION WHERE ID = ?", array($id));
    ok('Se eliminó correctamente la version');
}
?>
<form method="post" action="#"  class="form-horizontal" onsubmit="generarHash(); return false;" >
        <h4>
            Versionado de Software
        </h4>
        <div class="control-group">
            <label class="control-label" for="version">Version</label>
            <div class="controls">
                <div id="v" class="input-append">
                    <input id="version" name="version" style="width:300px"  type="text" class="input-small recordar" placeholder="Ingresar Numero Version">
                </div>
            </div>
        </div>
        <div class="control-group">
            <label class="control-label" for="detalle">Detalle de Version</label>
            <div class="controls">
                <div id="d" class="input-append">
                    <textarea rows="4" cols="50" id="detalle" name="detalle" style="width:300px"  type="text" class="input-small recordar" placeholder="Ingresar Numero Version"></textarea>
                </div>
            </div>
        </div>
        <div class="control-group">
            <div class="controls">
                <button type="submit" class="btn" >Generar Version</button>
            </div>
        </div>
</form>
<script type="text/javascript">

function generarHash(){
    if(confirm('¿Desea generar hash de la version de software?')){
        g('version_alta.php?accion=generar&'+$("form:eq(2)").serialize());
    }

}

function eliminarHash(version){
    if(confirm('¿Desea eliminar hash de la version de software?')){
        g('version_alta.php?accion=eliminar&id='+version+'&'+$("form:eq(2)").serialize());
    }

}

$(document).ready(
        function() {
            g('version_detalle.php','#resultado');
        }
);
</script>
<div id="resultado"></div>
