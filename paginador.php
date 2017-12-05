<?php
$paginador_total_rows = 0;
$paginador_url = $_SERVER['PHP_SELF'].'?';
$paginador_registros = 0;
$paginador_pagina_actual = 1;

function getPaginadorRs($sql, $variables, $registros = 12){
	global $paginador_total_rows, $paginador_registros, $paginador_url, $paginador_pagina_actual;
	$paginador_registros = $registros;

	foreach ($_GET as $key => $value){
		if($key != 'pagina')
			$paginador_url .= '&'.$key.'='.$value;
	}

	$paginador_pagina_actual = isset($_GET['pagina']) ? (int)$_GET['pagina'] : 1;
	$min = ($registros * ($paginador_pagina_actual - 1)) + 1;
	$max = $registros * $paginador_pagina_actual;
	
	$rs = sql('SELECT
					noel2.*, rnum as PAGINADOR_ROW
				FROM
					(
						SELECT
							noel.*, ROWNUM rnum 
						FROM
							('.$sql.') noel
						WHERE ROWNUM <= '.$max.'
						) noel2
				WHERE rnum  >= '.$min,
				$variables);

	$res_count = sql('	SELECT
							COUNT(*) as TOTAL
						FROM
							('.$sql.')',
						$variables);
	$paginador_total_rows = siguiente($res_count)->TOTAL;

	echo '<script type="text/javascript">$("#cantidad_registros_ultima_consulta").html("'.number_format($paginador_total_rows, 0, ',', '.').'");</script>';

	return $rs;
}

function getPaginadorLinks($contenedor){
	global $paginador_total_rows, $paginador_url, $paginador_registros, $paginador_pagina_actual;
	echo '<div class="pagination pagination-centered">';
		echo '<ul>';
		
		$total_paginas = (int)($paginador_total_rows / $paginador_registros);
		
		if($paginador_total_rows % $paginador_registros > 0)
			$total_paginas++;

		$primer_item = $paginador_pagina_actual - 5 <= 0 ? 1 : $paginador_pagina_actual - 5;
		$ultimo_item = $paginador_pagina_actual + 5 >= $total_paginas ? $total_paginas : $paginador_pagina_actual + 5;

		if($paginador_pagina_actual == 1)
			echo "<li class=\"disabled\"><a href=\"#\">&laquo; Primera</a></li>";
		else
			echo "<li><a href=\"#\" onclick=\"g('".$paginador_url."&pagina=1', '".$contenedor."')\">&laquo; Primera</a></li>";
		
		for($i = $primer_item; $i <= $ultimo_item; $i++){
			if($paginador_pagina_actual == $i)
				echo "<li class=\"disabled\"><a href=\"#\">$i</a></li>";
			else
				echo "<li><a href=\"#\" onclick=\"g('".$paginador_url."&pagina=".$i."', '".$contenedor."')\">$i</a></li>";

		}
		
		if($paginador_pagina_actual == $total_paginas)
			echo "<li class=\"disabled\"><a href=\"#\">Última &raquo;</a></li>";
		else
			echo "<li><a href=\"#\" onclick=\"g('".$paginador_url."&pagina=".$total_paginas."', '".$contenedor."')\">Última &raquo;</a></li>";
		
		echo '</ul>';
	echo '</div>';
}