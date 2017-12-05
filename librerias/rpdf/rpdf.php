<?php
error_reporting(E_ERROR);

require_once dirname(__FILE__).'/librerias/fpdf/fpdf.php';

function RPDF_GenerarReporte($recurso, $variables, $envianRes, $setup){
	global $tituloRPDF, $subTituloRPDF;

	$directorioBase = dirname(__FILE__).'/';

	$tituloRPDF = $setup['titulo'];
	$subTituloRPDF = $setup['subTitulo'];

	$setup['todoCentrado'] = !isset($setup['todoCentrado']) ? true : $setup['todoCentrado'];
	$setup['vertical'] = !isset($setup['vertical']) ? true : $setup['vertical'];

	if($setup['configuracion'] != NULL){
		if(file_exists($directorioBase.'rpdf_'.$setup['configuracion'].'_conf.php')){
			require_once $directorioBase.'rpdf_'.$setup['configuracion'].'_conf.php';
		}else{
			require_once $directorioBase.'rpdf_default_conf.php';
			$errorAlIncluirConfiguracion = true;
		}
	}else{
		require_once $directorioBase.'rpdf_default_conf.php';
	}
				
	$pdf = new RPDF($setup['vertical'] ? 'P' : 'L');
	$pdf->directorioBase = $directorioBase;
	$pdf->setup = $setup;
	$pdf->AliasNbPages();
	$pdf->AddPage();
	$pdf->SetFillColor(240);

	if($errorAlIncluirConfiguracion){
		RPDF_ReportarError($pdf, 'El archivo de configuracion "'.$setup['configuracion'].'" no se encuentra.');
		RPDF_ReportarError($pdf, 'Su nombre debe ser: "rpdf_'.$setup['configuracion'].'_conf.php"');
		RPDF_Salir($pdf);
	}
	
	if($setup['conexion'] != NULL){
		if($setup['conexion']['url'] != NULL){
			require_once $setup['conexion']['url'];
		}else if($setup['conexion']['nombre'] != NULL){
			require_once $directorioBase.'adodb_'.$setup['conexion']['nombre'].'_conexion.php';
		}else{
			RPDF_ReportarError($pdf, 'La configuración de Conexion con la DB no es correcta.');
			RPDF_ReportarError($pdf, 'Los atributos de conexion son: "url" o "nombre"');
			RPDF_Salir($pdf);
		}
	}else
		require_once $directorioBase.'adodb_default_conexion.php';


	//Ejecutamos los PRE-SQL
	if(isset($setup['preSQL']))
		foreach($setup['preSQL'] as $sql)
			RPDF_EvaluarRecurso($sql[0], $sql[1], false);

	
	//VALIDACIONES PREVIAS
	// if(!$envianRes)
		// if(($setup['corte'] || $setup['subTotalizar'])){
			// if($setup['corte'] && !preg_match('/order by(\s)*'.$setup['corte'].'/i', $recurso)){
				// RPDF_ReportarError($pdf, 'AL DEFINIR UN CORTE O SUBTOTALIZAR DEBE ORDERNAR EL SQL EN BASE AL CORTE DEFINIDO.');
				// RPDF_ReportarError($pdf, 'AÑADA: "ORDER BY '.strtoupper($setup['corte']).'" EN LA SENTENCIA.');
				// RPDF_Salir($pdf);
			// }else if(!$setup['corte']){
				// RPDF_ReportarError($pdf, 'AL DEFINIR LOS SUBTOTALIZADORES DEBE DEFINIR UN CORTE.');
				// RPDF_Salir($pdf);
			// }
		// }
	foreach($setup['columnas'] as $columna => $datos)
		if($datos['porcentaje'] == true && ($datos['totalizar'] != true || !in_array($columna, $setup['subTotalizar']))){
			RPDF_ReportarError($pdf, 'Para mostrar el porcentaje de la columna "'.$columna.'", usted debe totalizarla y subTotalizarla');
			RPDF_Salir($pdf);
		}
	//FIN DE VALIDACIONES PREVIAS
	
	//DEFINIMOS VARIABLES GROSAS
		$totalizador = array();		//['COLUMNA'=>20000, 'COLUMNA'=>200]
		$subTotalizador = array();
		$contadorCorte = array();
	//FIN DEFINICION VARIABLES GROSAS
	
	$res = RPDF_EvaluarRecurso($recurso, $variables, $envianRes);
	
	if(!($res->RecordCount() > 0)){ //No ser mayor a cero, significa que puede ser 0, o numeros negativos. No tocar.
		RPDF_ReportarError($pdf, 'No hay datos para mostrar');
		RPDF_Salir($pdf);
	}
	$rows = $res->GetAll();

	/**
		ANALIZADOR GROSO
	*/
	
	//Vemos si hay que Totalizar algo. Definimos las columnas en el array
	foreach($setup['columnas'] as $columna => $datos)
		if($datos['totalizar'])
			$totalizador[$columna] = 0;
	
	//Anchos Minimos por el Ancho del texto de la columna
	if($setup['ajustarAnchos']){
		foreach($setup['columnas'] as $columna => $datos){
			$anchoCelda = RPDF_CalcularAnchoCelda($columna, $datos['tipo']);
			if($datos['ancho'] < $anchoCelda)
				$setup['columnas'][$columna]['ancho'] = $anchoCelda;
		}
	}
	
	foreach($rows as $row){
		//Contamos
		if(count($setup['corte']) === 1)
			$contadorCorte[$row[$setup['corte'][0]]]++;
		else if(count($setup['corte']) === 2)
			$contadorCorte[$row[$setup['corte'][0]]][$row[$setup['corte'][1]]]++;

		//Totalizamos: Recorro el array de totalizacion buscando en el ROW el valor y se lo sumo
		foreach($totalizador as $columna => $datos)
			if(isset($setup['columnas'][$columna]['totalizarSi'])){
				$condicion = $setup['columnas'][$columna]['totalizarSi'];
				
				foreach($setup['columnas'] as $columna2 => $datos2)
					$condicion = str_ireplace('__'.$columna2.'__', '\''.$row[$columna2].'\'', $condicion);
				
				if(eval('return '.$condicion.';')){
					$totalizador[$columna] += $row[$columna];
				}

			}else
				$totalizador[$columna] += $row[$columna];
			
		//SubTotalizamos: Recorro el array de subTotalizacion buscando en el ROW el valor y se lo sumo
		foreach($setup['subTotalizar'] as $columna)
			if(count($setup['corte']) === 1)
				$subTotalizador[$row[$setup['corte'][0]]][$columna] += $row[$columna];
			else if(count($setup['corte']) === 2)
				$subTotalizador[$row[$setup['corte'][0]]][$row[$setup['corte'][1]]][$columna] += $row[$columna];

		//Anchos minimos de columnas NO totalizadas
		if($setup['ajustarAnchos']){
			foreach($setup['columnas'] as $columna => $datos){
				if(!array_key_exists($columna, $totalizador)){
					$anchoCelda = RPDF_CalcularAnchoCelda($row[$columna], $datos['tipo']);
					if($datos['ancho'] < $anchoCelda)
						$setup['columnas'][$columna]['ancho'] = $anchoCelda;
				}
			}
		}
	}

	//Anchos minimos SOLAMENTE de columnas totalizadas (porque siempre su tamaño va a ser mayor, y segun el tipo)
	if($setup['ajustarAnchos']){
		foreach($totalizador as $columna => $valor){
			$anchoCelda = $setup['columnas'][$columna]['porcentaje'] ? 10 : 0; //Si se pide el porcentaje, hay que sumarle 10 al ancho
			$anchoCelda += RPDF_CalcularAnchoCelda(RPDF_Mostrar_Dato($valor, $setup['columnas'][$columna]['tipo']), $setup['columnas'][$columna]['tipo']);
			if($setup['columnas'][$columna]['ancho'] < $anchoCelda)
				$setup['columnas'][$columna]['ancho'] = $anchoCelda;
		}
	}
	
	//Buscamos posibles columnas para expandir y mejoramos la estetica
	if($setup['ajustarAnchos']){
		$anchoTabla = 0;
		foreach($setup['columnas'] as $columna => $datos)
			$anchoTabla += $datos['ancho'];

		if($anchoTabla <= $pdf->AnchoContenido){
			//Calculamos el espacio que queda restante, así vemos si dá o no dá para aprovecharlo
			$anchoRestante = $pdf->AnchoContenido - $anchoTabla;

			if($anchoRestante <= 30){ //Resta una cantidad no muy grande
				//Buscamos una columna que sea relativamente grande, y se lo metemos a esa
				foreach($setup['columnas'] as $columna => $datos){
					if($datos['ancho'] >= 80){
						$setup['columnas'][$columna]['ancho'] += $anchoRestante;
						$anchoRestante = 0;
						break;
					}
				}
			}else{
				//Vemos cuanto ancho le debería ir a cada columna
				$anchoParaCadaColumna = (int)($anchoRestante / count($setup['columnas']));
				//Si es menor o igual a 10, se lo seteamos a cada una y restamos de lo restante
				if($anchoParaCadaColumna <= 10){
					foreach($setup['columnas'] as $columna => $datos)
						$setup['columnas'][$columna]['ancho'] += $anchoParaCadaColumna;
					$anchoRestante = 0;
				}else{ //sino, le sumamos solamente 10 a cada uno, y le restamos ese total (segun cantidad de columnas) al anchoRestante.
					foreach($setup['columnas'] as $columna => $datos)
						$setup['columnas'][$columna]['ancho'] += 10;
					$anchoRestante = $anchoRestante - (count($setup['columnas']) * 10);
				}
			}
		}else{
			RPDF_ReportarError($pdf, 'LA TABLA ES MAS GRANDE QUE LA HOJA');
			RPDF_Salir($pdf);
		}
	}

	$pdf->AnchoTabla = 0;
	foreach($setup['columnas'] as $columna => $datos)
		$pdf->AnchoTabla += $datos['ancho'];
	
	/* CALCULAMOS LOS DATOS PARA EL MARGEN Y EL ANCHO DE LAS COLUMNAS PADRES */
	foreach($setup['columnasPadres'] as $padre => $hijos){
		$margenEncontrado = false;
		foreach($setup['columnas'] as $columna => $dato)
			if(in_array($columna, $hijos)){
				$setup['columnasPadres'][$padre]['ancho'] += $setup['columnas'][$columna]['ancho'];
				$margenEncontrado = true;
			}else if(!$margenEncontrado){
				$setup['columnasPadres'][$padre]['x'] += $setup['columnas'][$columna]['ancho'];
			}
	}

	$pdf->CeldaMargenIzquierdo = $setup['todoCentrado'] ? (($pdf->AnchoContenido - $pdf->AnchoTabla) / 2) : 0;
	
	/**
		GENERAMOS EL PDF PROPIAMENTE DICHO
	*/
	$i = 0;
	$ultimoTextoDeCorte[0] = false;
	$ultimoTextoDeCorte[1] = false;
	
	while($row = $rows[$i]){//No hay que hacer ForEach, porque pierdo el puntero de memoria
		//Veo cuando saltar de pagina, y salto
		//Si va todo normal, y estoy por mostrar un ROW pero llegué al final (no tengo los 5 minimos de espacio)
		if($pdf->GetY() > ($pdf->AltoMaximo - 5))
			$pdf->AddPage();
		
		//Si hay que mostrar un sub total, y hay que mostrarlo en el proximo ROW (si es que hay proximo ROW), pero va a quedar solo en la proxima pagina
		if(count($setup['subTotalizar']) > 0){
			$nextRow = RPDF_ProximoRow($rows, $i);
			// if($nextRow !== false){
				// $restanteMinimo = 0;
				// if(count($setup['corte']) === 2 && $row[$setup['corte'][1]] != $nextRow[$setup['corte'][1]])
					// $restanteMinimo = 30; //15 del chico, del espacio, y del grande, y 5 de 1 row
				// else if(count($setup['corte']) === 1 && $row[$setup['corte'][0]] != $nextRow[$setup['corte'][0]])
					// $restanteMinimo = 15; //5 del chico y 5 del row
				$restanteMinimo = 5; //5 del row
				if(count($setup['corte']) > 1 && ($row[$setup['corte'][1]] != $nextRow[$setup['corte'][1]]))
					$restanteMinimo += 15; //5 del espacio, 5 del string y 5 del dato
				if(count($setup['corte']) > 0 && ($row[$setup['corte'][0]] != $nextRow[$setup['corte'][0]]))
					$restanteMinimo += 10; //5 del string y 5 del dato
				
				if($pdf->GetY() > ($pdf->AltoMaximo - $restanteMinimo))
					$pdf->AddPage();
			// }
		}
		
		//Mostramos el subtotal si cambió y no es el 1º
		if(count($subTotalizador) > 0){
			if(count($setup['corte']) > 1 && ($ultimoTextoDeCorte[1] !== false && $ultimoTextoDeCorte[1] != $row[$setup['corte'][1]])){
				RPDF_Mostrar_SubTotalChico($pdf, $setup, $subTotalizador, $ultimoTextoDeCorte[0], $ultimoTextoDeCorte[1], $totalizador);
				$pdf->Ln(5);
			}
			if(count($setup['corte']) > 0 && ($ultimoTextoDeCorte[0] !== false && $ultimoTextoDeCorte[0] != $row[$setup['corte'][0]])){
				RPDF_Mostrar_SubTotalGrande($pdf, $setup, $subTotalizador, $ultimoTextoDeCorte[0], $totalizador);
				$pdf->Ln(5);
			}
		}
		
		//Mostramos el Corte Mas Grande
		if(count($setup['corte']) > 0 && $ultimoTextoDeCorte[0] != $row[$setup['corte'][0]]){
			//altoCabecera:
				// 5 = Un "Enter"
				//[5]= Corte Grande
				//[5]= Corte Chico
				//[5]= Columnas Padres
				// 5 = Cabecera
				// 5 = 1 ROW minimo
			$restanteMinimo = 0;
			//SI va el Corte Grande
			if(count($setup['corte']) > 0){
				$restanteMinimo += 5;
				//SI va el Corte Chico
				if(count($setup['corte']) > 1)
					$restanteMinimo += 5;
			}
			//SI Columnas Padres
			if(count($setup['columnasPadres']) > 0)
				$restanteMinimo +=5;
			
			$restanteMinimo += 15; //El minimo (El "Enter", Cabecera y el Row Minimo)

			if($pdf->GetY() > ($pdf->AltoMaximo - $restanteMinimo))
				$pdf->AddPage();

			//Muestro el valor del texto de corte.. Como cabecera de cabecera.
			$pdf->SetFont('Arial', 'B', 10);
			RPDF_AcomodarMargenIzquierdo($pdf, $setup['todoCentrado']);
			$pdf->SetFillColor(160);
			//El texto
			$pdf->Cell($pdf->AnchoTabla - 30, 5, RPDF_Mostrar_Dato(str_replace('_', ' ', $setup['corte'][0]).':', 'texto').RPDF_Mostrar_Dato($row[$setup['corte'][0]], $setup['columnas'][$setup['corte'][0]]['tipo']), 1, 0, 'L', 1);
			//La cantidad
			if(count($setup['corte']) === 1)
				$pdf->Cell(30, 5, $setup['contarCorte'] == true ? 'Cantidad: '.$contadorCorte[$row[$setup['corte'][0]]] : '', 1, 1, 'R', 1);
			else if(count($setup['corte']) === 2){
				$sumatoria = 0;
				foreach($contadorCorte[$row[$setup['corte'][0]]] as $cantidad){
					$sumatoria += $cantidad;
				}
				$pdf->Cell(30, 5, $setup['contarCorte'] == true ? 'Cantidad: '.$sumatoria : '', 1, 1, 'R', 1);
			}
			$pdf->SetFont('Arial', '', 10);
		}
		
		//Mostramos el Corte Mas Chico (Si es que hay que mostrarlo)
		if(count($setup['corte']) > 1 && $ultimoTextoDeCorte[1] != $row[$setup['corte'][1]]){
			//Muestro el valor del texto de corte.. Como cabecera de cabecera.
			$pdf->SetFont('Arial', 'B', 10);
			RPDF_AcomodarMargenIzquierdo($pdf, $setup['todoCentrado']);
			$pdf->SetX($pdf->GetX() + 10);
			$pdf->SetFillColor(200);
			//El texto
			$pdf->Cell($pdf->AnchoTabla - 40, 5, RPDF_Mostrar_Dato(str_replace('_', ' ', $setup['corte'][1]).':', 'texto').RPDF_Mostrar_Dato($row[$setup['corte'][1]], $setup['columnas'][$setup['corte'][1]]['tipo']), 1, 0, 'L', 1);
			//La cantidad
			$pdf->Cell(30, 5, $setup['contarCorte'] == true ? 'Cantidad: '.$contadorCorte[$row[$setup['corte'][0]]][$row[$setup['corte'][1]]] : '', 1, 1, 'R', 1);
			$pdf->SetFont('Arial', '', 10);
		}

		$pdf->SetFillColor(240);
		
		//Mostramos cabeceras
		if(count($setup['corte']) > 1){
			if(($ultimoTextoDeCorte[1] != $row[$setup['corte'][1]]) || ($ultimoTextoDeCorte[0] != $row[$setup['corte'][0]]))
				RPDF_ColocarCabeceraReporte($pdf, $setup);
		}else if(count($setup['corte']) > 0){
			if($ultimoTextoDeCorte[0] != $row[$setup['corte'][0]]){
				RPDF_ColocarCabeceraReporte($pdf, $setup);
			}
		}
		
		//Muestro la cabecera en caso que sea una nueva pagina
		if($pdf->GetY() == $pdf->AltoDeInicio)
			RPDF_ColocarCabeceraReporte($pdf, $setup);
		
		RPDF_AcomodarMargenIzquierdo($pdf, $setup['todoCentrado']);
		
		/**
		[MUESTRO EL REGISTRO]
		Muestro cada CELDA del ROW: Recorro las columnas, busco el dato en el ROW, y muestro el CELL
		*/
		foreach($setup['columnas'] as $columna => $datos){
			RPDF_Colorear($pdf, $setup, $row[$columna]);
			$pdf->Cell($datos['ancho'], 5, RPDF_Mostrar_Dato($row[$columna], $datos['tipo']), 0, 0, $datos['alineado']);
		}
		$pdf->SetTextColor(0,0,0);

		$pdf->Ln(0);
		RPDF_AcomodarMargenIzquierdo($pdf, $setup['todoCentrado']);
		RPDF_Linea($pdf);
		
		$ultimoTextoDeCorte[0] = $row[$setup['corte'][0]];
		if(count($setup['corte']) > 1)
			$ultimoTextoDeCorte[1] = $row[$setup['corte'][1]];
		
		//Salto de Linea para que el proximo ROW quede bien
		$pdf->Ln(5);
		$i++;
	}

	if(count($subTotalizador) > 0){
		if(count($setup['corte']) > 1){
			RPDF_Mostrar_SubTotalChico($pdf, $setup, $subTotalizador, $ultimoTextoDeCorte[0], $ultimoTextoDeCorte[1], $totalizador);
			$pdf->Ln(5);
		}
		RPDF_Mostrar_SubTotalGrande($pdf, $setup, $subTotalizador, $ultimoTextoDeCorte[0], $totalizador);
	}
	
	//Mostramos Totales
	if(count($totalizador) > 0){
		$pdf->Ln(5);
		RPDF_Mostrar_Totales($pdf, $setup, $totalizador);
	}

	if($setup['contarTotal']){
		RPDF_AcomodarMargenIzquierdo($pdf, $setup['todoCentrado']);
		$pdf->Cell($pdf->AnchoTabla, 5, 'Cantidad Total: '.count($rows), 1, 1, 'C', 1);
	}

	if(count($setup['pie']) > 0){
		foreach($setup['pie'] as $pie){
			$pdf->Ln(5);

			foreach($totalizador as $columna => $valor)
				$pie = str_ireplace('__'.$columna.'__', $valor, $pie);

			$reglasEncontradas = null;
			$pregResu = preg_match_all("/#=(.*)=#/i", $pie, $reglasEncontradas, PREG_SET_ORDER);
			if($pregResu === 1){
				//Reemplazamos la 1º Y UNICA REGLA (se supone que hay 1 sola)
				$valor = eval('return '.$reglasEncontradas[0][1].';');
				$valor = is_float($valor) || is_double($valor) ? number_format($valor, 2, ',', '.') : (
						 						is_int($valor) ? number_format($valor, 0, ',', '.') : $valor);
				$pie = str_ireplace($reglasEncontradas[0][0], $valor, $pie);

				RPDF_AcomodarMargenIzquierdo($pdf, $setup['todoCentrado']);
				$pdf->MultiCell($pdf->AnchoTabla, 5, $pie, 1, 'J', 1);

			}elseif($pregResu === false){
				RPDF_ReportarError($pdf, 'Error al evaluar las reglas del pie: '.preg_last_error());
				RPDF_Salir($pdf);
			}else{
				RPDF_AcomodarMargenIzquierdo($pdf, $setup['todoCentrado']);
				$pdf->MultiCell($pdf->AnchoTabla, 5, $pie, 1, 'J', 1);
			}
		}	
	}

	RPDF_Salir($pdf);
}

function RPDF_ColocarCabeceraReporte($pdf, $setup){
	$pdf->SetFont('Times', 'B', 8);
	$pdf->SetFillColor(240);
	$xInicial = $pdf->GetX();
	// Cabeceras "Padres"
	foreach($setup['columnasPadres'] as $padre => $datos){
		$pdf->SetX($setup['todoCentrado'] ? $xInicial + $datos['x'] + $pdf->CeldaMargenIzquierdo : $xInicial + $datos['x']);
		$pdf->Cell($datos['ancho'], 5, str_replace('_', ' ', $padre), 1, 0, 'C', 1);
	}
	
	$pdf->Ln(5);
	
	// Cabeceras "Hijas"
	RPDF_AcomodarMargenIzquierdo($pdf, $setup['todoCentrado']);
	
	$i = 0;
	foreach($setup['columnas'] as $columna => $datos){
		$pdf->Cell($datos['ancho'], 5, str_replace('_', ' ', $columna), 1, 0, 'C', 1);
		$i++;
	}
	$pdf->SetFont('Arial', '', 10);
	$pdf->Ln(5);
}

function RPDF_AcomodarMargenIzquierdo($pdf, $centrar){
	if($centrar){
		$x = $pdf->GetX();
		$pdf->SetX($x + $pdf->CeldaMargenIzquierdo);
	}
}

function RPDF_ReportarError($pdf, $texto){
	$pdf->Ln(5);
	$pdf->SetFont('Arial', 'B', 10);
	$pdf->SetFillColor(255, 0, 0);
	$pdf->Cell(0, 5, $texto, 1, 1, 'C', 1);
	$pdf->SetFont('Arial', '', 10);
	$pdf->SetFillColor(240);
}

function RPDF_Linea($pdf, $setup){
	$y = $pdf->GetY();
	$x = $pdf->GetX();
	if($setup['todoCentrado'])
		$x += $pdf->CeldaMargenIzquierdo;
	
	if($pdf->CurOrientation == 'P')
		$pdf->Line($x, $y, $x + $pdf->AnchoTabla, $y);
	else
		$pdf->Line($x, $y, $x + $pdf->AnchoTabla, $y);
}

function RPDF_Mostrar_Dato($valor, $tipo){
	$valor = strtoupper($valor);
	return 	$tipo == 'moneda'		? '$'.number_format($valor, 2, ',', '.') : (
			$tipo == 'miles'		? number_format($valor, 0, '', '.') : (
			$tipo == 'porcentaje'	? number_format($valor, 2, ',', '.').'%' : (
			$tipo == 'decimales'	? number_format($valor, 2, ',', '.') : $valor)));
}

function RPDF_Colorear($pdf, $setup, $valor){
	if($setup['colorear']){
		if(is_numeric($valor) && $valor < 0)
			$pdf->SetTextColor(150,0,0);
		else
			$pdf->SetTextColor(0,0,0);
	}
}

function RPDF_Mostrar_Totales($pdf, $setup, $totalizador){
	if($pdf->GetY() > ($pdf->AltoMaximo - 10))
		$pdf->AddPage();

	$pdf->SetFont('Arial', 'B', 10);
	$pdf->SetFillColor(200);
	//Mostramos los totales
	$anchoCeldaLabelTotal = 0;
	
	RPDF_AcomodarMargenIzquierdo($pdf, $setup['todoCentrado']);
	$textoTotal = $setup['textoTotal'] != NULL ? $setup['textoTotal'] : 'Total Gral.:';
	$pdf->Cell($pdf->AnchoTabla, 5, $textoTotal, 0, 1, 'L');
	RPDF_AcomodarMargenIzquierdo($pdf, $setup['todoCentrado']);

	foreach($setup['columnas'] as $columna => $datos){
		if(array_key_exists($columna, $totalizador)){ //TODO Esto me parece que se resume en: if($datos['totalizar'])
			if($anchoCeldaLabelTotal > 0)
				$pdf->Cell($anchoCeldaLabelTotal, 5, '', 0, 0, 'R');
			
			RPDF_Colorear($pdf, $setup, $totalizador[$columna]);
			$pdf->Cell($datos['ancho'], 5, RPDF_Mostrar_Dato($totalizador[$columna], $datos['tipo']), 1, 0, $datos['alineado'], 1);
			$anchoCeldaLabelTotal = 0;
		}else{
			$anchoCeldaLabelTotal += (int)$datos['ancho'];
		}
	}
	
	$pdf->Ln(5);
	$pdf->SetFont('Arial', '', 10);
	$pdf->SetFillColor(240);
	$pdf->SetTextColor(0,0,0);
}

function RPDF_Mostrar_SubTotalGrande($pdf, $setup, $subTotalizador, $ultimoTextoDeCorteGrande, $totalizador){
	$pdf->SetFont('Arial', 'B', 9);
	$pdf->SetFillColor(150);
	//Mostramos los totales
	$anchoCeldaLabelTotal = 0;
	$sumatoria = array();
	
	if(count($setup['corte']) === 2)
		foreach($subTotalizador[$ultimoTextoDeCorteGrande] as $itemSubTotalizador)
			foreach($setup['subTotalizar'] as $columna)
				$sumatoria[$columna] += $itemSubTotalizador[$columna];
	else if(count($setup['corte']) === 1)
		$sumatoria = $subTotalizador[$ultimoTextoDeCorteGrande];

	RPDF_AcomodarMargenIzquierdo($pdf, $setup['todoCentrado']);
	$textoSubTotal = $setup['textoSubTotal'] != NULL ? str_replace('$', $ultimoTextoDeCorteGrande, $setup['textoSubTotal']) : 'Sub Total de '.$ultimoTextoDeCorteGrande.':';
	$pdf->Cell(0, 5, $textoSubTotal, 0, 1, 'L');
	RPDF_AcomodarMargenIzquierdo($pdf, $setup['todoCentrado']);
	
	foreach($setup['columnas'] as $columna => $datos){
		if(array_key_exists($columna, $sumatoria)){
			if($anchoCeldaLabelTotal > 0)
				$pdf->Cell($anchoCeldaLabelTotal, 5, '', 0, 0, 'R');
			
			//Si pide el porcentaje
			$porcentaje = $datos['porcentaje'] === true ? '('.round((($sumatoria[$columna] / $totalizador[$columna]) * 100), 1).'%) ' : '';
			RPDF_Colorear($pdf, $setup, $sumatoria[$columna]);
			$pdf->Cell($datos['ancho'], 5, $porcentaje.RPDF_Mostrar_Dato($sumatoria[$columna], $datos['tipo']), 1, 0, $datos['alineado'], 1);
			$anchoCeldaLabelTotal = 0;
		}else{
			$anchoCeldaLabelTotal += (int)$datos['ancho'];
		}
	}
	
	$pdf->Ln(5);
	$pdf->SetFont('Arial', '', 10);
	$pdf->SetFillColor(240);
	$pdf->SetTextColor(0,0,0);
}

function RPDF_Mostrar_SubTotalChico($pdf, $setup, $subTotalizador, $ultimoTextoDeCorteGrande, $ultimoTextoDeCorteChico, $totalizador){
	$pdf->SetFont('Arial', 'B', 9);
	$pdf->SetFillColor(200);
	//Mostramos los totales
	$anchoCeldaLabelTotal = 0;

	RPDF_AcomodarMargenIzquierdo($pdf, $setup['todoCentrado']);
	$textoSubTotal = $setup['textoSubTotal'] != NULL ? str_replace('$', $ultimoTextoDeCorteChico, $setup['textoSubTotal']) : 'Sub Total de '.$ultimoTextoDeCorteChico.':';
	$pdf->Cell($anchoCeldaLabelTotal, 5, $textoSubTotal, 0, 1, 'L');
	RPDF_AcomodarMargenIzquierdo($pdf, $setup['todoCentrado']);

	foreach($setup['columnas'] as $columna => $datos){
		if(array_key_exists($columna, $subTotalizador[$ultimoTextoDeCorteGrande][$ultimoTextoDeCorteChico])){
			if($anchoCeldaLabelTotal > 0)
				$pdf->Cell($anchoCeldaLabelTotal, 5, '', 0, 0, 'R');
			
			$porcentaje = $datos['porcentaje'] === true ? '('.round((($subTotalizador[$ultimoTextoDeCorteGrande][$ultimoTextoDeCorteChico][$columna] / $totalizador[$columna]) * 100), 1).'%) ' : '';
			RPDF_Colorear($pdf, $setup, $subTotalizador[$ultimoTextoDeCorteGrande][$ultimoTextoDeCorteChico][$columna]);
			$pdf->Cell($datos['ancho'], 5, $porcentaje.RPDF_Mostrar_Dato($subTotalizador[$ultimoTextoDeCorteGrande][$ultimoTextoDeCorteChico][$columna], $datos['tipo']), 1, 0, $datos['alineado'], 1);
			$anchoCeldaLabelTotal = 0;
		}else{
			$anchoCeldaLabelTotal += (int)$datos['ancho'];
		}
	}
	
	$pdf->Ln(5);
	$pdf->SetFont('Arial', '', 10);
	$pdf->SetFillColor(240);
	$pdf->SetTextColor(0,0,0);
}

function RPDF_CalcularAnchoCelda($texto, $tipo){
	$strLen = (int)strlen($texto);
	if($tipo == 'moneda')	
		return ($strLen * 2);// + (int)($strLen / 40);
	if($tipo == 'miles')
		return ($strLen * 2) + (int)($strLen / 2);// + (int)($strLen / 40);
	if($tipo == 'decimales')
		return 1 + ($strLen * 2);// + (int)($strLen / 40);
	else
		return 2 + ($strLen * 2) + (int)($strLen / 2);
}

//TODO Funcionalidad no definida
function RPDF_Codebar($xpos, $ypos, $code, $start='A', $end='A', $basewidth=0.35, $height=16) {
    $barChar = array (
        '0' => array (6.5, 10.4, 6.5, 10.4, 6.5, 24.3, 17.9),
        '1' => array (6.5, 10.4, 6.5, 10.4, 17.9, 24.3, 6.5),
        '2' => array (6.5, 10.0, 6.5, 24.4, 6.5, 10.0, 18.6),
        '3' => array (17.9, 24.3, 6.5, 10.4, 6.5, 10.4, 6.5),
        '4' => array (6.5, 10.4, 17.9, 10.4, 6.5, 24.3, 6.5),
        '5' => array (17.9,    10.4, 6.5, 10.4, 6.5, 24.3, 6.5),
        '6' => array (6.5, 24.3, 6.5, 10.4, 6.5, 10.4, 17.9),
        '7' => array (6.5, 24.3, 6.5, 10.4, 17.9, 10.4, 6.5),
        '8' => array (6.5, 24.3, 17.9, 10.4, 6.5, 10.4, 6.5),
        '9' => array (18.6, 10.0, 6.5, 24.4, 6.5, 10.0, 6.5),
        '$' => array (6.5, 10.0, 18.6, 24.4, 6.5, 10.0, 6.5),
        '-' => array (6.5, 10.0, 6.5, 24.4, 18.6, 10.0, 6.5),
        ':' => array (16.7, 9.3, 6.5, 9.3, 16.7, 9.3, 14.7),
        '/' => array (14.7, 9.3, 16.7, 9.3, 6.5, 9.3, 16.7),
        '.' => array (13.6, 10.1, 14.9, 10.1, 17.2, 10.1, 6.5),
        '+' => array (6.5, 10.1, 17.2, 10.1, 14.9, 10.1, 13.6),
        'A' => array (6.5, 8.0, 19.6, 19.4, 6.5, 16.1, 6.5),
        'T' => array (6.5, 8.0, 19.6, 19.4, 6.5, 16.1, 6.5),
        'B' => array (6.5, 16.1, 6.5, 19.4, 6.5, 8.0, 19.6),
        'N' => array (6.5, 16.1, 6.5, 19.4, 6.5, 8.0, 19.6),
        'C' => array (6.5, 8.0, 6.5, 19.4, 6.5, 16.1, 19.6),
        '*' => array (6.5, 8.0, 6.5, 19.4, 6.5, 16.1, 19.6),
        'D' => array (6.5, 8.0, 6.5, 19.4, 19.6, 16.1, 6.5),
        'E' => array (6.5, 8.0, 6.5, 19.4, 19.6, 16.1, 6.5),
    );
    $this->SetFont('Arial','',13);
    $this->Text($xpos, $ypos + $height + 4, $code);
    $this->SetFillColor(0);
    $code = strtoupper($start.$code.$end);
    for($i=0; $i<strlen($code); $i++){
        $char = $code[$i];
        if(!isset($barChar[$char])){
            $this->Error('Invalid character in barcode: '.$char);
        }
        $seq = $barChar[$char];
        for($bar=0; $bar<7; $bar++){
            $lineWidth = $basewidth*$seq[$bar]/6.5;
            if($bar % 2 == 0){
                $this->Rect($xpos, $ypos, $lineWidth, $height, 'F');
            }
            $xpos += $lineWidth;
        }
        $xpos += $basewidth*10.4/6.5;
    }
}

function RPDF_Salir($pdf){
	$pdf->Output();
	exit;
}

/* SOPORTE */
function RPDF_EjecutarSQL($sql, $variables, $limit = false){
	global $dbRPDF;

	try{
		$sqlFinal = $limit === false ? $sql : 'SELECT * FROM ('.$sql.') WHERE ROWNUM <= '.(int)$limit;
		return $dbRPDF->Execute($sqlFinal, $variables);
	}catch(Exception $e){
		echo "Variables:\n<br>";
		var_dump($variables);
		echo "\n<br>";
		echo 'ERROR: RPDF_EjecutarSQL() al ejecutar SQL: '.$dbRPDF->ErrorMsg()."\n<br>$sql";
		exit;
	}
}

function RPDF_EvaluarRecurso($recurso, $variables, $envianRes, $limit = false){
	return $res = !$envianRes ? RPDF_EjecutarSQL($recurso, $variables, $limit) : $recurso;
}

function RPDF_ProximoRow($rows, $i){
	try{
		return ($i == count($rows) - 1) ? false : $rows[$i + 1];
	}catch(exception $e){
		die('ERROR "RPDF_ProximoRow()": Algo se rompió. Funcion del Framework');
	}
	
}

function RPDF_GetArrayForSelect($row){ //Para acaparar los SQL que devuelven 1 sola columna, que es tanto el VALUE como el TEXT del select. Y tambien los que traen 2 columnas (es lo normal)
	$rowArray = RPDF_RowToArray($row);
	$valueYtext = array();
	$valueYtext[] = $valueYtext['value'] = $rowArray[0];
	$valueYtext[] = $valueYtext['text'] = count($rowArray) == 1  ? $valueYtext[0] : $valueYtext[1];
	return $valueYtext;
}

function RPDF_RowToArray($row){ //Esto se hace porque un ROW no puede ser accedido diractamente como array
	$array = array();
	foreach($row as $valor){
		$array[] = $valor;
	}
	return $array;
}

// function RPDF_ValidarCondicion($valor1, condicion, $valor2){
// 	if(!preg_match('/[==|!=|>|>=|<|<=]/i', $recurso)){
// 		RPDF_ReportarError($pdf, 'No se puede reconocer la condicion "'.$condicion.'"');
// 		RPDF_Salir($pdf);
// 	}
// 	return eval($valor1.$condicion.$valor2);
// }