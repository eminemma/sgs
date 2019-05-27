<?php
session_start();
include_once dirname(__FILE__) . '/../../db.php';
conectar_db();

header("Pragma: public");
header("Expires: 0");
header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
header("Cache-Control: public");
header("../numero_letra.php");
require "header_listado_b.php";

$mostrar = false;
$semana  = 1;

if (!is_file('../escribano/escribano_img/gordo_invierno_2019_semana' . $semana . '_extracto.png')) {
    if (is_file('../escribano/escribano_img/gordo_invierno_2019_semana' . $semana . '_extracto.jpg')) {
        include_once 'imagen_anticipada_semana.php';
    }
}
$pdf = new PDF('L', 'mm', 'A4');
$pdf->AliasNbPages();
$pdf->AddPage();
$xnombre = $_GET['nombre'];
$xurl    = $_GET['url'];
$ximagen = $_GET['imagen'];
$pdf->Image('../escribano/escribano_img/gordo_invierno_2019' . $semana . '.png', 5, 20, 288, 170);
$pdf->Output(dirname(__FILE__) . '/../../extracto/' . $_SESSION['sorteo'] . '_SEMANA_' . $semana . '.pdf', F);
$pdf->Output();
unlink('../escribano/escribano_img/gordo_invierno_2019' . $semana . '_extracto.png');
