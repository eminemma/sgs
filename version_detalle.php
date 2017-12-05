<?php
require_once 'db.php';
require_once 'paginador.php';
$sql = "SELECT VERSION, DETALLE, HASH, TO_CHAR(FECHA_VERSION,'dd/mm/yyyy HH:mi:ss') as FECHA_VERSION, ID FROM SGS.T_VERSION ORDER BY FECHA_VERSION desc";
$rs  = getPaginadorRs($sql);

if ($rs->RecordCount() == 0) {
    die(error('No existen versiones del software'));
    getPaginadorLinks('#resultado');
}
?>
<h4>Listado de Comprobaciones</h4>
<table class="table table-bordered">
 <thead>
    <tr>
        <th>Version</th>
        <th>Hash</th>
        <th>Fecha Version</th>
        <th>Acciones</th>
    </tr>
 </thead>
 <tbody>
 <?php while ($row = siguiente($rs)) {
    ?>
    <tr>
        <td><?php echo $row->VERSION; ?></td>
        <td><?php echo $row->HASH; ?></td>
        <td><?php echo $row->FECHA_VERSION; ?></td>
        <td><a target="_blank" href="pdf_version.php?id=<?php echo $row->ID; ?>" title="Reporte de Version"><div class="fa fa-print fa-2x"></div></a></td>
        <td><a target="_blank" href="pdf_version_detalle.php?id=<?php echo $row->ID; ?>" title="Reporte de Version"><div class="fa fa-print fa-2x"></div></a></td>
        <td><a onclick="eliminarHash('<?php echo $row->ID; ?>')" title="Eliminar Version"><div class="fa fa-trash-o fa-2x"></div></a></td>
    </tr>
    <?php }?>
 </tbody>
</table>
<?php getPaginadorLinks('#resultado');?>