<?php
@session_start();
require_once dirname(__FILE__) . '/librerias/adodb/adodb.inc.php';
require_once dirname(__FILE__) . '/librerias/adodb/adodb-exceptions.inc.php';
include_once dirname(__FILE__) . '/mensajes.php';

function conectar_db_kanban()
{
    global $db_kanban;

    $db_kanban          = NewADOConnection('oci8po');
    $db_kanban->charSet = 'utf8';
    /*
    DESA
    $db_kanban->Connect("(DESCRIPTION =
    (ADDRESS =
    (PROTOCOL = TCP)
    (HOST = 172.16.50.18)
    (PORT = 1521)
    (HASH = '".rand(0,99999999)."')
    )
    (CONNECT_DATA =(SID = DESA)))", 'DU'.$_SESSION['dni'], $_SESSION['clave']);

     */

    //PROD
    /*
    try{
    $db_kanban->Connect("(DESCRIPTION =
    (ADDRESS =
    (PROTOCOL = TCP)
    (HOST = nscentral-scan.loteriadecordoba.com.ar)
    (PORT = 1521)
    (HASH = '".rand(0,99999999)."')
    )
    (CONNECT_DATA =(SERVICE_NAME = CENTRAL)))", 'kanban', 'dukanban');
     */

    /*
    try{

    $db_kanban->Connect("(DESCRIPTION =
    (ADDRESS =
    (PROTOCOL = TCP)
    (HOST = 172.16.50.18)
    (PORT = 1521)
    (HASH = '".rand(0,99999999)."')
    )
    (CONNECT_DATA =(SID = DESA)))", 'kanban', 'dukanban');
     */

    try {
        $db_kanban->Connect("(DESCRIPTION =
                (ADDRESS =
                (PROTOCOL = TCP)
                (HOST = 172.16.50.18)
                (PORT = 1521)
                (HASH = '" . rand(0, 99999999) . "')
                )
                (CONNECT_DATA =(SID = desa_01)))", 'sgs_importacion', 'esquema');

        return true;
    } catch (Exception $e) {
        $error = oci_error();

        if ($error['code'] == 28000) {
            $error = 'La Cuenta se encuentra bloqueada, por favor comuniquese con el administrador';
        } else if ($error['code'] == 1017) {
            $error = 'Usuario y/o contraseña incorrecta';
        } else {
            $error = $error['message'];
        }

        if ($error !== false) {
            error('Error al Conectar a la Base de Datos: ' . $error);
        }

        $_SESSION = array();

        return false;
    }

}

function sql_kanban($sql, $variables)
{
    global $db_kanban;

    if (!isset($db_kanban)) {
        conectar_db_kanban();
    }

    try {
        return $db_kanban->Execute($sql, $variables);
    } catch (exception $e) {
        $error = 'Error: ' . $db_kanban->ErrorMsg() . "\n" . $sql . "\n" . 'Variables: ' . "\n" . print_r($variables, true);
        error($error);
        return false;
    }
}

function siguiente_kanban(&$rs, $array = false)
{
    $retorno = $rs->FetchNextObject();

    if ($retorno) {
        return $array ? (array) $retorno : $retorno;
    } else {
        return false;
    }

}

function ComenzarTransaccion_kanban($db_kanban)
{
    $db_kanban->StartTrans();
}

function FinalizarTransaccion_kanban($db_kanban)
{
    $db_kanban->CompleteTrans(true);
}
