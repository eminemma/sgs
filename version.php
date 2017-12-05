<?php
require_once 'db.php';
//error_reporting(E_ALL);
$accion = $_GET['accion'];
if ($accion == 'comprobar') {
    conectar_db();
    $exito = true;
    if (isset($_GET['hash']) && strlen(trim($_GET['hash'])) == 0) {
        error('Es necesario ingresar el hash');
        $exito = false;
    }
    if ($exito) {
        $db->StartTrans();
        $rs  = sql("SELECT SEC_T_AUDITORIA.NEXTVAL AS ID FROM DUAL");
        $row = siguiente($rs);
        $id  = $row->ID;
        sql("INSERT
                INTO SGS.T_AUDITORIA_COMPROBACION
              (
                ID_AUDITORIA
              )
              VALUES
              (
                ?
              )", array($id));

        $rs       = sql("SELECT SGS.F_COMPROBAR_MD5() as MD5_BASE_DATOS FROM DUAL");
        $row      = siguiente($rs);
        $md5_base = $row->MD5_BASE_DATOS;

        sql("INSERT
                INTO SGS.T_AUDITORIA_DETALLE
                  (
                    ID_AUDITORIA,
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
                INTO SGS.T_AUDITORIA_DETALLE
                  (
                    ID_AUDITORIA,
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
                INTO SGS.T_AUDITORIA_DETALLE
                  (
                    ID_AUDITORIA,
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

        sql("UPDATE SGS.T_AUDITORIA_COMPROBACION
            SET HASH_INGRESADO     = ?,
                HASH_COMPROBADO    = ?
            WHERE ID_AUDITORIA     = ?", array($_GET['hash'], $md5_software, $id));
        $db->CompleteTrans(true);
    }
}
?>


<form method="post" action="#"  class="form-horizontal" onsubmit="comprobarHash(); return false;" >
        <h4>
            Comprobacion
        </h4>
        <div class="control-group">
            <label class="control-label" for="hash">Ultimo Hash Informado</label>
            <div class="controls">
                <div id="h" class="input-append">
                    <input id="hash" name="hash" style="width:300px"  type="text" class="input-small recordar" placeholder="Ingresar Hash">
                </div>
            </div>
        </div>
        <div class="control-group">
            <div class="controls">
              <input type="hidden" id="accion" name="accion" value="comprobar">
                <button type="submit" class="btn" >Comprobar</button>
            </div>
        </div>
</form>
<script type="text/javascript">

function comprobarHash(){
    if(confirm('¿Desea comprobar la version de software?')){
        g('version.php?'+$("form:eq(2)").serialize());
    }

}

$(document).ready(
        function() {
            g('comprobacion.php','#resultado');
        }
);
</script>
<div id="resultado"></div>
