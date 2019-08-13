<?php
/*
/*     Mando de extracto anticipado a Personas Involucradas con la informacion
/*
/*        @author Emmanuel Quattropani (Programador) 09/06/2017
 */
@session_start();
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\PHPMailer;

require __DIR__ . '/../vendor/autoload.php';
include_once dirname(__FILE__) . '/../config/cuentas_email.php';
//error_reporting(E_ALL);
//ini_set('display_errors', 1);
set_time_limit(0);
$semana = $_GET['semana'];
include dirname(__FILE__) . '/../db.php';

if (!is_file(dirname(__FILE__) . '/../sorteo/acta/' . $_SESSION['sorteo'] . '_SEMANA_' . $semana . '.pdf')) {
    die(error('No se genero el extracto anticipado, y no se puede enviar al email'));
}

$mail              = new PHPMailer(true);
//$mail->SMTPDebug   = 4;
$mail->SMTPAutoTLS = "false";
$mail->SMTPSecure  = "";
$mail->PluginDir   = "includes/";
$mail->Mailer      = "smtp";
$mail->Host        = "mail.loteriacba.com.ar";
//$mail->Host      = "172.16.51.10";
//$mail->Host      = "mail.loteriadecordoba.com.ar";
$mail->Port     = "25";
$mail->FromName = "Loteria de Cordoba";
$mail->Timeout  = 30;
$mail->CharSet  = 'UTF-8';
$mail->From     = "aplicativos@loteriacba.com.ar";
$mail->FromName = "Loteria de Cordoba";
$mail->Subject  = 'KANBAN - Sorteo: ' . $_SESSION['sorteo'] . ' - Semana Nº' . $semana;

$mail->AddAttachment(dirname(__FILE__) . '/../sorteo/acta/' . $_SESSION['sorteo'] . '_SEMANA_' . $semana . '.pdf', $_SESSION['sorteo'] . '_SEMANA_' . $semana . '.pdf');
$mail->AddAddress($email['sorteo']);

$mail->IsHTML(true);
conectar_db();
try {
    $rs_hora = $db->Execute("SELECT to_char(sysdate,'dd/mm/yyyy hh24:mi:ss') as hora from dual");
} catch (exception $e) {
    die($db->ErrorMsg());
}
$row_hora = $rs_hora->FetchNextObject($toupper = true);
$hora     = $row_hora->HORA;
$mensaje  = null;
$mensaje .= "El siguiente email fue generado por el sistema SGS, a la hora " . $hora . ": contiene informacion sobre el sorteo " . $_SESSION['sorteo'];

$mail->Body = $mensaje;
$exito      = $mail->Send();

if (!$exito) {
    die(error('Problemas enviando correo electronico' . $mail->ErrorInfo . ''));

} else {
    die(ok('Se envio correctamente el email'));
}
