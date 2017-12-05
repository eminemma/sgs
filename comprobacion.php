<?php
require_once 'db.php';
require_once 'paginador.php';
$sql = "SELECT HASH_INGRESADO, HASH_COMPROBADO, to_char(FECHA,'dd/mm/yyyy hh24:mi:ss') as FECHA FROM SGS.T_AUDITORIA_COMPROBACION ORDER BY to_date(FECHA,'dd/mm/yyyy hh24:mi:ss') desc";
$rs  = getPaginadorRs($sql);
getPaginadorLinks('#resultado');
?>
<h4>Listado de Comprobaciones</h4>
<table class="table table-bordered">
 <thead>
    <tr>
        <th>Hash Ingresado</th>
        <th>Hash SGS Software</th>
        <th>Validacion</th>
        <th>Fecha</th>
    </tr>
 </thead>
 <tbody>
 <?php while ($row = siguiente($rs)) {
    ?>
    <tr>
        <td><?php echo $row->HASH_INGRESADO; ?></td>
        <td><?php echo $row->HASH_COMPROBADO; ?></td>
        <td><?php
if ($row->HASH_INGRESADO == $row->HASH_COMPROBADO) {

        ?><span class="label label-success">Correcto</span><?php } else {?><span class="label label-important">Incorrecto</span><?php }?></td>
        <td><?php echo $row->FECHA; ?></td>

    </tr>
    <?php }?>
 </tbody>
</table>
<?php getPaginadorLinks('#resultado');?>