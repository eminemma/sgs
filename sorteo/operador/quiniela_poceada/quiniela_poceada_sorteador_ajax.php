<?php
@session_start();
include_once dirname(__FILE__) . '/../../../db.php';
include_once dirname(__FILE__) . '/../../../librerias/alambre/funcion.inc.php';

$accion  = isset($_POST['accion']) ? $_POST['accion'] : '';
$validar = isset($_POST['validar']) ? $_POST['validar'] : '';
$juego   = isset($_POST['juego']) ? $_POST['juego'] : '';

/**
Devolver la configuracion de la Loteria
 */

if ($accion == 'configuracion' && $juego == 'primer_juego') {
    conectar_db();
    $sorteo   = $_SESSION['sorteo'];
    $id_juego = $_SESSION['id_juego'];

    $loteria   = array();
    $rs_sorteo = sql('SELECT  PRIMER_ELEMENTO AS billete_inicial, ULTIMO_ELEMENTO AS billete_final,
							1 as fraccion_inicial,fracciones as fraccion_final
					FROM sgs.T_SORTEO
					WHERE SORTEO 	 = ?
						AND ID_JUEGO 	 = ? ', array($sorteo, $id_juego));

    $row_sorteo = siguiente($rs_sorteo);

    $loteria['min_billete']                     = $row_sorteo->BILLETE_INICIAL;
    $loteria['max_billete']                     = $row_sorteo->BILLETE_FINAL;
    $loteria['cantidad_premios_tradicional']    = 0;
    $loteria['cantidad_premios_extraordinario'] = 0;

    $extracciones = array();
    for ($i=1; $i <=20 ; $i++) { 

        $rs = sql("SELECT COUNT(ID_EXTRACCION) AS CANTIDAD FROM SGS.T_EXTRACCION TE
                         WHERE SORTEO=?
                         AND ID_JUEGO=?
                         AND POSICION = ?

                         AND (SORTEO_ASOC LIKE ('%QUINIELA ASOCIADA%') OR SORTEO_ASOC LIKE ('%VALIDA%'))", array($sorteo, $id_juego,$i));
        $row = siguiente($rs);
        $sorteado = false;
        if($row->CANTIDAD > 0){
            $sorteado = true;
        }
        $extracciones[] = array('POSICION' => $i,'DESCRIPCION' => 'EXTRACCION Nº'.$i, 'SORTEADO' => $sorteado);
    }
    $loteria['premios'] = array();
    foreach ($extracciones as $extraccion) {        
        $premio                 = array();
        $premio['posicion']     = $extraccion['POSICION'];
        $premio['descripcion']  = ucfirst($extraccion['DESCRIPCION']);
        $premio['sorteado']     = $extraccion['SORTEADO'];
        $premio['afecta']     = 'entero';
        array_push($loteria['premios'], $premio);
    }
    header('Content-Type: application/json');
    echo json_encode($loteria);
    exit;
}


/**
Controlas si esa posicion ya esta cargada, o el entero con fraccion si corresponde
 */
if ($accion == 'control_ingreso') {
    conectar_db();
    ComenzarTransaccion($db);
    $afecta       = isset($_POST['afecta']) ? $_POST['afecta'] : '';
    $siempre_sale = isset($_POST['siempre_sale']) ? $_POST['siempre_sale'] : '';
    $entero       = isset($_POST['entero']) ? $_POST['entero'] : '';
    $posicion     = isset($_POST['posicion']) ? $_POST['posicion'] : '';
    $fraccion     = isset($_POST['fraccion']) ? $_POST['fraccion'] : '';
    $juego        = isset($_POST['juego']) ? $_POST['juego'] : '';
    $id_juego     = $_SESSION['id_juego'];
    $sorteo       = $_SESSION['sorteo'];
    $usuario      = $_SESSION['dni'];

    //Segun que afecta la consulta de busqueda cambia
    $variables           = array();
    $busqueda_extraccion = "	SELECT COUNT(*) as CANTIDAD
								FROM sgs.T_EXTRACCION
								WHERE POSICION=?
									AND ID_JUEGO=?
									AND SORTEO=?
                                    AND (SORTEO_ASOC LIKE ('%QUINIELA ASOCIADA%') OR SORTEO_ASOC LIKE ('%VALIDA%'))";
    array_push($variables, $posicion, $id_juego, $sorteo);

    //Controlo si ya esta pasado como extraccion
    try {
        $rs_numero = sql($busqueda_extraccion, $variables);

    } catch (exception $e) {
        $mensaje = array("mensaje" => "Error al insertar: " . $db->ErrorMsg(), "tipo" => "error");
    }

    $row_numeros_sorteado = siguiente($rs_numero);

    if ($row_numeros_sorteado->CANTIDAD == 0) {
        //$db->debug=true;
        //Control de ingresos de los operadores
        if ($juego == 'primer_juego') {
            $juego = 1;
        } 

     

        try {

            $rs_control = sql("		SELECT NUMERO,POSICION,FRACCION
									FROM SGS.TEMP_CTRL_INGRESO_NUMERO
									WHERE ZONA_JUEGO=?
										AND trim(ID_USUARIO)!=?
										AND ID_JUEGO=?
										and posicion=?", array($juego, 'DU' . $usuario, $id_juego, $posicion));
            //$mensaje = 'OK';
        } catch (exception $e) {
            $mensaje = array("mensaje" => "Error " . $db->ErrorMsg(), "tipo" => "error");
        }

        $existe_bola     = false;
        $existe_posicion = false;
        if ($rs_control->RowCount() > 0) {
            $existe_posicion = true;
        }

        while ($bolas_ingresadas = siguiente($rs_control)) {
            if ($bolas_ingresadas->NUMERO == $entero && empty($fraccion)) {
                $existe_bola = true;
                break;
            } else if ($bolas_ingresadas->NUMERO == $entero && $bolas_ingresadas->FRACCION == $fraccion) {
                $existe_bola = true;
                break;
            }
        }

         try {
            $existe_extraccion = false;
            $existe_extraccion_posicion = null;
            $rs = sql("   SELECT COUNT(*) AS EXTRACCION,POSICION 
                                    FROM sgs.T_EXTRACCION
                                    WHERE ID_JUEGO=?
                                        AND SORTEO=?
                                        AND ZONA_JUEGO=?
                                        AND numero = ?
                                        AND (SORTEO_ASOC LIKE ('%QUINIELA ASOCIADA%') OR SORTEO_ASOC LIKE ('%VALIDA%'))
                                         GROUP BY POSICION", array($id_juego,$sorteo,1, $entero));
            if($row = siguiente($rs)){
                if($row->EXTRACCION>0){
                    $existe_extraccion = true;
                    $existe_extraccion_posicion = $row->POSICION;
                }
            }
            //$mensaje = 'OK';
        } catch (exception $e) {
            $mensaje = array("mensaje" => "Error " . $db->ErrorMsg(), "tipo" => "error");
        }

        try {

            $tipo_sorteo  = 'T';
            $fraccion     = null;
            $usuario      = 'DU' . $_SESSION['dni'];
            $siempre_sale = '0';

          //  $db->debug=true;

            $cantidadGanadores = 0;
            $stmt              = $db->PrepareSP("BEGIN SGS.PR_INGRESAR_NUMERO(:a1, :a2, :a3, :a4, :a5, :a6, :a7, :a8,:a9,:a10); END;");
            $db->InParameter($stmt, $juego, 'a1');
            $db->InParameter($stmt, $entero, 'a2');
            $db->InParameter($stmt, $id_juego, 'a3');
            $db->InParameter($stmt, $sorteo, 'a4');
            $db->InParameter($stmt, $posicion, 'a5');
            $db->InParameter($stmt, $fraccion, 'a6');
            $db->InParameter($stmt, $tipo_sorteo, 'a7');
            $db->InParameter($stmt, $siempre_sale, 'a8');
            $db->InParameter($stmt, $usuario, 'a9');
            $db->OutParameter($stmt, $cantidadGanadores, 'a10');
            $ok = $db->Execute($stmt);

            //sql('COMMIT');
            $valida = false;
            if ($siempre_sale == '0') {
                //    No afecta a siempre sale (JUEGO 2)
                if (!$ok) {
                    $mensaje = array("mensaje" => "Error al insertar: " . $db->ErrorMsg(), "tipo" => "error");
                } else {
                    if($existe_extraccion){
                         $valida = true;
                    }
                    if (!$existe_posicion) {
                        if($existe_extraccion){
                            $mensaje = array("mensaje" => "Se cargo Correctamente, la extraccion ya existe en la posicion ".$existe_extraccion_posicion, "tipo" => "success");
                        }else
                        $mensaje = array("mensaje" => "Se cargo Correctamente la Extraccion", "tipo" => "success");

                    } else if ($existe_posicion && $existe_bola) {
                        $mensaje = array("mensaje" => "Se cargo Correctamente la Extraccion, validada con el otro usuario", "tipo" => "success", "valida"=>$valida);
                        if($valida === false){
                             sql("UPDATE SGS.t_parametro_compartido
                                SET VALOR='VALIDA',ID_USUARIO=?
                                 WHERE ID_JUEGO=?
                                    AND PARAMETRO='VALIDACION'", array($_SESSION['dni'],$id_juego));
                        }
                        sql("DELETE FROM sgs.TEMP_CTRL_INGRESO_NUMERO");
                    } else if ($existe_posicion && !$existe_bola) {
                        $mensaje = array("mensaje" => "Se cargo pero sin coincidencias", "tipo" => "error");
                        //Grabo en parametros compartidos coincidencias
                        sql("UPDATE SGS.t_parametro_compartido
									SET VALOR=?,
                                        ID_USUARIO=?
								WHERE ID_JUEGO=?
					  				AND PARAMETRO='COINCIDENCIA'", array('No existio coincidencias en los numeros ingresados', $_SESSION['dni'], $id_juego));
                        //Borro al tabla de control de ingreso, sino hay coincidencias
                        sql("DELETE FROM sgs.TEMP_CTRL_INGRESO_NUMERO");
                    }
                }
            }

            $db->CommitTrans();

        } catch (exception $e) {
            $mensaje = array("mensaje" => "Error al insertar: " . $db->ErrorMsg(), "tipo" => "error");

        }
    } else {
        $mensaje = array("mensaje" => "Esta Extraccion ya se encuentra cargada como Numero Sorteado", "tipo" => "error");
    }

    FinalizarTransaccion($db);
    header('Content-Type: application/json');
    echo json_encode($mensaje);
}

/**
Control de Ganadores(SI FINALIZO LA EXTRACCIOn)
 */
if ($accion == 'control_ganador' && $juego == 'primer_juego') {
    $id_juego = $_SESSION['id_juego'];
    $sorteo   = $_SESSION['sorteo'];
    conectar_db();
    //$db->debug=true;
    try {
        $rs_extraccion_primer = sql("SELECT COUNT(*) AS EXTRACCION
							FROM sgs.T_EXTRACCION
							WHERE ID_JUEGO=?
 								AND SORTEO=?
 								AND (ZONA_JUEGO=1 OR ZONA_JUEGO=3)
                                AND (SORTEO_ASOC LIKE ('%QUINIELA ASOCIADA%') OR SORTEO_ASOC LIKE ('%VALIDA%'))", array($id_juego, $sorteo));

        $rs_extraccion_segundo = sql("	SELECT COUNT(*) AS EXTRACCION
										FROM sgs.T_EXTRACCION
										WHERE ID_JUEGO=?
			 								AND SORTEO=?
			 								AND ZONA_JUEGO=1
                                            AND (SORTEO_ASOC not LIKE ('%COINCIDE%') )", array($id_juego, $sorteo));


        $mensaje                   = array("mensaje" => "No Finalizo", "tipo" => "error");
        $cantidad_extracciones     = 0;
        $row_extraccion_primer     = siguiente($rs_extraccion_primer);
        $row_extraccion_segundo    = siguiente($rs_extraccion_segundo);
        $cantidad_extracciones     = (int) $row_extraccion_primer->EXTRACCION;

        if ($cantidad_extracciones == (int) 20) {
            $mensaje = array("mensaje" => "Finalizo", "tipo" => "error");
        }

        $rs_parametro_coincidencia = sql(" SELECT VALOR,ID_USUARIO
										FROM SGS.t_parametro_compartido TS
										WHERE ID_JUEGO=?
										  AND PARAMETRO='COINCIDENCIA'", array($id_juego));
        $row_parametro_coincidencia = siguiente($rs_parametro_coincidencia);

        if (!is_null($row_parametro_coincidencia->VALOR)) {
            $mensaje['coincidencia'] = $row_parametro_coincidencia->VALOR;
            if ($row_parametro_coincidencia->ID_USUARIO != $_SESSION['dni']) {
                sql("UPDATE SGS.t_parametro_compartido
                        SET VALOR=null,ID_USUARIO=null
                     WHERE ID_JUEGO=?
                        AND PARAMETRO='COINCIDENCIA'", array($id_juego));
            }
        }

        $rs_parametro_reincio = sql(" SELECT VALOR,ID_USUARIO
                                        FROM SGS.t_parametro_compartido TS
                                        WHERE ID_JUEGO=?
                                          AND PARAMETRO='REINICIO'", array($id_juego));
        $row_parametro_reinicio = siguiente($rs_parametro_reincio);

        if (!is_null($row_parametro_reinicio->VALOR)) {
            $mensaje['reinicio'] = $row_parametro_reinicio->VALOR;
            $mensaje['tipo']         = 'info';
            if ($row_parametro_reinicio->ID_USUARIO != $_SESSION['dni']) {
                sql("UPDATE SGS.t_parametro_compartido
                        SET VALOR=null,ID_USUARIO=null
                     WHERE ID_JUEGO=?
                        AND PARAMETRO='REINICIO'", array($id_juego));
            }
        }

        $rs_parametro_validacion = sql(" SELECT VALOR,ID_USUARIO
                                        FROM SGS.t_parametro_compartido TS
                                        WHERE ID_JUEGO=?
                                          AND PARAMETRO='VALIDACION'", array($id_juego));
        $row_parametro_validacion = siguiente($rs_parametro_validacion);

        if (!is_null($row_parametro_validacion->VALOR)) {
            $mensaje['coincidencia'] = $row_parametro_validacion->VALOR;
            $mensaje['tipo']         = 'success';
           if ($row_parametro_validacion->ID_USUARIO != $_SESSION['dni']) {
                sql("UPDATE SGS.t_parametro_compartido
                        SET VALOR=NULL,ID_USUARIO=null
                     WHERE ID_JUEGO=?
                        AND PARAMETRO='VALIDACION'", array($id_juego));
            }

        }

    } catch (exception $e) {
        $mensaje = array("mensaje" => "Error al insertar: " . $db->ErrorMsg(), "tipo" => "error");
    }

    header('Content-Type: application/json');
    echo json_encode($mensaje);
}


/**
Habilitar pantallas segun el juego (FUNCIONANDO)
 */
if ($accion == 'mostrar_extracto') {

    $tipo     = isset($_POST['tipo']) ? $_POST['tipo'] : '';
    $id_juego = $_SESSION['id_juego'];
    try {
        conectar_db();

        if ($tipo == 'ver_tradicional') {
            $juego = 1;
        } else if ($tipo == 'ver_extraordinario') {
            $juego = 2;
        } else if ($tipo == 'ver_siempre_sale') {
            $juego = 3;
        }

        if ($tipo == 'ver_siempre_sale') {
            $rs_extraccion_segundo = sql("SELECT tg.id_premio_descripcion as PREMIO,COUNT(*) as GANADOR
										FROM SGS.T_SORTEO TS,
										  	SGS.T_PROGRAMA TP,
										  	SGS.t_programa_premios tpr,
										  	SGS.t_ganadores tg,
	                    				 	sgs.t_extraccion te
										WHERE ts.SORTEO        =?
										AND TS.ID_JUEGO        =?
										AND ts.id_programa     = tp.id_programa
										AND tp.id_programa     = tpr.id_programa
										AND tpr.id_descripcion =tg.id_premio_descripcion
										AND ts.sorteo          =tg.sorteo
										AND ts.id_juego        =tg.id_juego
										AND upper(tpr.sale_o_sale) ='SI'
										and te.sorteo=ts.sorteo
						                and te.id_juego=ts.id_juego
						                and te.numero=tg.billete
						                and te.posicion=tg.id_premio_descripcion
										GROUP BY tg.id_premio_descripcion", array($sorteo, $id_juego));

            if ($rs_extraccion_segundo->RecordCount() == 0) {
                $mensaje = array("mensaje" => "En este sorteo no hay juego Sortea Hasta Que Sale " . $juego, "tipo" => "info");
                header('Content-Type: application/json');
                die(json_encode($mensaje));
            }
        }

        ComenzarTransaccion($db);
        sql("UPDATE SGS.t_parametro_compartido
						SET VALOR=?
					WHERE ID_JUEGO=?
					  AND PARAMETRO='ZONA_MOSTRANDO'", array($juego, $id_juego));
        $pantalla = '';
        FinalizarTransaccion($db);
        $juego = '';
        if ($tipo == 'ver_siempre_sale') {
            $juego = 'SIEMPRE SALE';
        } else if ($tipo == 'ver_extraordinario') {
            $juego = 'EXTRAORDINARIO';
        } else if ($tipo == 'ver_tradicional') {
            $juego = 'TRADICIONAL';
        }

        $mensaje = array("mensaje" => "Se va a mostrar el juego " . $juego, "tipo" => "info");
    } catch (exception $e) {
        $mensaje = array("mensaje" => "Error al insertar: " . $db->ErrorMsg(), "tipo" => "error");
    }

    header('Content-Type: application/json');
    echo json_encode($mensaje);
}

/**
Eliminar extraccion sorteo
 */
if ($accion == 'eliminar') {

    $extraccion = isset($_POST['extraccion']) ? $_POST['extraccion'] : '';
    $posicion   = isset($_POST['posicion']) ? $_POST['posicion'] : '';
    $entero     = isset($_POST['entero']) ? $_POST['entero'] : '';
    //$db->debug=true;
    $sorteo   = $_SESSION['sorteo'];
    $id_juego = $_SESSION['id_juego'];
    conectar_db();
    //$db->debug=true;
    ComenzarTransaccion($db);
    try {
        sql('DELETE
				FROM SGS.T_EXTRACCION
				WHERE ID_EXTRACCION = ?', array($extraccion));

        sql("UPDATE SGS.T_PARAMETRO_COMPARTIDO

                        SET VALOR               = 'SI',ID_USUARIO = ?

                        WHERE PARAMETRO         = 'REINICIO'

                            AND ID_JUEGO        = ? ",array($_SESSION['dni'],$_SESSION['id_juego']));

        sql('   DELETE
				FROM SGS.T_PREMIO_EXTRACTO
				WHERE ID_DESCRIPCION      = ?
				AND ID_JUEGO           	  = ?
				AND SORTEO                = ?
				AND BILLETE               = ?
                ', array($posicion, $id_juego, $sorteo, $entero));

        $mensaje = array("mensaje" => "Se Elimino Correctamente la posicion " . $posicion, "tipo" => "error");
    } catch (exception $e) {
        $mensaje = array("mensaje" => "Error al insertar: " . $db->ErrorMsg(), "tipo" => "error");
    }
    FinalizarTransaccion($db);
    header('Content-Type: application/json');
    echo json_encode($mensaje);
}
