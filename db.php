<?php
@session_start();
require_once dirname(__FILE__) . '/librerias/adodb/adodb.inc.php';
require_once dirname(__FILE__) . '/librerias/adodb/adodb-exceptions.inc.php';
include_once dirname(__FILE__) . '/mensajes.php';

function conectar_db()
{
    global $db;

    $db = NewADOConnection('oci8po');
    //$db->SetCharSet('utf8');
    $db->charSet = 'utf8';
    /*
    $db->Connect("(DESCRIPTION =
    (ADDRESS =
    (PROTOCOL = TCP)
    (HOST = 172.16.50.18)
    (PORT = 1521)
    (HASH = '".rand(0,99999999)."')
    )
    (CONNECT_DATA =(SID = DESA)))", 'DU'.$_SESSION['dni'], $_SESSION['clave']);
    return true;

     */
    try {
        // $db->PConnect("(DESCRIPTION =
        //                 (ADDRESS =
        //             (PROTOCOL = TCP)
        //                 (HOST = 172.16.50.18)
        //                 (PORT = 1521)
        //                 (HASH = '1')
        //              )
        // (CONNECT_DATA =(SID = DESA)))", 'DU'.$_SESSION['dni'], $_SESSION['clave']);
        /*
        $db->PConnect("(DESCRIPTION =
        (ADDRESS =
        (PROTOCOL = TCP)
        (HOST = 172.16.50.152)
        (PORT = 1521)
        )
        (CONNECT_DATA =(SID = XE)))", 'sgs', 'esquema');
         */
        $db->PConnect("(DESCRIPTION =
                        (ADDRESS =
                    (PROTOCOL = TCP)
                        (HOST = localhost)
                        (PORT = 1521)
                        (HASH = '1')
                     )
                (CONNECT_DATA =(SID = xe)))", 'sgs', 'esquema');

        return true;
    } catch (Exception $e) {

        $error = oci_error();
        if ($error['code'] == 28000) {
            $error = 'La Cuenta se encuentra bloqueada, por favor comuniquese con el administrador';
        } else if ($error['code'] == 1017) {
            $error = 'Usuario y/o contraseña incorrecta';
        } else if ($error['code'] == 1005) {
            $error = 'No se paso correctamente el usuario/password';
        } else {
            $error = $error['message'];
        }

        if ($error !== false) {
            error('Error al Conectar a la Base de Datos: ' . $error);
        }

        exit;
        $_SESSION = array();

        return false;

    }
}

function sql($sql, $variables)
{
    global $db;

    if (!isset($db)) {
        conectar_db();
    }

    try {

        return $db->Execute($sql, $variables);
    } catch (exception $e) {
        $error = 'Error: ' . $db->ErrorMsg() . "\n" . $sql . "\n" . 'Variables: ' . "\n" . print_r($variables, true);
        error($error);
        return false;
    }
}

function siguiente(&$rs, $array = false)
{
    $retorno = $rs->FetchNextObject();

    if ($retorno) {
        return $array ? (array) $retorno : $retorno;
    } else {
        return false;
    }

}

function ComenzarTransaccion($db)
{
    $db->StartTrans();
}

function FinalizarTransaccion($db)
{
    $db->CompleteTrans(true);
}
