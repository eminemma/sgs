<?php
//error_reporting(E_ALL);
//ini_set('display_errors', 1);
session_start();
include_once dirname(__FILE__) . '/../../mensajes.php';
include_once dirname(__FILE__) . '/../../db_kanban.php';
include_once dirname(__FILE__) . '/../../db.php';

$accion = isset($_GET['accion']) ? $_GET['accion'] : '';

if ($accion == 'exportar') {

    $sorteo   = $_SESSION['sorteo'];
    $id_juego = $_SESSION['id_juego'];
    try {
        conectar_db_kanban();
        conectar_db();
        ComenzarTransaccion_kanban($db_kanban);
        $rs_sorteo_kanban = sql_kanban('	SELECT *
										FROM KANBAN.T_SORTEO
										WHERE SORTEO=?
											AND ID_JUEGO=?', array($sorteo, $id_juego));
        if ($rs_sorteo_kanban->RecordCount() > 0) {
            $rs_sorteo_local = sql("	SELECT 	to_char(FECHA_SORTEO,'dd/mm/yyyy hh24:mi:ss') as FECHA_SORTEO,
											to_char(FECHA_BAJA,'dd/mm/yyyy') as FECHA_BAJA,
											ID_ESCRIBANO,
											ID_JEFE,
											ID_OPERADOR,
											ID_PROGRAMA,
											PRIMER_ELEMENTO,
											ULTIMO_ELEMENTO,
											FRACCIONES,
											PROGRESION,
											CANTIDAD_SORTEOS_FECHA,
											SORTEO_UNICO,
											DESCRIPCION,
											MONTO_FRACCION,
											FECHA,
											ID_TIPO_JUEGO,
											CANTIDAD_SERIE,
											to_char(FECHA_HASTA_PAGO_PREMIO,'dd/mm/yyyy') as FECHA_HASTA_PAGO_PREMIO,
											QUINIELA_ASOC
									FROM SGS.T_SORTEO
									WHERE ID_JUEGO=?
										AND SORTEO=?", array($id_juego, $sorteo));
            $row_sorteo_local = siguiente($rs_sorteo_local);
            //Importar datos del Sorteo de KANBAN con los cambios del Sorteador

            /*$db_kanban->debug = true;*/
            //$db->debug=true;
            sql_kanban("	UPDATE KANBAN.T_SORTEO S
								SET S.FECHA_SORTEO 			  = to_date(?,'dd/mm/yyyy HH24:mi:ss'),
									S.FECHA_BAJA              = to_date(?,'dd/mm/yyyy'),
									S.ID_ESCRIBANO            = ?,
									S.USUARIO_JEFE_SORTEO  	  = ?,
									S.USUARIO_OPERADOR     	  = ?,
									S.FECHA_HASTA_PAGO_PREMIO = to_date(?,'dd/mm/yyyy'),
									S.QUINIELA_ASOC           = ?,
                                    S.ESTADO_SORTEO           = 'F'
								WHERE S.ID_JUEGO           	  = ?
									AND S.SORTEO  			  = ?", array($row_sorteo_local->FECHA_SORTEO,
                $row_sorteo_local->FECHA_BAJA,
                $row_sorteo_local->ID_ESCRIBANO,
                $row_sorteo_local->ID_JEFE,
                $row_sorteo_local->ID_OPERADOR,
                $row_sorteo_local->FECHA_HASTA_PAGO_PREMIO,
                $row_sorteo_local->QUINIELA_ASOC,
                $id_juego, $sorteo));

            //Importar Extractos al KANBAN
            $id_programa = $row_sorteo_local->ID_PROGRAMA;
            if ($id_juego == 32) {
                sql_kanban("DELETE FROM kanban.t_premio_extracto WHERE sorteo = ? and id_juego = ?", array($sorteo, $id_juego));
            }
            $rs_extractos_local = sql('	SELECT * FROM SGS.T_PREMIO_EXTRACTO WHERE ID_JUEGO=? AND SORTEO=?', array($id_juego, $sorteo));
            if ($rs_extractos_local->RecordCount() == 0) {
                error('No existen datos enla tabla t_premio_extracto (SORTEADOR)');
                exit;
            }
            while ($row_extractos_local = siguiente($rs_extractos_local)) {
                $rs_premio_extracto = sql('	SELECT SALE_O_SALE,ID_DESCRIPCION
											FROM SGS.T_PROGRAMA_PREMIOS
											WHERE 	ID_PROGRAMA=?
												AND ID_DESCRIPCION=?', array($id_programa, $row_extractos_local->ID_DESCRIPCION));

                $row_premio_extracto = siguiente($rs_premio_extracto);
                //Si el extracto no es el de siempre sale
                if (!is_null($row_premio_extracto->SALE_O_SALE) && $row_premio_extracto->SALE_O_SALE == 'SI' && (int) $row_extractos_local->ZONA_JUEGO == 2) {
                    $rs_ss_extracto = sql_kanban('	SELECT COUNT(*) as CANTIDAD_SALE_O_SALE
														FROM KANBAN.T_EXTRACTO_SALEOSALE
														WHERE ID_JUEGO=?
															AND SORTEO=?
															AND ID_DESCRIPCION=?
															AND BILLETE=?', array($id_juego, $sorteo, $row_extractos_local->ID_DESCRIPCION, $row_extractos_local->BILLETE));
                    $row_ss_extracto = siguiente_kanban($rs_ss_extracto);
                    if ($row_ss_extracto->CANTIDAD_SALE_O_SALE > 0) {
                        $res_ganador = sql('	SELECT COUNT(*) AS GANADOR
											FROM SGS.T_GANADORES
											WHERE ID_PREMIO_DESCRIPCION=?
											  AND BILLETE=?', array($row_extractos_local->ID_DESCRIPCION,
                            $row_extractos_local->BILLETE));

                        $row_ganador = siguiente($res_ganador);

                        $ganador = 'N';
                        if ($row_ganador->GANADOR > 0) {
                            $ganador = 'S';
                        }

                        sql_kanban("	UPDATE KANBAN.T_EXTRACTO_SALEOSALE
											  SET ID_USUARIO=USER,
												  PROGRESION=?,
												  HORAEXTRACCION=to_date(?,'dd/mm/yyyy'),
												  GANADOR=?
											  WHERE ID_JUEGO=?
											  AND SORTEO=?
											  AND ID_DESCRIPCION=?
											  AND BILLETE=?", array($row_extractos_local->PROGRESION, date('d/m/Y', strtotime($row_extractos_local->HORAEXTRACCION)), $ganador, $id_juego, $sorteo, $row_extractos_local->ID_DESCRIPCION, $row_extractos_local->BILLETE));

                    } else if ($row_extractos_local->ZONA_JUEGO == 2) {

                        $res_ganador = sql('	SELECT COUNT(*) AS GANADOR
											FROM SGS.T_GANADORES
											WHERE ID_PREMIO_DESCRIPCION=?
											  AND BILLETE=?', array($row_extractos_local->ID_DESCRIPCION,
                            $row_extractos_local->BILLETE));

                        $row_ganador = siguiente($res_ganador);

                        $ganador = 'N';
                        if ($row_ganador->GANADOR > 0) {
                            $ganador = 'S';
                        }

                        sql_kanban("	INSERT
										  INTO KANBAN.T_EXTRACTO_SALEOSALE
											(
											  ID_EXTRACTO,
											  ID_DESCRIPCION,
											  BILLETE,
											  SORTEO,
											  ID_JUEGO,
											  SERIE,
											  ID_USUARIO,
											  PROGRESION,
											  HORAEXTRACCION,
											  GANADOR
											)
											VALUES
											(
											  NULL,
											  $row_extractos_local->ID_DESCRIPCION,
											  $row_extractos_local->BILLETE,
											  $sorteo,
											  $id_juego,
											  1,
											  'DU" . $_SESSION['dni'] . "',
											  $row_extractos_local->PROGRESION,
											  to_date('" . date('d/m/Y', strtotime($row_extractos_local->HORAEXTRACCION)) . "','dd/mm/yyyy'),
											  '" . $ganador . "'
											)");
                    }
                } else {
                    if ($id_juego == 32) {

                        $fraccion = $row_extractos_local->FRACCION;
                        if (is_null($row_extractos_local->FRACCION)) {
                            $fraccion = 'null';
                        }

                        $importe = $row_extractos_local->IMPORTE;
                        if (is_null($row_extractos_local->IMPORTE)) {
                            $importe = 'null';
                        }
                        $progresion = $row_extractos_local->PROGRESION;
                        if (is_null($row_extractos_local->PROGRESION)) {
                            $progresion = 'null';
                        }

                        $sorteo_asoc = $row_extractos_local->SORTEO_ASOC;
                        if (is_null($row_extractos_local->SORTEO_ASOC)) {
                            $sorteo_asoc = 'null';
                        }

                        sql_kanban("INSERT
                                        INTO KANBAN.T_PREMIO_EXTRACTO
                                          (
                                            ID_DESCRIPCION,
                                            BILLETE,
                                            SORTEO,
                                            ID_JUEGO,
                                            SERIE,
                                            ID_USUARIO,
                                            FRACCION,
                                            PROGRESION,
                                            HORAEXTRACCION,
                                            IMPORTE,
                                            SORTEO_ASOC
                                          )
                                          VALUES
                                          (
                                           $row_extractos_local->ID_DESCRIPCION,
                                           $row_extractos_local->BILLETE,
                                           $sorteo,
                                            $id_juego,
                                            1,
                                            'DU" . $_SESSION['dni'] . "',
                                            $fraccion,
                                            $progresion,
                                            to_date('" . date('d/m/Y', strtotime($row_extractos_local->HORAEXTRACCION)) . "','dd/mm/yyyy'),
                                            $importe,
                                            '$sorteo_asoc'
                                          )");

                       
                   
                    } else {
                        $rs_extracto = sql_kanban('	SELECT COUNT(*) as CANTIDAD_EXTRACTO
													FROM KANBAN.T_PREMIO_EXTRACTO
													WHERE ID_JUEGO=?
														AND SORTEO=?
														AND ID_DESCRIPCION=?', array($id_juego, $sorteo, $row_extractos_local->ID_DESCRIPCION));
                        $row_extracto = siguiente_kanban($rs_extracto);
                        if ($row_extracto->CANTIDAD_EXTRACTO > 0) {
                            $fraccion = $row_extractos_local->FRACCION;
                            if (is_null($row_extractos_local->FRACCION)) {
                                $fraccion = 'null';
                            }

                            $importe = $row_extractos_local->IMPORTE;
                            if (is_null($row_extractos_local->IMPORTE)) {
                                $importe = 'null';
                            }
                            $progresion = $row_extractos_local->PROGRESION;
                            if (is_null($row_extractos_local->PROGRESION)) {
                                $progresion = 'null';
                            }

                            sql_kanban("UPDATE KANBAN.T_PREMIO_EXTRACTO
										SET
											BILLETE=$row_extractos_local->BILLETE,
											SORTEO=$sorteo,
											ID_JUEGO=$id_juego,
											SERIE=1,
											ID_USUARIO='DU" . $_SESSION['dni'] . "',
											FRACCION=$fraccion,
											PROGRESION=$progresion,
											HORAEXTRACCION= to_date('" . date('d/m/Y', strtotime($row_extractos_local->HORAEXTRACCION)) . "','dd/mm/yyyy'),
											IMPORTE=$importe
									WHERE ID_JUEGO=?
										AND SORTEO=?
										AND ID_DESCRIPCION=$row_extractos_local->ID_DESCRIPCION", array($id_juego, $sorteo));
                        } else {
                            $fraccion = $row_extractos_local->FRACCION;
                            if (is_null($row_extractos_local->FRACCION)) {
                                $fraccion = 'null';
                            }

                            $importe = $row_extractos_local->IMPORTE;
                            if (is_null($row_extractos_local->IMPORTE)) {
                                $importe = 'null';
                            }
                            $progresion = $row_extractos_local->PROGRESION;
                            if (is_null($row_extractos_local->PROGRESION)) {
                                $progresion = 'null';
                            }

                            sql_kanban("INSERT
										INTO KANBAN.T_PREMIO_EXTRACTO
										  (
											ID_DESCRIPCION,
											BILLETE,
											SORTEO,
											ID_JUEGO,
											SERIE,
											ID_USUARIO,
											FRACCION,
											PROGRESION,
											HORAEXTRACCION,
											IMPORTE
										  )
										  VALUES
										  (
										   $row_extractos_local->ID_DESCRIPCION,
										   $row_extractos_local->BILLETE,
										   $sorteo,
											$id_juego,
											1,
											'DU" . $_SESSION['dni'] . "',
											$fraccion,
											$progresion,
											to_date('" . date('d/m/Y', strtotime($row_extractos_local->HORAEXTRACCION)) . "','dd/mm/yyyy'),
											$importe
										  )");
                        }
                    }
                }
            }

            /*verifico si existe quiniela asociada y genero*/
            if ($id_juego == 1) {
                if (!is_null($row_sorteo_local->QUINIELA_ASOC) && $row_sorteo_local->QUINIELA_ASOC != '') {
                    /*busco el sorteo en kanban*/
                    $rs_sorteo_quin_kanban = sql_kanban('	SELECT *
    										FROM KANBAN.T_SORTEO
    										WHERE SORTEO=?
    											AND ID_JUEGO=?', array($row_sorteo_local->QUINIELA_ASOC, 2));
                    if ($rs_sorteo_quin_kanban->RecordCount() > 0) {
                        try {
                            $db_kanban->Execute("CALL kanban.generar_quiniela_desde_loteria(?,?,?,?)", array($id_juego, $sorteo, 2, $row_sorteo_local->QUINIELA_ASOC));
                        } catch (exception $e) {
                            error('Error al generar el extracto de quiniela asociado -' . $db_kanban->ErrorMsg());
                            exit;
                        }

                    } else {
                        error('No Existe el Sorteo de Quiniela asociado en KANBAN (PRODUCCION)');
                        exit;
                    }
                } else {
                    error('Debe ingresar el Sorteo de Quiniela Asociado');
                    exit;
                }
            }

        } else {
            error('No Existe Creado el Sorteo en KANBAN (PRODUCCION)');
            exit;
        }
    } catch (exception $e) {
        error('Error en la base de datos' . $db->ErrorMsg());
    }

    FinalizarTransaccion_kanban($db_kanban);
 
    if($id_juego == 32){
        //include dirname(__FILE__) . '/../../mail/procesar_enviar_mail_poceada_contralor.php';
    }
    ok('Se Exportaron los datos del sorteo,extracciones al KANBAN');
}

if ($accion == 'exportar_anticipada') {

    $semana    = isset($_GET['semana']) ? $_GET['semana'] : '';
    $orden     = isset($_GET['orden']) ? $_GET['orden'] : '';
    $billete   = null;
    $fraccion  = null;
    $escribano = null;
    $semana    = $_GET['semana'];
    try {
        $rs_ganador = sql("	SELECT 	TG.ID_JUEGO,
				  					TG.SORTEO,
				  					TG.SEMANA,
				  					TG.BILLETE,
				  					TG.FRACCION,
				  					TG.AGENCIA,
				  					TG.LOCALIDAD,
				  					TG.NOMBRE,
		                			TA.ID_ESCRIBANO
							FROM 	SGS.T_ANTICIPADA_GANADORES TG,
									SGS.T_ANTICIPADA TA
							WHERE TG.SORTEO 	= ?
								AND TG.ID_JUEGO = ?
								AND TG.SEMANA  	= ?
                                AND TG.ORDEN    = ?
		            			AND TG.SEMANA 	= TA.SEMANA
		            			AND TG.SORTEO 	= TA.SORTEO
		            			AND TG.ID_JUEGO = TA.ID_JUEGO", array($_SESSION['sorteo'], $_SESSION['id_juego'], $semana, $orden));
        if ($row_ganador = siguiente($rs_ganador)) {
            $billete   = $row_ganador->BILLETE;
            $fraccion  = $row_ganador->FRACCION;
            $escribano = $row_ganador->ID_ESCRIBANO;
        } else {
            die(error('No existen extracciones para la semana seleccionada'));
        }

        conectar_db();
        conectar_db_kanban();

        $db_kanban->StartTrans();
        $db->StartTrans();
        $rs_validacion = sql_kanban(" 	SELECT SUC_BAN, NRO_AGEN, VENTA_CONTADO, VENTA_EMPLEADO, OCR
											FROM KANBAN.T_REPARTO_INTELIGENTE
											WHERE  BILLETE   = ?
												AND FRACCION = ?
												AND SORTEO   = ?
												AND SERIE    = ?
												AND ID_JUEGO = ?",
            array(
                $billete,
                $fraccion,
                $_SESSION['sorteo'],
                $_SESSION['serie'],
                $_SESSION['id_juego'],
            )
        );

        $rowValidacion = siguiente_kanban($rs_validacion);
        $mensajeAlerta = '';
        $suc_ban       = $rowValidacion->SUC_BAN;
        $nro_agen      = $rowValidacion->NRO_AGEN;

        if ($rowValidacion->VENTA_CONTADO != 'S' && $rowValidacion->VENTA_EMPLEADO != 'S') {
            //Si no es Venta Contado y tampoco Venta Empleado
            if ($rowValidacion->SUC_BAN != null && $rowValidacion->NRO_AGEN == null) {
                die(error('El billete se encuentra en el almacen de la delegacion'));
            } else if ($rowValidacion->SUC_BAN == null) {
                die(error('El billete se encuentra en el almacen general'));
            }

        }
        $rs = sql_kanban("	SELECT *
											FROM
												KANBAN.T_PREMIOS_ANTICIPADA PA,
												KANBAN.T_PROGRAMA_PREMIOS_ANTIC PP
											WHERE
												PA.ID_PREMIOS_ANTICIPADA=PP.ID_PROGRAMA_PREMIOS_ANTIC
											AND PA.BILLETE  = ?
											AND PA.FRACCION = ?
											AND PA.SORTEO   = ?
											AND PA.SERIE    = ?
											AND PA.ID_JUEGO = ?
											AND PP.SEMANA   = ?
                                            AND PP.ORDEN   = ? ",
            array($billete, $fraccion, $_SESSION['sorteo'], $_SESSION['serie'], $_SESSION['id_juego'], $semana, $orden));

        if ($rs->RowCount() > 0) {
            die(error('El premio de la semana ya se encuentra en el kanban'));
        } else {

            $rs_id_programa_premios_antic = sql_kanban("
				SELECT *
				FROM
					KANBAN.T_PROGRAMA_PREMIOS_ANTIC PP
				WHERE
					PP.ID_JUEGO    = ?
					AND PP.SORTEO  = ?
					AND PP.SERIE   = ?
					AND PP.SEMANA  = ?
                    AND PP.ORDEN   = ?",
                array($_SESSION['id_juego'], $_SESSION['sorteo'], $_SESSION['serie'], $semana, $orden));

            $row_id_programa_premios_antic = siguiente_kanban($rs_id_programa_premios_antic);

            $rs_premio = sql_kanban("   SELECT IMPORTE, DESCRIPCION AS PREMIO,ID_TIPO_PREMIO,ID_TIPO_PREMIO,ID_DESCRIPCION_ESPECIA
										  	FROM KANBAN.T_PROGRAMA_PREMIOS_ANTIC
								         	WHERE ID_PROGRAMA_PREMIOS_ANTIC = ?", array($row_id_programa_premios_antic->ID_PROGRAMA_PREMIOS_ANTIC));

            $row_premio = $rs_premio->FetchNextObject($toupper = true);

            $rs = sql_kanban("INSERT INTO KANBAN.T_PREMIOS_ANTICIPADA
                							(ID_JUEGO,SORTEO, SERIE, BILLETE, FRACCION, ID_PROGRAMA_PREMIOS_ANTIC, FECHA_CARGA, ID_USER_CARGA, ID_ESCRIBANO,CONFIRMADO)
									  	VALUES (?,?,?,?,?,?,SYSDATE,?, ?,'S')",
                array(
                    $_SESSION['id_juego'],
                    $_SESSION['sorteo'],
                    $_SESSION['serie'],
                    $billete,
                    $fraccion,
                    $row_id_programa_premios_antic->ID_PROGRAMA_PREMIOS_ANTIC,
                    'DU' . $_SESSION['dni'],
                    $escribano,
                ));

            $rs_seq = sql_kanban("	SELECT ID_PREMIOS_ANTICIPADA
									    FROM KANBAN.T_PREMIOS_ANTICIPADA
									    WHERE ID_PROGRAMA_PREMIOS_ANTIC = ?", array($row_id_programa_premios_antic->ID_PROGRAMA_PREMIOS_ANTIC));

            $rs_seq = $rs_seq->FetchNextObject($toupper = true);

            $importe_con_ley = ($row_premio->IMPORTE / 0.95);

            if ($row_premio->ID_TIPO_PREMIO == 2 || $row_premio->ID_TIPO_PREMIO == 1) {

                $rs = sql_kanban("SELECT ID_DESCRIPCION
									  from KANBAN.T_PREMIO_DESCRIPCION
									 WHERE DESCRIPCION LIKE 'PREMIO ANTICIPADO'");

                $rs_ID = $rs->FetchNextObject($toupper = true);

                $id_descripcion = $rs_ID->ID_DESCRIPCION;
                $especie        = null;
                if ((int) $row_premio->ID_TIPO_PREMIO == 1) {
                    $importe_con_ley = null;
                    $especie         = null;
                }

                $rs = sql_kanban("INSERT INTO KANBAN.T_PREMIOS (FRACCION,IMPORTE, ID_DESCRIPCION,BILLETE,ID_JUEGO,SORTEO,SERIE, CONCEPTO,SUC_BAN,NRO_AGEN,
														FECHA_ALTA,ID_SORTEO_ANTICIPADO,OCR,USUARIO,ESPECIE)
						VALUES (?,?,?,?,?,?,?,?,?,?,TO_DATE(?,'dd/mm/yyyy'),?,?,?,?)",
                    array(
                        $fraccion,
                        $importe_con_ley,
                        $id_descripcion,
                        $billete,
                        $_SESSION['id_juego'],
                        $_SESSION['sorteo'],
                        $_SESSION['serie'],
                        $row_premio->PREMIO,
                        $suc_ban,
                        $nro_agen,
                        $_POST['fecha_desde'],
                        $rs_seq->ID_PREMIOS_ANTICIPADA,
                        $rowValidacion->OCR,
                        'DU' . $_SESSION['dni'],
                        $row_premio->ID_DESCRIPCION_ESPECIA,
                    )
                );

                //PREMIO ESPECIE
                $importe      = null;
                $especie      = null;
                $desc_especie = null;

                //$db_kanban->debug = true;
                $rs_estimulo = null;
                if ((int) $row_premio->ID_TIPO_PREMIO == 1) {
                    $rs_estimulo = sql_kanban("		SELECT      PD.ORDEN,
                                                                PP.ID_DESCRIPCION,
    														    PD.DESCRIPCION
    														    ||' '
    														    ||PD.DESCRIPCION AS ESTIMULO,
    														    PP.ID_PREMIO_AFECTA_ESTIMULO,
    														    PP.PREMIO_EFECTIVO AS IMPORTE,
                                                                PP.PREMIO_ID_ESPECIAS,
                                                                TE.DESCRIPCION_ESPECIA
    													FROM 	KANBAN.T_PROGRAMA_PREMIOS PP,
    													  		KANBAN.T_PROGRAMA_PREMIOS_ANTIC PD,
                                                                KANBAN.T_DESCRIPCION_ESPECIAS TE
    													WHERE PP.ID_PREMIO_AFECTA_ESTIMULO  = ?
    													AND PP.ID_PREMIO_AFECTA_ESTIMULO 	= PD.ID_PROGRAMA_PREMIOS_ANTIC
                                                        AND PP.PREMIO_ID_ESPECIAS = TE.ID_DESCRIPCION_ESPECIA
    													AND PD.SORTEO                    	= ?
    													AND PD.ID_JUEGO                  	= ?
    													AND PD.SERIE                     	= ?
    													ORDER BY PD.SEMANA ASC",
                        array(
                            $row_id_programa_premios_antic->ID_PROGRAMA_PREMIOS_ANTIC,
                            $_SESSION['sorteo'],
                            $_SESSION['id_juego'],
                            $_SESSION['serie'],
                        )
                    );

                    $row_estimulo = $rs_estimulo->FetchNextObject($toupper = true);
                    $especie      = $row_estimulo->PREMIO_ID_ESPECIAS;
                    $desc_especie = $row_estimulo->DESCRIPCION_ESPECIA;
                } else {
                    $rs_estimulo = sql_kanban("    SELECT      PD.ORDEN,
                                                            PP.ID_DESCRIPCION,
                                                            PD.DESCRIPCION
                                                            ||' '
                                                            ||PD.DESCRIPCION AS ESTIMULO,
                                                            PP.ID_PREMIO_AFECTA_ESTIMULO,
                                                            PP.PREMIO_EFECTIVO AS IMPORTE,
                                                            PP.PREMIO_ID_ESPECIAS
                                                    FROM    KANBAN.T_PROGRAMA_PREMIOS PP,
                                                            KANBAN.T_PROGRAMA_PREMIOS_ANTIC PD
                                                    WHERE PP.ID_PREMIO_AFECTA_ESTIMULO  = ?
                                                    AND PP.ID_PREMIO_AFECTA_ESTIMULO    = PD.ID_PROGRAMA_PREMIOS_ANTIC
                                                    AND PD.SORTEO                       = ?
                                                    AND PD.ID_JUEGO                     = ?
                                                    AND PD.SERIE                        = ?
                                                    ORDER BY PD.SEMANA ASC",
                        array(
                            $row_id_programa_premios_antic->ID_PROGRAMA_PREMIOS_ANTIC,
                            $_SESSION['sorteo'],
                            $_SESSION['id_juego'],
                            $_SESSION['serie'],
                        )
                    );
                    $row_estimulo = $rs_estimulo->FetchNextObject($toupper = true);
                    $desc_especie = 'EFECTIVO';
                    $importe      = $row_estimulo->IMPORTE;
                }

                if ($rs_estimulo->RowCount() != 0) {

                    $rs = sql_kanban("	SELECT ID_DESCRIPCION
											FROM KANBAN.T_PREMIO_DESCRIPCION
											WHERE TIPO_PREMIO = 'ESTIMULO'");

                    $rs          = $rs->FetchNextObject($toupper = true);
                    $id_estimulo = $rs->ID_DESCRIPCION;

                    $rs = sql_kanban("INSERT INTO KANBAN.T_PREMIOS (FRACCION,IMPORTE, ID_DESCRIPCION,BILLETE,	ID_JUEGO,SORTEO,SERIE, CONCEPTO,SUC_BAN,NRO_AGEN,
														FECHA_ALTA,ID_SORTEO_ANTICIPADO,OCR,ESPECIE)
														VALUES (?,?,?,?,?,?,?,?,?,?,TO_DATE(?,'dd/mm/yyyy'),?,?,?)",
                        array(
                            $fraccion,
                            $importe,
                            $id_estimulo,
                            $billete,
                            $_SESSION['id_juego'],
                            $_SESSION['sorteo'],
                            $_SESSION['serie'],
                            'ESTIMULO - SEM. ' . $semana . ' - PREMIO Nº ' . $row_estimulo->ORDEN . ' - ' . $desc_especie,
                            $suc_ban,
                            $nro_agen,
                            $_POST['fecha_desde'],
                            $rs_seq->ID_PREMIOS_ANTICIPADA,
                            $rowValidacion->OCR,
                            $especie,
                        )
                    );

                }
            }
            sql("UPDATE SGS.T_ANTICIPADA_GANADORES
                SET EXPORTADO          ='SI'
                WHERE ID_JUEGO = ?
                AND SORTEO     = ?
                AND SEMANA     = ?
                AND ORDEN      = ?", array($_SESSION['id_juego'], $_SESSION['sorteo'], $semana, $orden));
            /* $db_kanban->RollbackTrans();
            $db->RollbackTrans();*/
            $db_kanban->CompleteTrans(true);
            $db->CompleteTrans(true);
            die(ok('La extracción fue exportada con exito al kanban, y se genero el premio con el estimulo correspondiente'));

        }
    } catch (exception $e) {
        error('Anticipados Error: -' . $db_kanban->ErrorMsg() . ' - ' . $db->ErrorMsg());
        die();
    }

}

if ($accion == 'exportar_quiniela_poceada') {

}
