<?php
/**
 * loteria_anticipada_datos.php
 *
 * Exportar participantes a CSV
 *
 * @author     Emmanuel Quattropani<emmanuel.quattropani@loteriacba.com.ar>
 * Date: 16/05/2017
 */
include_once dirname(__FILE__) . '/../../db.php';
conectar_db();
$sorteo   = (int) $_SESSION['sorteo'];
$id_juego = (int) $_SESSION['id_juego'];
try {
    $path = '';
    $rs   = $db->execute("SELECT DIRECTORY_PATH FROM ALL_DIRECTORIES WHERE UPPER(DIRECTORY_NAME) = 'TMP_DOC'");
    if ($rs->RecordCount() == 0) {
        die(error('No existe el directorio TMP_DOC en base de datos'));
    }
    if ($r = $rs->fetchRow()) {
        $path = $r['DIRECTORY_PATH'];
    }

    $procedure      = $db->prepareSp("BEGIN SGS.PR_EXPORTAR_PARTICIPANTES(:P_ID_JUEGO,:P_SORTEO,:NOMBRE_ARCHIVO); END;");
    $nombre_archivo = '';
    $db->inParameter($procedure, $id_juego, "P_ID_JUEGO");
    $db->inParameter($procedure, $sorteo, "P_SORTEO");
    $db->OutParameter($procedure, $nombre_archivo, "NOMBRE_ARCHIVO");
    $ok = $db->Execute($procedure);
    if (!$ok) {
        die(error('Error al generar CSV, ' . $_SESSION['sorteo'] . ' de Loteria'));
    }

    /*$server_file = "/sgs/" . $nombre_archivo;
    $file        = fopen($nombre_archivo, "w");
    $local_file  = __DIR__ . "/" . $nombre_archivo;

    $ftp_conn = ConectarFTP();
    ftp_pasv($ftp_conn, true);
    if (!ftp_get($ftp_conn, $local_file, $server_file, FTP_BINARY)) {
    die("Error descargando $server_file.");
    }
    ftp_close($ftp_conn);*/
    /* $remoteDir = $path . "/";
    $localDir  = __DIR__ . "/";
    $stream    = ConectarSSH();
    if (!$dir = opendir("ssh2.sftp://{$stream}{$remoteDir}")) {
    die('Could not open the directory');
    }

    $files   = array();
    $files[] = $nombre_archivo;

    foreach ($files as $file) {
    if (!$remote = @fopen("ssh2.sftp://{$stream}/{$remoteDir}{$file}", 'r')) {
    echo "Error: no se puede abrir archivo remoto file: $file\n";
    continue;
    }

    if (!$local = @fopen($localDir . $file, 'w')) {
    echo "Error no se pudo crear archivo local: $file\n";
    continue;
    }

    $read     = 0;
    $filesize = filesize("ssh2.sftp://{$stream}/{$remoteDir}{$file}");
    while ($read < $filesize && ($buffer = fread($remote, $filesize - $read))) {
    $read += strlen($buffer);
    if (fwrite($local, $buffer) === false) {
    echo "Error no se pudo escribir sobre el archivo  file: $file\n";
    break;
    }
    }
    fclose($local);
    fclose($remote);
    }*/

    header('Content-Type: application/csv');
    header('Content-Disposition: attachment; filename=' . $nombre_archivo);
    header('Pragma: no-cache');
    readfile("/home/oracle/directorios/sgs/" . $nombre_archivo);
    unlink(__DIR__ . "/" . $nombre_archivo);
} catch (Exception $e) {
    die($db->ErrorMsg());
}

function ConectarFTP($ruta = '')
{
    $servidor = "desa-ent.loteriadecordoba.com.ar";
    $puerto   = 21;
    $timeout  = 50;
    $user     = "oracleftp";
    $pass     = "ora+123";

    $id_ftp = ftp_connect($servidor, $puerto, $timeout); //Obtiene un manejador del Servidor FTP
    if (!$id_ftp) {
        die("No se pudo conectar al Servidor FTP $servidor");
    }

    ftp_login($id_ftp, $user, $pass); //Logueo al Servidor FTP
    if (!empty($ruta)) {
        ftp_chdir($id_ftp, $ruta);
    }
    return $id_ftp;
}

function ConectarSSH()
{
    $host     = '172.16.50.18';
    $port     = 22;
    $username = 'desarrollo';
    $password = 'Desa123';
    if (!function_exists("ssh2_connect")) {
        die('Function ssh2_connect not found, you cannot use ssh2 here');
    }

    if (!$connection = ssh2_connect($host, $port)) {
        die('Error: Conexion fallida al servidor ftp');
    }

    if (!ssh2_auth_password($connection, $username, $password)) {
        die('Error: Las credenciales no son correctas al ftp');
    }

    if (!$stream = ssh2_sftp($connection)) {
        die('Error: No se puede crear conexion al ftp');
    }

    return $stream;
}
