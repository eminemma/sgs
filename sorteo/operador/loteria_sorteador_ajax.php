<?php
@session_start();
include_once dirname(__FILE__) . '/../../db.php';
include_once dirname(__FILE__) . '/../../librerias/alambre/funcion.inc.php';
/*error_reporting(E_ALL);
ini_set('display_errors',1);*/
$accion  = isset($_POST['accion']) ? $_POST['accion'] : '';
$validar = isset($_POST['validar']) ? $_POST['validar'] : '';
$juego   = isset($_POST['juego']) ? $_POST['juego'] : '';

/**
Devolver la configuracion de la Loteria
 */
if ($accion == 'configuracion' && $juego == 'primer_juego') {
    conectar_db();
    //$db->debug=true;
    $sorteo   = $_SESSION['sorteo'];
    $id_juego = $_SESSION['id_juego'];

    $loteria   = array();
    $rs_sorteo = sql('SELECT  PRIMER_ELEMENTO AS billete_inicial, ULTIMO_ELEMENTO AS billete_final,
							1 as fraccion_inicial,fracciones as fraccion_final
					FROM sgs.T_SORTEO
					WHERE SORTEO 	 = ?
						AND ID_JUEGO 	 = ? ', array($sorteo, $id_juego));

    $row_sorteo = siguiente($rs_sorteo);

    $loteria['min_fraccion']                    = $row_sorteo->FRACCION_INICIAL;
    $loteria['max_fraccion']                    = $row_sorteo->FRACCION_FINAL;
    $loteria['min_billete']                     = $row_sorteo->BILLETE_INICIAL;
    $loteria['max_billete']                     = $row_sorteo->BILLETE_FINAL;
    $loteria['cantidad_premios_tradicional']    = 20;
    $loteria['cantidad_premios_extraordinario'] = 6;

    $rs_programa = sql("	SELECT 	tpp.id_descripcion AS posicion,
  							 	tpp.afecta,
  								tpd.descripcion,
  								DECODE(upper(tpp.sale_o_sale),'SI',1,'NO',0) AS siempre_sale,
  								(SELECT count(*) FROM SGS.T_EXTRACCION te WHERE te.posicion=tpp.id_descripcion and te.sorteo=ts.sorteo) as SORTEADO
						FROM 	sgs.T_SORTEO ts,
  								SGS.t_programa tp,
  								SGS.t_programa_premios tpp,
  								SGS.t_premio_descripcion tpd
						WHERE ts.SORTEO       =?
							AND ts.ID_JUEGO       =?

							and ts.id_programa=tp.id_programa
							AND tp.id_juego       =ts.id_juego
							AND tpp.id_programa   =tp.id_programa
							AND tpd.id_premio_desc=tpp.id_descripcion
                            AND tpd.descripcion <> 'VIGESIMO PRIMER PREMIO'
						 order by tpp.id_descripcion", array($sorteo, $id_juego));

    //AND tpd.descripcion <> 'VIGESIMO CUARTO PREMIO'

    $loteria['premios'] = array();
    while ($row_programa = siguiente($rs_programa)) {
        $premio                 = array();
        $premio['posicion']     = strtolower($row_programa->POSICION);
        $premio['afecta']       = strtolower($row_programa->AFECTA);
        $premio['descripcion']  = ucfirst($row_programa->DESCRIPCION);
        $premio['siempre_sale'] = (bool) $row_programa->SIEMPRE_SALE;
        $premio['sorteado']     = (bool) $row_programa->SORTEADO;
        array_push($loteria['premios'], $premio);
    }
    header('Content-Type: application/json');
    echo json_encode($loteria);
    exit;
}

/**
Devolver la configuracion de la Loteria (SIEMPRE SALE)
 */
if ($accion == 'configuracion' && $juego == 'segundo_juego') {
    $sorteo    = $_SESSION['sorteo'];
    $id_juego  = $_SESSION['id_juego'];
    $loteria   = array();
    $rs_sorteo = sql('SELECT  PRIMER_ELEMENTO AS billete_inicial, ULTIMO_ELEMENTO AS billete_final,
							1 as fraccion_inicial,fracciones as fraccion_final
					FROM sgs.T_SORTEO
					WHERE SORTEO 	 = ?
						AND ID_JUEGO 	 = ? ', array($sorteo, $id_juego));

    $row_sorteo = siguiente($rs_sorteo);

    $loteria['min_fraccion'] = $row_sorteo->FRACCION_INICIAL;
    $loteria['max_fraccion'] = $row_sorteo->FRACCION_FINAL;
    $loteria['min_billete']  = $row_sorteo->BILLETE_INICIAL;
    $loteria['max_billete']  = $row_sorteo->BILLETE_FINAL;

    $rs_programa = sql("	SELECT 	tpp.id_descripcion AS posicion,
  							 	tpp.afecta,
  								tpd.descripcion,
  								DECODE(upper(tpp.sale_o_sale),'SI',1,'NO',0) AS siempre_sale
						FROM 	sgs.T_SORTEO ts,
  								SGS.t_programa tp,
  								SGS.t_programa_premios tpp,
  								SGS.t_premio_descripcion tpd
						WHERE ts.SORTEO       =?
							AND ts.ID_JUEGO       =?
							AND tp.id_juego       =ts.id_juego
							AND tpp.id_programa   =tp.id_programa
							AND tpd.id_premio_desc=tpp.id_descripcion
              				AND upper(tpp.sale_o_sale)='SI'
                            and tpp.PREMIO_ID_ESPECIAS is null
              				AND ts.id_programa=tp.id_programa", array($sorteo, $id_juego));
    $loteria['premios'] = array();
    while ($row_programa = siguiente($rs_programa)) {
        $premio                 = array();
        $premio['posicion']     = strtolower($row_programa->POSICION);
        $premio['afecta']       = strtolower($row_programa->AFECTA);
        $premio['descripcion']  = ucfirst($row_programa->DESCRIPCION);
        $premio['siempre_sale'] = (bool) $row_programa->SIEMPRE_SALE;
        array_push($loteria['premios'], $premio);
    }

    header('Content-Type: application/json');
    echo json_encode($loteria);
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
    $billete      = isset($_POST['billete']) ? $_POST['billete'] : '';
    $id_juego     = 1;
    $sorteo       = $_SESSION['sorteo'];
    $usuario      = $_SESSION['dni'];

    if ((int) $juego == 4) {
        try {
            //$db->debug = true;
            //
            $rs          = sql("SELECT COUNT(*) AS EXISTE FROM SGS.T_EXTRACCION WHERE SORTEO=? AND ID_JUEGO= ? AND POSICION = ?", array($sorteo, $id_juego, $posicion));
            $row_ganador = siguiente($rs);
            if ($row_ganador->EXISTE > 0) {
                die('<div id="error_juego" class="alert alert-error"> <button type="button" class="close" onclick="$(this).parent().remove();">x</button><span><i class="icon-remove"></i></span><span class="contenido_error">Ya se ingreso el billete entero</span></div>');
            }

            // $stmt = $db->PrepareSP("BEGIN SGS.PR_TT_NUEVO_SORTEO_ENTERO_URNA(:a1, :a2, :a3, :a4, :a5); END;");
            // $db->InParameter($stmt, $id_juego, 'a1');
            // $db->InParameter($stmt, $sorteo, 'a2');
            // $db->InParameter($stmt, $posicion, 'a3');
            // $db->InParameter($stmt, $juego, 'a4');
            // $db->InParameter($stmt, $billete, 'a5');
            // $ok = $db->Execute($stmt);
            $stmt = $db->PrepareSP("BEGIN SGS.PR_TT_NUEVO_SORTEO_ENTERO(:a1, :a2, :a3, :a4); END;");
            $db->InParameter($stmt, $id_juego, 'a1');
            $db->InParameter($stmt, $sorteo, 'a2');
            $db->InParameter($stmt, $posicion, 'a3');
            $db->InParameter($stmt, $juego, 'a4');
            $ok = $db->Execute($stmt);

            $db->CommitTrans();
            FinalizarTransaccion($db);
            die('<div id="error_juego" class="alert alert-success"> <button type="button" class="close" onclick="$(this).parent().remove();">x</button><span><i class="icon-remove"></i></span><span class="contenido_error">Se busco el ganador para el sorteo entero</span></div>');
        } catch (exception $e) {
            die('<div id="error_juego" class="alert alert-error"> <button type="button" class="close" onclick="$(this).parent().remove();">x</button><span><i class="icon-remove"></i></span><span class="contenido_error">' . $e->GetMessage() . '</span></div>');
        }

    } else if ((int) $juego == 3) {
        try {
            //$db->debug = true;
            //
            $rs          = sql("SELECT COUNT(*) AS EXISTE FROM SGS.T_EXTRACCION WHERE SORTEO=? AND ID_JUEGO= ? AND POSICION = ?", array($sorteo, $id_juego, $posicion));
            $row_ganador = siguiente($rs);
            if ($row_ganador->EXISTE > 0) {
                die('<div id="error_juego" class="alert alert-error"> <button type="button" class="close" onclick="$(this).parent().remove();">x</button><span><i class="icon-remove"></i></span><span class="contenido_error">Ya se realizo el sorteo para este premio</span></div>');
            }

            // $stmt = $db->PrepareSP("BEGIN SGS.PR_TT_NUEVO_SORTEO_ENTERO_URNA(:a1, :a2, :a3, :a4, :a5); END;");
            // $db->InParameter($stmt, $id_juego, 'a1');
            // $db->InParameter($stmt, $sorteo, 'a2');
            // $db->InParameter($stmt, $posicion, 'a3');
            // $db->InParameter($stmt, $juego, 'a4');
            // $db->InParameter($stmt, $billete, 'a5');
            $stmt = $db->PrepareSP("BEGIN SGS.PR_TT_NUEVO_SORTEO_ENTERO_FRA(:a1, :a2, :a3, :a4); END;");
            // $ok = $db->Execute($stmt);
            $db->InParameter($stmt, $id_juego, 'a1');
            $db->InParameter($stmt, $sorteo, 'a2');
            $db->InParameter($stmt, $posicion, 'a3');
            $db->InParameter($stmt, $juego, 'a4');
            $ok = $db->Execute($stmt);

            $db->CommitTrans();
            FinalizarTransaccion($db);
            die('<div id="error_juego" class="alert alert-success"> <button type="button" class="close" onclick="$(this).parent().remove();">x</button><span><i class="icon-remove"></i></span><span class="contenido_error">Se busco el ganador para el sorteo entero</span></div>');
        } catch (exception $e) {
            die('<div id="error_juego" class="alert alert-error"> <button type="button" class="close" onclick="$(this).parent().remove();">x</button><span><i class="icon-remove"></i></span><span class="contenido_error">' . $e->GetMessage() . '</span></div>');
        }

    } else {

        //Segun que afecta la consulta de busqueda cambia
        $variables           = array();
        $busqueda_extraccion = "	SELECT COUNT(*) as CANTIDAD
								FROM sgs.T_EXTRACCION
								WHERE POSICION=?
									AND ID_JUEGO=?
									AND SORTEO=?";
        array_push($variables, $posicion, $id_juego, $sorteo);

        //Controlo si ya esta pasado como extraccion
        try {
            $rs_numero = sql($busqueda_extraccion, $variables);

        } catch (exception $e) {
            $mensaje = array("mensaje" => "Error al insertar: " . $db->ErrorMsg(), "tipo" => "error");
        }

        $row_numeros_sorteado = siguiente($rs_numero);

        if ($row_numeros_sorteado->CANTIDAD == 0 || $juego == 'segundo_juego') {
            //$db->debug=true;
            //Control de ingresos de los operadores
            if ($juego == 'primer_juego') {
                $juego = 1;
            } else if ($juego == 'segundo_juego') {
                $juego = 2;
            }

            if (!empty($fraccion)) {
                $juego = 3;
            }

            try {

                $rs_control = sql("		SELECT NUMERO,POSICION,FRACCION
										FROM SGS.TEMP_CTRL_INGRESO_NUMERO
										WHERE ZONA_JUEGO 		 = ?
											AND TRIM(ID_USUARIO)!= ?
											AND ID_JUEGO 		 = ?", array($juego, 'DU' . $usuario, $id_juego));
                //$mensaje = 'OK';
            } catch (exception $e) {
                $mensaje = array("mensaje" => "Error al insertar: " . $db->ErrorMsg(), "tipo" => "error");
            }

            $existe_bola     = false;
            $existe_posicion = false;
            if ($rs_control->RowCount() > 0) {
                $existe_posicion = true;
            }

            while ($bolas_ingresadas = siguiente($rs_control)) {
                if ($bolas_ingresadas->POSICION == $posicion && $bolas_ingresadas->NUMERO == $entero && empty($fraccion)) {
                    $existe_bola = true;
                    break;
                } else if ($bolas_ingresadas->POSICION == $posicion && $bolas_ingresadas->NUMERO == $entero && $bolas_ingresadas->FRACCION == $fraccion) {
                    $existe_bola = true;
                    break;
                }
            }

            try {
                //Buscar ganador para el Siempre Sale (Cualquier posicion puede ser) "SOLAMENTE PARA SABER SI ESTA VACANTE"
                //Afecta a siempre sale
                if ($juego == 'primer_juego') {
                    $juego = 1;
                } else if ($juego == 'segundo_juego') {
                    $juego = 2;
                }
                if (!empty($fraccion)) {
                    $juego = 3;
                }
                /*
                //anulado para sorteo de reyes 2017 (con 6 extraordinarios)
                else if ($posicion == 26) {
                $juego = 4;
                }
                 */
                $tipo_sorteo = 'E';
                $fraccion    = (isset($fraccion) || empty($fraccion)) ? $fraccion : null;
                //$db->debug=true;
                /*        var_dump($siempre_sale);*/
                $usuario        = 'DU' . $_SESSION['dni'];
                $siempre_sale_p = 0;
                if ($existe_posicion && $existe_bola) {
                    $siempre_sale_p = ($siempre_sale == 'true') ? '1' : '0';
                }
                //SOLO SERVIDOR B
                if ($_SESSION['juego_tipo'] == 'EXTRAORDINARIA') {
                    $siempre_sale_p = '1';
                }
                $cantidadGanadores = 0;
                $stmt              = $db->PrepareSP("BEGIN SGS.PR_INGRESAR_NUMERO(:a1, :a2, :a3, :a4, :a5, :a6, :a7, :a8,:a9,:a10); END;");
                $db->InParameter($stmt, $juego, 'a1');
                $db->InParameter($stmt, $entero, 'a2');
                $db->InParameter($stmt, $id_juego, 'a3');
                $db->InParameter($stmt, $sorteo, 'a4');
                $db->InParameter($stmt, $posicion, 'a5');
                $db->InParameter($stmt, $fraccion, 'a6');
                $db->InParameter($stmt, $tipo_sorteo, 'a7');
                $db->InParameter($stmt, $siempre_sale_p, 'a8');
                $db->InParameter($stmt, $usuario, 'a9');
                $db->OutParameter($stmt, $cantidadGanadores, 'a10');
                $ok = $db->Execute($stmt);
                /* var_dump($juego, $entero, $id_juego, $sorteo, $posicion, $fraccion, $tipo_sorteo, $siempre_sale, $usuario);*/

                //sql('COMMIT');
                //SOLO SERVIDOR B
                $existe_posicion = false;
                $existe_bola     = true;
                if ($siempre_sale_p == '1') {

                    //    Valido las bolas ingresadas de ambos usuarios si coinciden (LO HACE EL PROCEDURE)
                    //    Si coinciden grabo la extraccion (LO HACE EL PROCEDURE)
                    if (!$ok) {
                        $mensaje = array("mensaje" => "Error al insertar: " . $db->ErrorMsg(), "tipo" => "error");
                    } else {
                        if (!$existe_posicion) {
                            $mensaje = array("mensaje" => "Se cargo Correctamente la Extraccion", "tipo" => "info");
                            //Borro al tabla de control de ingreso, sino hay coincidencias
                        } else if ($existe_posicion && !$existe_bola) {
                            $mensaje = array("mensaje" => "Se cargo pero sin coincidencias", "tipo" => "error");
                            sql("UPDATE SGS.t_parametro_compartido
									SET VALOR=?,ID_USUARIO=?
								WHERE ID_JUEGO=?
										AND PARAMETRO='COINCIDENCIA'", array('No existie coincidencias en los numeros ingresados ', $_SESSION['dni'], $id_juego));
                            //Borro al tabla de control de ingreso, sino hay coincidencias
                            sql("DELETE FROM sgs.TEMP_CTRL_INGRESO_NUMERO");

                        } else if ($existe_posicion && $existe_bola && $cantidadGanadores > 0) {
                            $mensaje = array("mensaje" => "Se cargo Correctamente la Extraccion, con Ganador", "tipo" => "success");
                            sql("DELETE FROM sgs.TEMP_CTRL_INGRESO_NUMERO");
                        } else if ($existe_posicion && $existe_bola && $cantidadGanadores == 0) {
                            $mensaje = array("mensaje" => "Se cargo Correctamente la Extraccion, POZO VACANTE", "tipo" => "success");
                            sql("DELETE FROM sgs.TEMP_CTRL_INGRESO_NUMERO");
                        }

                        if (!$existe_posicion) {
                            $mensaje = array("mensaje" => "Se cargo Correctamente la Extraccion", "tipo" => "info");

                        } else if ($existe_posicion && $existe_bola) {
                            $mensaje = array("mensaje" => "Validación Correcta", "tipo" => "success");

                            sql("UPDATE SGS.t_parametro_compartido
                                    SET VALOR=?,ID_USUARIO=?
                                WHERE ID_JUEGO=?
                                        AND PARAMETRO='VALIDACION'", array('Validación Correcta entre operadores', $_SESSION['dni'], $id_juego));

                            sql("DELETE FROM sgs.TEMP_CTRL_INGRESO_NUMERO");
                        } else if ($existe_posicion && !$existe_bola) {
                            $mensaje = array("mensaje" => "Se cargo pero sin coincidencias", "tipo" => "error");
                            //Grabo en parametros compartidos coincidencias
                            sql("UPDATE SGS.t_parametro_compartido
                                    SET VALOR=?,ID_USUARIO=?
                                WHERE ID_JUEGO=?
                                        AND PARAMETRO='COINCIDENCIA'", array('No existio coincidencias en los numeros ingresados', $_SESSION['dni'], $id_juego));
                            //Borro al tabla de control de ingreso, sino hay coincidencias
                            sql("DELETE FROM sgs.TEMP_CTRL_INGRESO_NUMERO");
                        }
                    }
                } else if ($siempre_sale_p == '0') {
                    //    No afecta a siempre sale (JUEGO 2)
                    if (!$ok) {
                        $mensaje = array("mensaje" => "Error al insertar: " . $db->ErrorMsg(), "tipo" => "error");
                    } else {
                        if (!$existe_posicion) {
                            $mensaje = array("mensaje" => "Se cargo Correctamente la Extraccion", "tipo" => "info");

                        } else if ($existe_posicion && $existe_bola) {
                            $mensaje = array("mensaje" => "Validación Correcta", "tipo" => "success");

                            sql("UPDATE SGS.t_parametro_compartido
									SET VALOR=?,ID_USUARIO=?
								WHERE ID_JUEGO=?
										AND PARAMETRO='VALIDACION'", array('Validación Correcta entre operadores', $_SESSION['dni'], $id_juego));

                            sql("DELETE FROM sgs.TEMP_CTRL_INGRESO_NUMERO");
                        } else if ($existe_posicion && !$existe_bola) {
                            $mensaje = array("mensaje" => "Se cargo pero sin coincidencias", "tipo" => "error");
                            //Grabo en parametros compartidos coincidencias
                            sql("UPDATE SGS.t_parametro_compartido
									SET VALOR=?,ID_USUARIO=?
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
        $rs_extraccion_primer = sql('SELECT COUNT(*) AS EXTRACCION
							FROM sgs.T_EXTRACCION
							WHERE ID_JUEGO=?
 								AND SORTEO=?
 								AND (ZONA_JUEGO=1 OR ZONA_JUEGO=3)', array($id_juego, $sorteo));

        $rs_extraccion_segundo = sql('	SELECT COUNT(*) AS EXTRACCION
										FROM sgs.T_EXTRACCION
										WHERE ID_JUEGO=?
			 								AND SORTEO=?
			 								AND ZONA_JUEGO=4', array($id_juego, $sorteo));

        $rs_cantidad_extracciones = sql(' SELECT COUNT(*) as EXTRACCIONES
										FROM SGS.T_SORTEO TS,
										  SGS.T_PROGRAMA TP,
										  SGS.t_programa_premios tpr
										WHERE ts.SORTEO        =?
										AND TS.ID_JUEGO        =?
                                        and tpr.id_descripcion <= 20
										AND ts.id_programa     = tp.id_programa
										AND tp.id_programa     = tpr.id_programa', array($sorteo, $id_juego));
        // AND tpr.ID_DESCRIPCION<> 24

        $mensaje                   = array("mensaje" => "No Finalizo", "tipo" => "error");
        $cantidad_extracciones     = 0;
        $row_extraccion_primer     = siguiente($rs_extraccion_primer);
        $row_extraccion_segundo    = siguiente($rs_extraccion_segundo);
        $row_cantidad_extracciones = siguiente($rs_cantidad_extracciones);
        $cantidad_extracciones     = (int) $row_extraccion_primer->EXTRACCION;

        if ($cantidad_extracciones == (int) $row_cantidad_extracciones->EXTRACCIONES) {
            $mensaje = array("mensaje" => "Finalizo", "tipo" => "error");
        }

        $rs_parametro_coincidencia = sql(" SELECT VALOR,ID_USUARIO
										FROM SGS.t_parametro_compartido TS
										WHERE ID_JUEGO=?
										  AND PARAMETRO='COINCIDENCIA'", array($id_juego));
        $row_parametro_coincidencia = siguiente($rs_parametro_coincidencia);

        if (!is_null($row_parametro_coincidencia->VALOR)) {
            $mensaje['coincidencia'] = $row_parametro_coincidencia->VALOR;
        }

        $rs_parametro_reincio = sql(" SELECT VALOR,ID_USUARIO
										FROM SGS.t_parametro_compartido TS
										WHERE ID_JUEGO=?
										  AND PARAMETRO='REINICIO'", array($id_juego));
        $row_parametro_reinicio = siguiente($rs_parametro_reincio);

        if (!is_null($row_parametro_reinicio->VALOR) && $row_parametro_reinicio->VALOR == 'SI') {
            $mensaje['reinicio'] = $row_parametro_reinicio->VALOR;
            sql("UPDATE SGS.t_parametro_compartido
					SET VALOR='NO'
				 WHERE ID_JUEGO=?
					AND PARAMETRO='REINICIO'", array($id_juego));
        }
        if ($row_parametro_coincidencia->ID_USUARIO != $_SESSION['dni']) {
            sql("UPDATE SGS.t_parametro_compartido
					SET VALOR=null,ID_USUARIO=null
				 WHERE ID_JUEGO=?
					AND PARAMETRO='COINCIDENCIA'", array($id_juego));
        }

        $rs_parametro_coincidencia = sql(" SELECT VALOR,ID_USUARIO
										FROM SGS.t_parametro_compartido TS
										WHERE ID_JUEGO=?
										  AND PARAMETRO='VALIDACION'", array($id_juego));
        $row_parametro_coincidencia = siguiente($rs_parametro_coincidencia);

        if (!is_null($row_parametro_coincidencia->VALOR)) {
            $mensaje['coincidencia'] = $row_parametro_coincidencia->VALOR;
            $mensaje['tipo']         = 'success';
            if ($row_parametro_coincidencia->ID_USUARIO != $_SESSION['dni']) {
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

if ($accion == 'control_ganador' && $juego == 'segundo_juego') {
    $id_juego = 1;
    $sorteo   = $_SESSION['sorteo'];
    try {
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
           and tpr.id_descripcion<>21
        GROUP BY tg.id_premio_descripcion", array($sorteo, $id_juego));

        //--and tpr.id_descripcion <> 24

        /* if ($rs_extraccion_segundo->RecordCount() == 0) {
        $mensaje = array("mensaje" => "NO SALE_O_SALE", "tipo" => "error");
        header('Content-Type: application/json');
        die(json_encode($mensaje));
        }*/
        $mensaje = array("mensaje" => "No Finalizo", "tipo" => "error");
        while ($row_extraccion_segundo = siguiente($rs_extraccion_segundo)) {
            if ($row_extraccion_segundo->GANADOR == 0) {
                break;
            }

            $mensaje = array("mensaje" => "Finalizo", "tipo" => "error");
        }

        $rs_parametro_coincidencia = sql(" SELECT VALOR,ID_USUARIO
										FROM SGS.t_parametro_compartido TS
										WHERE ID_JUEGO=?
										  AND PARAMETRO='COINCIDENCIA'", array($id_juego));
        $row_parametro_coincidencia = siguiente($rs_parametro_coincidencia);

        if (!is_null($row_parametro_coincidencia->VALOR)) {
            $mensaje['coincidencia'] = $row_parametro_coincidencia->VALOR;
        }

        if ($row_parametro_coincidencia->ID_USUARIO != $_SESSION['dni']) {
            sql("UPDATE SGS.t_parametro_compartido
					SET VALOR=null,ID_USUARIO=null
				 WHERE ID_JUEGO=?
					AND PARAMETRO='COINCIDENCIA'", array($id_juego));
        }

        $rs_parametro_coincidencia = sql(" SELECT VALOR,ID_USUARIO
										FROM SGS.t_parametro_compartido TS
										WHERE ID_JUEGO=?
										  AND PARAMETRO='VALIDACION'", array($id_juego));
        $row_parametro_coincidencia = siguiente($rs_parametro_coincidencia);

        if (!is_null($row_parametro_coincidencia->VALOR)) {
            $mensaje['coincidencia'] = $row_parametro_coincidencia->VALOR;
            $mensaje['tipo']         = 'success';

            if ($row_parametro_coincidencia->ID_USUARIO != $_SESSION['dni']) {
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

if ($accion == 'control_ganador' && $juego == 'incentivo') {
    $id_juego = 1;
    $sorteo   = $_SESSION['sorteo'];
    try {
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
            $mensaje = array("mensaje" => "NO SALE_O_SALE", "tipo" => "error");
            header('Content-Type: application/json');
            die(json_encode($mensaje));
        }
        $mensaje = array("mensaje" => "No Finalizo", "tipo" => "error");
        while ($row_extraccion_segundo = siguiente($rs_extraccion_segundo)) {
            if ($row_extraccion_segundo->GANADOR == 0) {
                break;
            }

            $mensaje = array("mensaje" => "Finalizo", "tipo" => "error");
        }

        $rs_parametro_coincidencia = sql(" SELECT VALOR,ID_USUARIO
										FROM SGS.t_parametro_compartido TS
										WHERE ID_JUEGO=?
										  AND PARAMETRO='COINCIDENCIA'", array($id_juego));
        $row_parametro_coincidencia = siguiente($rs_parametro_coincidencia);

        if (!is_null($row_parametro_coincidencia->VALOR)) {
            $mensaje['coincidencia'] = $row_parametro_coincidencia->VALOR;
        }
        if ($row_parametro_coincidencia->ID_USUARIO != $_SESSION['dni']) {
            sql("UPDATE SGS.t_parametro_compartido
					SET VALOR=null,ID_USUARIO=null
				 WHERE ID_JUEGO=?
					AND PARAMETRO='COINCIDENCIA'", array($id_juego));
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
        } else if ($tipo == 'ver_sorteo_entero') {
            $juego = 4;
        }

        /*     if ($tipo == 'ver_siempre_sale') {
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
        }*/

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
        } else if ($tipo == 'ver_sorteo_entero') {
            $juego = 'SORTEO ENTERO';
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

        //if ($posicion == 1) {
        sql('   DELETE
					FROM SGS.T_GANADORES
					WHERE ID_JUEGO            = ?
					AND SORTEO                = ?
					AND ID_PREMIO_DESCRIPCION = ?
					AND BILLETE               = ?', array($id_juego, $sorteo, $posicion, $entero));
        //}
        /*
        if($posicion==24){
        sql('   DELETE
        FROM SGS.T_GANADORES
        WHERE ID_JUEGO            = ?
        AND SORTEO                = ?
        AND ID_PREMIO_DESCRIPCION = ?
        AND BILLETE               = ?',array($id_juego,$sorteo,$posicion,$entero));
        }
         */

        sql('   DELETE
				FROM SGS.T_PREMIO_EXTRACTO
				WHERE ID_DESCRIPCION      = ?
				AND ID_JUEGO           	  = ?
				AND SORTEO                = ?
				AND BILLETE               = ?', array($posicion, $id_juego, $sorteo, $entero));

        $mensaje = array("mensaje" => "Se Elimino Correctamente la posicion " . $posicion, "tipo" => "error");
    } catch (exception $e) {
        $mensaje = array("mensaje" => "Error al insertar: " . $db->ErrorMsg(), "tipo" => "error");
    }
    FinalizarTransaccion($db);
    header('Content-Type: application/json');
    echo json_encode($mensaje);
}
