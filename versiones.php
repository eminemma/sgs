<?php
session_start();
include_once 'db.php';
conectar_db();
$rs = sql("SELECT VERSION, DETALLE, HASH, FECHA_VERSION, ID FROM SGS.T_VERSION ORDER BY FECHA_VERSION DESC");

while ($row = siguiente($rs)) {
    echo '<div>Version ' . $row->VERSION . '</div>';
    echo '<pre>' . $row->DETALLE . '</pre>';
    echo '<div>Hash: ' . $row->HASH . '</div><hr>';
}
