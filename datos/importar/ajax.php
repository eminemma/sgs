<?php
/*
##43214##
Se quito el campo estado al traer el programa asociado al sorteo. Emmanuel Quattropani (10/08/2017)

##41208## Reunion 09/08/2017
Se importa el horario del sorteo recibido del kanban. Emmanuel Quattropani (10/08/2017)

##47029## Gordo Navidad 2017
Se modifica la importación final para tomar los datos por medio de dblink.
 */

error_reporting(E_ERROR);
ini_set('diplay_errors', 1);
session_start();
set_time_limit(0);
include_once dirname(__FILE__) . '/../../mensajes.php';
include_once dirname(__FILE__) . '/../../db.php';
include_once dirname(__FILE__) . '/../../db_kanban.php';

$accion = $_GET['accion'];
switch ($accion) {
    //IMPORTACION DE LA LOTERIA EXTRAORDINARIA
    case 'importar_loteria':
        try {
            $billetes   = isset($_GET['billetes']) ? $_GET['billetes'] : '';
            $solo_venta = (isset($_GET['solo_venta']) && $_GET['solo_venta'] != 'undefined') ? $_GET['solo_venta'] : 0;
            $sorteo     = $_SESSION['sorteo'];
            $id_juego   = $_SESSION['id_juego'];
            //IMPORTACION DE DATOS DEL SORTEO
            conectar_db();
            conectar_db_kanban();
            importar_datos_sorteo();

            // $db_kanban->debug = true;

            //CANTIDAD DE REGISTROS A MIGRAR
            $rs_venta_neta = sql_kanban("	SELECT SUM(CANTIDAD) AS CANTIDAD
						  				FROM (
												SELECT COUNT(*) AS CANTIDAD
												FROM  	KANBAN.T_REPARTO_INTELIGENTE R,
													  	JUEGOS.V_AGENCIA A,
													  	GESTION.T_SUCURSAL S,
                    									GESTION.T_PROVINCIA P
												WHERE 	R.SUC_BAN  		= 	A.SUC_BAN(+)
													AND R.NRO_AGEN 		= 	A.NRO_AGEN(+)
													AND R.SUC_BAN  		= 	S.ID_SUCURSAL(+)
                  									AND S.ID_PROVINCIA	=	P.ID_PROVINCIA(+)
													AND R.ID_JUEGO      =	$id_juego
													AND R.SORTEO        =	$sorteo
													AND R.AGENCIA       =	'S'
													AND (
														R.SUC_BAN_DEVUELVE  IS NULL
														OR R.NRO_AGEN_DEVUELVE IS NULL
													)
										UNION
										SELECT COUNT(*) AS CANTIDAD
										FROM 	KANBAN.T_REPARTO_INTELIGENTE R,
												GESTION.T_SUCURSAL S
										WHERE 	R.ID_JUEGO 	=	$id_juego
											AND R.SORTEO    =	$sorteo
											AND R.SUC_BAN   =	S.ID_SUCURSAL(+)
											AND R.VENTA_EMPLEADO IS NOT NULL
										UNION
										SELECT COUNT(*) AS CANTIDAD
										FROM 	KANBAN.T_REPARTO_INTELIGENTE R,
												GESTION.T_SUCURSAL S,
												GESTION.T_LOCALIDAD L,
												GESTION.T_PROVINCIA P
										WHERE R.SUC_BAN    		 =	S.ID_SUCURSAL(+)
											AND S.ID_PROVINCIA   =	P.ID_PROVINCIA(+)
											AND P.ID_PROVINCIA   =	L.ID_PROVINCIA(+)
											AND L.DEFECTO(+)     =	1
											AND R.ID_JUEGO       =	$id_juego
											AND R.SORTEO         =	$sorteo
											AND R.VENTA_CONTADO IS NOT NULL)");

            $row_venta_neta = siguiente_kanban($rs_venta_neta);
            $venta_neta     = $row_venta_neta->CANTIDAD;
            ComenzarTransaccion($db);
            info('CANTIDAD DE REGISTROS A MIGRAR: ' . $venta_neta . ' Fecha ' . date('d/m/Y H:i:s'));

            sql('DELETE FROM sgs.t_billetes_participantes WHERE SORTEO=? AND ID_JUEGO=?', array($sorteo, $id_juego));
            //SI EXISTE EL PREMIO EXTRAORDINARIO SORTEO POR ENTERO SE USA $sql_migrar_entero sino $sql_migrar
            $rs                  = sql_kanban('SELECT FRACCIONES FROM KANBAN.T_SORTEO WHERE SORTEO = ? AND ID_JUEGO = ? ', array($sorteo, $id_juego));
            $row_sorteo          = siguiente_kanban($rs);
            $cantidad_fracciones = (int) $row_sorteo->FRACCIONES;
            /*$db_kanban->debug    = true;*/
            $sql_migrar_entero = " INSERT /*+ APPEND */
										  INTO SGS.T_BILLETES_PARTICIPANTES
										    (
										      ID_JUEGO,
										      SORTEO,
										      BILLETE,
										      ID_SUCURSAL,
										      ID_AGENCIA,
										      DESCRIPCION_AGENCIA,
										      LOCALIDAD,
										      PROVINCIA,
										      FRACCION,
										      PROGRESION,
										      SERIE,
										      DESCRIPCION_SUCURSAL,
										      PARTICIPA_ENTERO,
										      MODALIDAD
										    )
										  			SELECT R.ID_JUEGO ID_JUEGO,
													       R.SORTEO SORTEO,
													       R.BILLETE BILLETE,
													       R.SUC_BAN SUC_BAN,
													       R.NRO_AGEN NRO_AGEN,
													       REPLACE(A.NOMBRE,'''','') DESCRIPCION_AGENCIA,
													       REPLACE(A.LOCALIDAD,'''','') LOCALIDAD,
													       DECODE(R.SUC_BAN,50,A.LOCALIDAD,P.DESCRIPCION) PROVINCIA,
													       R.FRACCION FRACCION,
													       R.PROGRESION PROGRESION,
													       R.SERIE SERIE,
													       A.DELEGACION DESCRIPCION_SUCURSAL,
													        Nvl((
													        SELECT DECODE(COUNT(TI.BILLETE),$cantidad_fracciones,'SI','NO')
													        FROM KANBAN.T_REPARTO_INTELIGENTE@KANBAN_ANTICIPADA TI
													        WHERE TI.BILLETE  = R.BILLETE
													          AND TI.SORTEO   = R.SORTEO
													          AND TI.ID_JUEGO = R.ID_JUEGO
													          AND TI.SUC_BAN  = R.SUC_BAN
													          AND TI.NRO_AGEN  = R.NRO_AGEN
													          AND TI.AGENCIA = 'S'
													          AND (TI.SUC_BAN_DEVUELVE IS NULL
													        	OR TI.NRO_AGEN_DEVUELVE  IS NULL )
													        GROUP BY BILLETE
													      ),'NO') AS PARTICIPA_ENTERO,
													       DECODE(OCR,NULL,'VENTA AGENCIA','BILLETE DIGITAL') AS MODALIDAD
													FROM KANBAN.T_REPARTO_INTELIGENTE@KANBAN_ANTICIPADA R,
													     JUEGOS.V_AGENCIA@KANBAN_ANTICIPADA A,
													     GESTION.T_SUCURSAL@KANBAN_ANTICIPADA S,
													     GESTION.T_PROVINCIA@KANBAN_ANTICIPADA P
													WHERE R.SUC_BAN          = A.SUC_BAN(+)
													  AND R.NRO_AGEN           = A.NRO_AGEN(+)
													  AND R.SUC_BAN            = S.ID_SUCURSAL(+)
													  AND S.ID_PROVINCIA       = P.ID_PROVINCIA(+)
													  AND R.ID_JUEGO           = $id_juego
													  AND R.SORTEO             = $sorteo
													  AND R.AGENCIA            = 'S'
													  AND (R.SUC_BAN_DEVUELVE IS NULL
													        OR R.NRO_AGEN_DEVUELVE  IS NULL )
													UNION
													SELECT  R.ID_JUEGO ID_JUEGO,
													        R.SORTEO SORTEO,
													        R.BILLETE BILLETE,
													        R.SUC_BAN SUC_BAN,
													        0 NRO_AGEN,
													        'VENTA CONTADO CASA CENTRAL' DESCRIPCION_AGENCIA,
													        NULL LOCALIDAD,
													        'CORDOBA' PROVINCIA,
													        R.FRACCION FRACCION,
													        R.PROGRESION PROGRESION,
													        R.SERIE SERIE,
													        S.DESCRIPCION DESCRIPCION_SUCURSAL,
														     Nvl((
													        SELECT DECODE(COUNT(TI.BILLETE),$cantidad_fracciones,'SI','NO')
													        FROM KANBAN.T_REPARTO_INTELIGENTE@KANBAN_ANTICIPADA TI
													        WHERE TI.BILLETE  = R.BILLETE
													          AND TI.SORTEO   = R.SORTEO
													          AND TI.ID_JUEGO = R.ID_JUEGO
													          AND TI.VENTA_EMPLEADO IS NOT NULL
													        GROUP BY BILLETE
													      ),'NO') AS PARTICIPA_ENTERO,
													      'VENTA CONTADO CASA CENTRAL' AS MODALIDAD
													FROM KANBAN.T_REPARTO_INTELIGENTE@KANBAN_ANTICIPADA R,
													  GESTION.T_SUCURSAL@KANBAN_ANTICIPADA S
													WHERE R.ID_JUEGO      	= $id_juego
													  AND R.SORTEO          = $sorteo
													  AND R.SUC_BAN         = S.ID_SUCURSAL(+)
													  AND R.VENTA_EMPLEADO IS NOT NULL
													UNION
													SELECT  R.ID_JUEGO ID_JUEGO,
													        R.SORTEO SORTEO,
													        R.BILLETE BILLETE,
													        R.SUC_BAN SUC_BAN,
													        0 NRO_AGEN,
													        'VENTA CONTADO' DESCRIPCION_AGENCIA,
													        L.DESCRIPCION LOCALIDAD,
													        P.DESCRIPCION PROVINCIA,
													        R.FRACCION FRACCION,
													        R.PROGRESION PROGRESION,
													        R.SERIE SERIE,
													        S.DESCRIPCION DESCRIPCION_SUCURSAL,
													        Nvl((
													        SELECT DECODE(COUNT(TI.BILLETE),$cantidad_fracciones,'SI','NO')
													        FROM KANBAN.T_REPARTO_INTELIGENTE@KANBAN_ANTICIPADA TI
													        WHERE TI.BILLETE  = R.BILLETE
													          AND TI.SORTEO   = R.SORTEO
													          AND TI.ID_JUEGO = R.ID_JUEGO
													          AND TI.SUC_BAN  = R.SUC_BAN
													          AND TI.VENTA_CONTADO IS NOT NULL
													        GROUP BY BILLETE
													      ),'NO') AS PARTICIPA_ENTERO,
													       'VENTA CONTADO' AS MODALIDAD
													FROM  KANBAN.T_REPARTO_INTELIGENTE@KANBAN_ANTICIPADA R,
													      GESTION.T_SUCURSAL@KANBAN_ANTICIPADA S,
													      GESTION.T_LOCALIDAD@KANBAN_ANTICIPADA L,
													      GESTION.T_PROVINCIA@KANBAN_ANTICIPADA P
													WHERE R.SUC_BAN      = S.ID_SUCURSAL(+)
													  AND S.ID_PROVINCIA   = P.ID_PROVINCIA(+)
													  AND P.ID_PROVINCIA   = L.ID_PROVINCIA(+)
													  AND L.DEFECTO(+)     = 1
													  AND R.ID_JUEGO       = $id_juego
													  AND R.SORTEO         = $sorteo
													  AND R.VENTA_CONTADO IS NOT NULL
										";

            sql($sql_migrar_entero);

            FinalizarTransaccion($db);
            //}

        } catch (exception $e) {
            error('Error en la base de datos' . $db->ErrorMsg());
            exit;
        }

        ok('Sorte :' . $sorteo . ', la Importacion finalizo con exito!');

        //Busco la cantidad de registros importados
        $rs_billetes_sgs  = sql("SELECT count(*) as CANTIDAD FROM SGS.T_BILLETES_PARTICIPANTES WHERE ID_JUEGO=? AND SORTEO=?", array($id_juego, $sorteo));
        $row_billetes_sgs = siguiente_kanban($rs_billetes_sgs);
        $migrados         = $row_billetes_sgs->CANTIDAD;
        if ($venta_neta != $migrados) {
            error('CANTIDAD DE REGISTROS MIGRADOS: ' . $migrados . '. La cantidad de registros no coincide. Por favor intente migrar nuevamente. Fecha ' . date('d/m/Y H:i:s'));
        } else {
            ok('CANTIDAD DE REGISTROS MIGRADOS: ' . $migrados . ' Fecha ' . date('d/m/Y H:i:s'));
        }

        break;

    case 'importar_quiniela':
        $billetes   = isset($_GET['billetes']) ? $_GET['billetes'] : '';
        $solo_venta = (isset($_GET['solo_venta']) && $_GET['solo_venta'] != 'undefined') ? $_GET['solo_venta'] : 0;

        //IMPORTACION DE DATOS DEL SORTEO (QUINIELA NO TIENE DISTRIBUCION VENTA DIRECTA EN LA AGENCIA)
        importar_datos_sorteo();
        break;
    case 'importar_loteria_anticipada':
        conectar_db();
        conectar_db_kanban();
        importar_datos_sorteo();
        $sorteo   = $_SESSION['sorteo'];
        $id_juego = $_SESSION['id_juego'];
        /*$db_kanban->debug = true;*/
        /*
        13-08-2015 CANTIDAD DE REGISTROS A MIGRAR VENTA ANTICIPADA y BILLETE DIGITAL
         */
        try {
            $rs_venta_neta = sql_kanban("    SELECT SUM(CANTIDAD) AS CANTIDAD FROM (

            SELECT     COUNT(*) AS CANTIDAD
            FROM     KANBAN.T_CUPON_ANTICIPADO TC,
            KANBAN.T_REPARTO_INTELIGENTE R,
            JUEGOS.V_AGENCIA A,
            GESTION.T_SUCURSAL S,
            GESTION.T_PROVINCIA P
            WHERE     TC.ID_REPARTO_INTELIGENTE=R.ID_REPARTO_INTELIGENTE
            AND R.SUC_BAN                  = A.SUC_BAN(+)
            AND R.NRO_AGEN                 = A.NRO_AGEN(+)
            AND R.SUC_BAN                  = S.ID_SUCURSAL(+)
            AND S.ID_PROVINCIA             = P.ID_PROVINCIA(+)
            AND R.ID_JUEGO                 = $id_juego
            AND R.SORTEO                   = $sorteo
            UNION
            SELECT COUNT(*) AS CANTIDAD
            FROM KANBAN.T_REPARTO_INTELIGENTE R,
            JUEGOS.V_AGENCIA A,
            GESTION.T_SUCURSAL S,
            GESTION.T_PROVINCIA P
            WHERE R.SUC_BAN          = A.SUC_BAN(+)
            AND R.NRO_AGEN           = A.NRO_AGEN(+)
            AND R.SUC_BAN            = S.ID_SUCURSAL(+)
            AND S.ID_PROVINCIA       = P.ID_PROVINCIA(+)
            AND R.ID_JUEGO           = $id_juego
            AND R.SORTEO             = $sorteo
            AND R.AGENCIA            = 'S'
            AND (R.SUC_BAN_DEVUELVE IS NULL
            OR R.NRO_AGEN_DEVUELVE  IS NULL )
            AND OCR IS NOT NULL
            UNION
            SELECT COUNT(*) AS CANTIDAD
            FROM    KANBAN.T_REPARTO_INTELIGENTE R,
            JUEGOS.V_AGENCIA A,
            GESTION.T_SUCURSAL S,
            GESTION.T_PROVINCIA P
            WHERE      R.SUC_BAN                  = A.SUC_BAN(+)
            AND R.NRO_AGEN                 = A.NRO_AGEN(+)
            AND R.SUC_BAN                  = S.ID_SUCURSAL(+)
            AND S.ID_PROVINCIA             = P.ID_PROVINCIA(+)
            AND R.ID_JUEGO                 = $id_juego
            AND R.SORTEO                   = $sorteo
            AND R.VENTA_EMPLEADO = 'S'
            AND R.OCR IS NOT NULL
            )");

            $row_venta_neta = siguiente_kanban($rs_venta_neta);
            $venta_neta     = $row_venta_neta->CANTIDAD;
            info('CANTIDAD DE REGISTROS A MIGRAR VENTA ANTICIPADA: ' . $venta_neta . ' Fecha ' . date('d/m/Y H:i:s'));

            //sql('BEGIN SGS.PR_IMPORTAR_ANTICIPADO(?,?);END;', array($sorteo, $id_juego));
            //
            sql("BEGIN
            	  --LIMPIAR DE BILLETES PARTICIPANTES
				  DELETE
				  FROM sgs.t_billetes_participantes
				  WHERE SORTEO=$sorteo
				  AND ID_JUEGO=$id_juego;
				  commit;
				  --LIMPIAR DE PROGRAMA DE ANTICIPADAS
				  DELETE
				  FROM SGS.T_ANTICIPADA
				  WHERE SORTEO = $sorteo
				  AND ID_JUEGO = $id_juego;
				  commit;
				  --CARGAR PROGRAMA DE ANTICIPADAS
				  INSERT /*+ APPEND */
				  INTO SGS.T_ANTICIPADA
				    (
				      ID_JUEGO,
				      SORTEO,
				      SEMANA,
				      PREMIO,
				      PRESCRIPCION,
				      PROX_SORTEO,
				      PREMIO_PROX_SORTEO,
				      IMPORTE,
				      ORDEN
				    )
				  SELECT TPA.ID_JUEGO,
				    TPA.SORTEO,
				    TPA.SEMANA,
				    TPA.DESCRIPCION,
				    TS.FECHA_HASTA_PAGO_PREMIO_MAX,
				    TO_CHAR(TPA.FECHA_PROXIMO_SORTEO,'DD/MM/YYYY') AS FECHA_PROXIMO_SORTEO,
				    (SELECT TPP.DESCRIPCION
				    FROM KANBAN.T_PROGRAMA_PREMIOS_ANTIC@KANBAN_ANTICIPADA TPP
				    WHERE TPP.ID_PROGRAMA_PREMIOS_ANTIC > TPA.ID_PROGRAMA_PREMIOS_ANTIC
				    AND TPP.SORTEO                      = TPA.SORTEO
				    AND TPP.SERIE                       = TPA.SERIE
				    AND TPP.ID_JUEGO                    = TPA.ID_JUEGO
				    AND ROWNUM                          = 1
				    ) AS DESCRIPCION,
				    TPA.IMPORTE,
				    TPA.ORDEN
				  FROM KANBAN.T_PROGRAMA_PREMIOS_ANTIC@KANBAN_ANTICIPADA TPA,
				    KANBAN.T_SORTEO@KANBAN_ANTICIPADA TS
				  WHERE TPA.ID_PROGRAMA_PREMIOS_ANTIC = TPA.ID_PROGRAMA_PREMIOS_ANTIC
				  AND TPA.SORTEO                      = $sorteo
				  AND TPA.ID_JUEGO                    = $id_juego
				  AND TPA.SORTEO                      = TS.SORTEO
				  AND TPA.ID_JUEGO                    = TS.ID_JUEGO
				  AND TPA.DESCRIPCION NOT LIKE '%ESTIMULO%';
				  commit;
				  --CARGAR BILLETES PARTICIPANTES DEL SORTEO
				  INSERT /*+ APPEND */
				  INTO SGS.T_BILLETES_PARTICIPANTES
				    (
				      ID_JUEGO,
				      SORTEO,
				      BILLETE,
				      ID_SUCURSAL,
				      ID_AGENCIA,
				      DESCRIPCION_AGENCIA,
				      LOCALIDAD,
				      PROVINCIA,
				      FRACCION,
				      PROGRESION,
				      SERIE,
				      DESCRIPCION_SUCURSAL,
				      MODALIDAD
				    )
				  SELECT R.ID_JUEGO ID_JUEGO,
				    R.SORTEO SORTEO,
				    R.BILLETE BILLETE,
				    R.SUC_BAN SUC_BAN,
				    R.NRO_AGEN NRO_AGEN,
				    REPLACE(A.NOMBRE,'''','') DESCRIPCION_AGENCIA,
				    REPLACE(A.LOCALIDAD,'''','') LOCALIDAD,
				    DECODE(R.SUC_BAN,50,A.LOCALIDAD,P.DESCRIPCION) PROVINCIA,
				    R.FRACCION FRACCION,
				    R.PROGRESION PROGRESION,
				    R.SERIE SERIE,
				    S.DESCRIPCION DESCRIPCION_SUCURSAL,
				    CASE
				      WHEN R.VENTA_EMPLEADO = 'S'
				      THEN 'VENTA CONTADO CASA CENTRAL'
				      WHEN R.VENTA_CONTADO = 'S'
				      THEN 'VENTA CONTADO'
				      ELSE 'VENTA AGENCIA'
				    END AS MODALIDAD
				  FROM KANBAN.T_CUPON_ANTICIPADO@KANBAN_ANTICIPADA TC,
				    KANBAN.T_REPARTO_INTELIGENTE@KANBAN_ANTICIPADA R,
				    JUEGOS.V_AGENCIA@KANBAN_ANTICIPADA A,
				    GESTION.T_SUCURSAL@KANBAN_ANTICIPADA S,
				    GESTION.T_PROVINCIA@KANBAN_ANTICIPADA P
				  WHERE TC.ID_REPARTO_INTELIGENTE=R.ID_REPARTO_INTELIGENTE
				  AND R.SUC_BAN                  = A.SUC_BAN(+)
				  AND R.NRO_AGEN                 = A.NRO_AGEN(+)
				  AND R.SUC_BAN                  = S.ID_SUCURSAL(+)
				  AND S.ID_PROVINCIA             = P.ID_PROVINCIA(+)
				  AND R.ID_JUEGO                 = $id_juego
				  AND R.SORTEO                   = $sorteo
				  AND R.SERIE                    =1
				  UNION
				  SELECT R.ID_JUEGO ID_JUEGO,
				    R.SORTEO SORTEO,
				    R.BILLETE BILLETE,
				    R.SUC_BAN SUC_BAN,
				    R.NRO_AGEN NRO_AGEN,
				    REPLACE(A.NOMBRE,'''','') DESCRIPCION_AGENCIA,
				    REPLACE(A.LOCALIDAD,'''','') LOCALIDAD,
				    DECODE(R.SUC_BAN,50,A.LOCALIDAD,P.DESCRIPCION) PROVINCIA,
				    R.FRACCION FRACCION,
				    R.PROGRESION PROGRESION,
				    R.SERIE SERIE,
				    S.DESCRIPCION DESCRIPCION_SUCURSAL,
				    CASE
				      WHEN R.VENTA_EMPLEADO = 'S'
				      THEN 'VENTA CONTADO CASA CENTRAL'
				      WHEN R.VENTA_CONTADO = 'S'
				      THEN 'VENTA CONTADO'
				      ELSE 'VENTA AGENCIA'
				    END AS MODALIDAD
				  FROM KANBAN.T_REPARTO_INTELIGENTE@KANBAN_ANTICIPADA R,
				    JUEGOS.V_AGENCIA@KANBAN_ANTICIPADA A,
				    GESTION.T_SUCURSAL@KANBAN_ANTICIPADA S,
				    GESTION.T_PROVINCIA@KANBAN_ANTICIPADA P
				  WHERE R.SUC_BAN      = A.SUC_BAN(+)
				  AND R.NRO_AGEN       = A.NRO_AGEN(+)
				  AND R.SUC_BAN        = S.ID_SUCURSAL(+)
				  AND S.ID_PROVINCIA   = P.ID_PROVINCIA(+)
				  AND R.ID_JUEGO       = $id_juego
				  AND R.SORTEO         = $sorteo
				  AND R.SERIE                    =1
				  AND R.VENTA_EMPLEADO = 'S'
				  AND R.OCR           IS NOT NULL
				  UNION
				  SELECT R.ID_JUEGO ID_JUEGO,
				    R.SORTEO SORTEO,
				    R.BILLETE BILLETE,
				    R.SUC_BAN SUC_BAN,
				    R.NRO_AGEN NRO_AGEN,
				    REPLACE(A.NOMBRE,'''','') DESCRIPCION_AGENCIA,
				    REPLACE(A.LOCALIDAD,'''','') LOCALIDAD,
				    DECODE(R.SUC_BAN,50,A.LOCALIDAD,P.DESCRIPCION) PROVINCIA,
				    R.FRACCION FRACCION,
				    R.PROGRESION PROGRESION,
				    R.SERIE SERIE,
				    A.DELEGACION DESCRIPCION_SUCURSAL,
				    'BILLETE DIGITAL' AS MODALIDAD
				  FROM KANBAN.T_REPARTO_INTELIGENTE@KANBAN_ANTICIPADA R,
				    JUEGOS.V_AGENCIA@KANBAN_ANTICIPADA A,
				    GESTION.T_SUCURSAL@KANBAN_ANTICIPADA S,
				    GESTION.T_PROVINCIA@KANBAN_ANTICIPADA P
				  WHERE R.SUC_BAN          = A.SUC_BAN(+)
				  AND R.NRO_AGEN           = A.NRO_AGEN(+)
				  AND R.SUC_BAN            = S.ID_SUCURSAL(+)
				  AND S.ID_PROVINCIA       = P.ID_PROVINCIA(+)
				  AND R.ID_JUEGO           = $id_juego
				  AND R.SORTEO             = $sorteo
				  AND R.SERIE                    =1
				  AND R.AGENCIA            = 'S'
				  AND (R.SUC_BAN_DEVUELVE IS NULL
				  OR R.NRO_AGEN_DEVUELVE  IS NULL )
				  AND R.OCR               IS NOT NULL
				  AND OCR                 IS NOT NULL;
				  commit;END;");

        } catch (exception $e) {
            error('Error en la base de datos' . $db->ErrorMsg());
            exit;
        }
        ok('Sorteo: ' . $sorteo . ', la Importacion finalizo con exito! Fecha ' . date('d/m/Y H:i:s'));

        break;
    case 'importar_quiniela_poceada':
        $billetes   = isset($_GET['billetes']) ? $_GET['billetes'] : '';
        $solo_venta = (isset($_GET['solo_venta']) && $_GET['solo_venta'] != 'undefined') ? $_GET['solo_venta'] : 0;
        $sorteo     = $_SESSION['sorteo'];
        $id_juego   = $_SESSION['id_juego'];

        $rs = sql("SELECT
				    TOTAL_PREMIOS_8_ACIERTOS,
    				TOTAL_PREMIOS_7_ACIERTOS,
    				TOTAL_PREMIOS_6_ACIERTOS,
    				TOTAL_PREMIOS_5_ACIERTOS
			FROM
    			KANBAN.T_TT_RECAUDACION@KANBAN_ANTICIPADA
    		WHERE SORTEO = $sorteo
    		AND ID_JUEGO = $id_juego");

        if ($rs->RecordCount() == 0) {
            die(error('Es necesario definir los premios para cada categoria de premios antes de importar'));
        }

        if ($rs->RecordCount() > 0) {
            $row = siguiente($rs);
            if ($row->TOTAL_PREMIOS_8_ACIERTOS == '0' || $row->TOTAL_PREMIOS_7_ACIERTOS == '0' || $row->TOTAL_PREMIOS_6_ACIERTOS == '0' || $row->TOTAL_PREMIOS_5_ACIERTOS == '0') {
                die(error('Es necesario definir los premios para cada categoria de premios antes de importar'));
            }
        }

        importar_datos_sorteo();

        importar_extracto_quiniela_asociada();
        break;
    default:
        die('Accion no complentada en importacion');
        break;
}

/*

$sql_migrar = "SELECT 'INSERT
INTO SGS.T_BILLETES_PARTICIPANTES
(
ID_JUEGO,
SORTEO,
BILLETE,
ID_SUCURSAL,
ID_AGENCIA,
DESCRIPCION_AGENCIA,
LOCALIDAD,
PROVINCIA,
FRACCION,
PROGRESION,
SERIE,
DESCRIPCION_SUCURSAL
)
VALUES
('||ID_JUEGO||','||SORTEO||','||BILLETE||','||DECODE(SUC_BAN,NULL,'NULL',SUC_BAN)||','||NRO_AGEN||','''||DESCRIPCION_AGENCIA||''','''||LOCALIDAD||''','''||PROVINCIA||''','||FRACCION||','||PROGRESION||','||SERIE||','''||DESCRIPCION_SUCURSAL||''')' AS EXEC
FROM (
SELECT     R.ID_JUEGO ID_JUEGO,
R.SORTEO SORTEO,
R.BILLETE BILLETE,
R.SUC_BAN SUC_BAN,
R.NRO_AGEN NRO_AGEN,
REPLACE(A.NOMBRE,'''','') DESCRIPCION_AGENCIA,
replace(A.LOCALIDAD,'''','') LOCALIDAD,
DECODE(R.SUC_BAN,50,A.LOCALIDAD,P.DESCRIPCION) PROVINCIA,
R.FRACCION FRACCION,
R.PROGRESION PROGRESION,
R.SERIE SERIE,
A.DELEGACION DESCRIPCION_SUCURSAL
FROM     KANBAN.T_REPARTO_INTELIGENTE R,
JUEGOS.V_AGENCIA A,
GESTION.T_SUCURSAL S,
GESTION.T_PROVINCIA P
WHERE     R.SUC_BAN         =    A.SUC_BAN(+)
AND R.NRO_AGEN      =    A.NRO_AGEN(+)
AND R.SUC_BAN        =     S.ID_SUCURSAL(+)
AND S.ID_PROVINCIA    =    P.ID_PROVINCIA(+)
AND R.ID_JUEGO      =    ?
AND R.SORTEO        =    ?
AND R.AGENCIA       =    'S'
AND (R.SUC_BAN_DEVUELVE  IS NULL
OR R.NRO_AGEN_DEVUELVE IS NULL
)
UNION
SELECT     R.ID_JUEGO ID_JUEGO,
R.SORTEO SORTEO,
R.BILLETE BILLETE,
R.SUC_BAN SUC_BAN,
0 NRO_AGEN,
'VENTA MOSTRADOR' DESCRIPCION_AGENCIA,
NULL LOCALIDAD,
'CORDOBA' PROVINCIA,
R.FRACCION FRACCION,
R.PROGRESION PROGRESION,
R.SERIE SERIE,
S.DESCRIPCION DESCRIPCION_SUCURSAL
FROM     KANBAN.T_REPARTO_INTELIGENTE R,
GESTION.T_SUCURSAL S
WHERE     R.ID_JUEGO     =    ?
AND R.SORTEO        =     ?
AND R.SUC_BAN       =    S.ID_SUCURSAL(+)
AND R.VENTA_EMPLEADO IS NOT NULL
UNION
SELECT     R.ID_JUEGO ID_JUEGO,
R.SORTEO SORTEO,
R.BILLETE BILLETE,
R.SUC_BAN SUC_BAN,
0 NRO_AGEN,
'VENTA CONTADO' DESCRIPCION_AGENCIA,
L.DESCRIPCION LOCALIDAD,
P.DESCRIPCION PROVINCIA,
R.FRACCION FRACCION,
R.PROGRESION PROGRESION,
R.SERIE SERIE,
S.DESCRIPCION DESCRIPCION_SUCURSAL
FROM     KANBAN.T_REPARTO_INTELIGENTE R,
GESTION.T_SUCURSAL S,
GESTION.T_LOCALIDAD L,
GESTION.T_PROVINCIA P
WHERE R.SUC_BAN         =    S.ID_SUCURSAL(+)
AND S.ID_PROVINCIA  =    P.ID_PROVINCIA(+)
AND P.ID_PROVINCIA  =    L.ID_PROVINCIA(+)
AND L.DEFECTO(+)    =    1
AND R.ID_JUEGO      =    ?
AND R.SORTEO        =    ?
AND R.VENTA_CONTADO IS NOT NULL)
";

 */

function importar_datos_sorteo()
{
    global $db_kanban;
    global $db;
    global $sorteo_kanban;

    if (!isset($db)) {
        conectar_db();
    }

    if (!isset($db_kanban)) {
        conectar_db_kanban();
    }
//    $db->debug        = true;
    //var_dump($db);
    ComenzarTransaccion($db);
    $sorteo     = $_SESSION['sorteo'];
    $id_juego   = $_SESSION['id_juego'];
    $solo_venta = (isset($_GET['solo_venta']) && $_GET['solo_venta'] != 'undefined') ? $_GET['solo_venta'] : 0;
    try {

        if ($solo_venta == 0) {

            $rs_sorteo_kanban = sql_kanban("	SELECT 	ID_SORTEO,to_char(FECHA_SORTEO,'YYYY-MM-DD HH24:MI:SS') AS FECHA_SORTEO,FECHA_BAJA,ID_ESCRIBANO,
            											USUARIO_JEFE_SORTEO,USUARIO_OPERADOR,ID_PROGRAMA,PRIMER_ELEMENTO,
            											ULTIMO_ELEMENTO,FRACCIONES,CANTIDAD_SORTEOS_FECHA,SORTEO_UNICO,DESCRIPCION,
            											MONTO_FRACCION,decode(id_juego,1,FECHA_HASTA_PAGO_PREMIO_MAX,FECHA_HASTA_PAGO_PREMIO) as FECHA_HASTA_PAGO_PREMIO_MAX,QUINIELA_ASOC
									  	FROM KANBAN.T_SORTEO
										 WHERE SORTEO=? AND ID_JUEGO=?", array($sorteo, $id_juego));

            $row_sorteo_kanban = siguiente_kanban($rs_sorteo_kanban);
            if ($rs_sorteo_kanban->RecordCount() == 0) {
                die(error('El sorteo seleccionado no existe en KANBAN'));
            }

            $sorteo_kanban['id_sorteo']               = $row_sorteo_kanban->ID_SORTEO;
            $sorteo_kanban['fecha_sorteo']            = $row_sorteo_kanban->FECHA_SORTEO;
            $sorteo_kanban['fecha_baja']              = $row_sorteo_kanban->FECHA_BAJA;
            $sorteo_kanban['id_escribano']            = $row_sorteo_kanban->ID_ESCRIBANO;
            $sorteo_kanban['id_jefe']                 = $row_sorteo_kanban->USUARIO_JEFE_SORTEO;
            $sorteo_kanban['id_operador']             = $row_sorteo_kanban->USUARIO_OPERADOR;
            $sorteo_kanban['id_programa']             = $row_sorteo_kanban->ID_PROGRAMA;
            $sorteo_kanban['primer_elemento']         = $row_sorteo_kanban->PRIMER_ELEMENTO;
            $sorteo_kanban['ultimo_elemento']         = $row_sorteo_kanban->ULTIMO_ELEMENTO;
            $sorteo_kanban['fracciones']              = $row_sorteo_kanban->FRACCIONES;
            $sorteo_kanban['progresion']              = $row_sorteo_kanban->PROGRESION;
            $sorteo_kanban['cantidad_sorteos_fecha']  = $row_sorteo_kanban->CANTIDAD_SORTEOS_FECHA;
            $sorteo_kanban['sorteo_unico']            = $row_sorteo_kanban->SORTEO_UNICO;
            $sorteo_kanban['descripcion']             = $row_sorteo_kanban->DESCRIPCION;
            $sorteo_kanban['monto_fraccion']          = $row_sorteo_kanban->MONTO_FRACCION;
            $sorteo_kanban['fecha_hasta_pago_premio'] = $row_sorteo_kanban->FECHA_HASTA_PAGO_PREMIO_MAX;
            $sorteo_kanban['quiniela_asoc']           = $row_sorteo_kanban->QUINIELA_ASOC;

            //T_PROGRAMA
            //Traigo los datos del Programa desde Kanban
            $programa_kanban    = array();
            $rs_programa_kanban = sql_kanban('SELECT *
									  FROM KANBAN.T_PROGRAMAS
									  WHERE ID_PROGRAMA=?', array($sorteo_kanban['id_programa']));

            $row_programa_kanban = siguiente_kanban($rs_programa_kanban);

            $programa_kanban['id_programa']      = $row_programa_kanban->ID_PROGRAMA;
            $programa_kanban['fecha']            = $row_programa_kanban->FECHA;
            $programa_kanban['codigo_tipo']      = $row_programa_kanban->CODIGO_TIPO;
            $programa_kanban['estado']           = $row_programa_kanban->ESTADO;
            $programa_kanban['fecha_baja']       = $row_programa_kanban->FECHA_BAJA;
            $programa_kanban['usuario_baja']     = $row_programa_kanban->USUARIO_BAJA;
            $programa_kanban['descripcion']      = $row_programa_kanban->DESCRIPCION;
            $programa_kanban['primer_elemento']  = $row_programa_kanban->PRIMER_ELEMENTO;
            $programa_kanban['ultimo_elemento']  = $row_programa_kanban->ULTIMO_ELEMENTO;
            $programa_kanban['cantidad_numeros'] = $row_programa_kanban->CANTIDAD_NUMEROS;
            if ($programa_kanban['id_programa'] == null) {
                die(error('El sorteo no posee programa de Premios en KANBAN'));
            }

            //Busco si existe el Programa en el SGS
            $rs_programa_sgs = sql('SELECT *
									  FROM SGS.T_PROGRAMA
									  WHERE ID_PROGRAMA=?', array($programa_kanban['id_programa']));
            if ($rs_programa_sgs->RowCount() > 0) {
                sql("UPDATE SGS.T_PROGRAMA SET FECHA          =?,
										  CODIGO_TIPO_JUEGO=?,
										  FECHA_BAJA       =?,
										  USUARIO_BAJA     =?,
										  DESCRIPCION      =?,
										  PRIMER_ELEMENTO  =?,
										  ULTIMO_ELEMENTO  =?,
										  CANTIDAD_NUMEROS =?
										  WHERE ID_PROGRAMA=?", array($programa_kanban['fecha'],
                    $programa_kanban['codigo_tipo'],
                    $programa_kanban['fecha_baja'],
                    $programa_kanban['usuario_baja'],
                    $programa_kanban['descripcion'],
                    $programa_kanban['primer_elemento'],
                    $programa_kanban['ultimo_elemento'],
                    $programa_kanban['cantidad_numeros'],
                    $programa_kanban['id_programa']));} else {
                sql("INSERT INTO SGS.T_PROGRAMA
				  (
					ID_PROGRAMA,
					FECHA,
					ID_JUEGO,
					CODIGO_TIPO_JUEGO,
					ESTADO,
					FECHA_BAJA,
					USUARIO_BAJA,
					DESCRIPCION,
					PRIMER_ELEMENTO,
					ULTIMO_ELEMENTO,
					CANTIDAD_NUMEROS
				  )
				  VALUES
				  (
					?,
					?,
					?,
					?,
					?,
					?,
					?,
					?,
					?,
					?,
					?
				  )", array($programa_kanban['id_programa'],
                    $programa_kanban['fecha'],
                    $id_juego,
                    $programa_kanban['codigo_tipo'],
                    $programa_kanban['estado'],
                    $programa_kanban['fecha_baja'],
                    $programa_kanban['usuario_baja'],
                    $programa_kanban['descripcion'],
                    $programa_kanban['primer_elemento'],
                    $programa_kanban['ultimo_elemento'],
                    $programa_kanban['cantidad_numeros']));}

            // T_PREMIO_DESCRIPCION
            $rs_premio_desc_kanban = sql_kanban("SELECT *
										  FROM KANBAN.T_PREMIO_DESCRIPCION");
            while ($row_premio_desc_kanban = siguiente($rs_premio_desc_kanban)) {
                $rs_premio_desc_sgs = sql("SELECT * FROM SGS.T_PREMIO_DESCRIPCION WHERE ID_PREMIO_DESC=?", array($row_premio_desc_kanban->ID_DESCRIPCION));
                if ($rs_premio_desc_sgs->RowCount() > 0) {
                    sql("UPDATE SGS.T_PREMIO_DESCRIPCION SET DESCRIPCION=? WHERE ID_PREMIO_DESC=?", array($row_premio_desc_kanban->DESCRIPCION, $row_premio_desc_kanban->ID_DESCRIPCION));
                } else {
                    sql("INSERT INTO SGS.T_PREMIO_DESCRIPCION (ID_PREMIO_DESC,DESCRIPCION) VALUES(?,?)", array($row_premio_desc_kanban->ID_DESCRIPCION, $row_premio_desc_kanban->DESCRIPCION));
                }
            }

            // T_JUEGO_TIPOS
            //$db->debug=true;
            $rs_juego_tipos_kanban = sql_kanban("SELECT *
										  FROM KANBAN.T_JUEGO_TIPOS
										  WHERE ID_JUEGO=?", array($id_juego));
            while ($row_juegos_tipo_kanban = siguiente($rs_juego_tipos_kanban)) {

                $rs_juegos_tipo_sgs = sql("SELECT * FROM SGS.T_JUEGO_TIPO WHERE ID_JUEGO=? AND CODIGO_TIPO_JUEGO=?", array($id_juego, $row_juegos_tipo_kanban->CODIGO_TIPO));
                if ($rs_juegos_tipo_sgs->RowCount() > 0) {
                    sql("UPDATE SGS.T_JUEGO_TIPO SET DESCRIPCION=? WHERE ID_JUEGO=? AND CODIGO_TIPO_JUEGO=?", array($row_juegos_tipo_kanban->DESCRIPCION, $id_juego, $row_juegos_tipo_kanban->CODIGO_TIPO));
                } else {
                    sql("INSERT INTO SGS.T_JUEGO_TIPO (ID_JUEGO,CODIGO_TIPO_JUEGO,DESCRIPCION) VALUES(?,?,?)", array($id_juego, $row_juegos_tipo_kanban->CODIGO_TIPO, $row_juegos_tipo_kanban->DESCRIPCION));
                }
            }
            //$db->debug=false;
            //$db->debug=true;
            //T_DESCRIPCION_ESPECIAS
            $rs_desc_especias_kanban = sql_kanban("SELECT *
										  FROM KANBAN.T_DESCRIPCION_ESPECIAS");
            while ($row_desc_especias_kanban = siguiente($rs_desc_especias_kanban)) {
                $rs_desc_especias_sgs = sql("SELECT * FROM SGS.T_DESCRIPCION_ESPECIAS WHERE ID_DESCRIPCION_ESPECIA=?", array($row_desc_especias_kanban->ID_DESCRIPCION_ESPECIA));
                if ($rs_desc_especias_sgs->RowCount() > 0) {
                    sql("UPDATE SGS.T_DESCRIPCION_ESPECIAS SET DESCRIPCION_ESPECIA=? WHERE ID_DESCRIPCION_ESPECIA=?", array(utf8_decode($row_desc_especias_kanban->DESCRIPCION_ESPECIA), $row_desc_especias_kanban->ID_DESCRIPCION_ESPECIA));
                } else {
                    sql("INSERT INTO SGS.T_DESCRIPCION_ESPECIAS (ID_DESCRIPCION_ESPECIA,DESCRIPCION_ESPECIA) VALUES(?,?)", array($row_desc_especias_kanban->ID_DESCRIPCION_ESPECIA, utf8_decode($row_desc_especias_kanban->DESCRIPCION_ESPECIA)));
                }
            }
            //T_PROGRAMA_PREMIOS
            $programa_premios_kanban = array();
            $rs_prog_premios_kanban  = sql_kanban("SELECT *
										  FROM KANBAN.T_PROGRAMA_PREMIOS
										  WHERE ID_PROGRAMA=?
										  AND ID_DESCRIPCION <> 66", array($sorteo_kanban['id_programa']));

            while ($row_prog_premios_kanban = siguiente($rs_prog_premios_kanban)) {

                $programa_premios_kanban['id_descripcion']                 = $row_prog_premios_kanban->ID_DESCRIPCION;
                $programa_premios_kanban['premio_efectivo']                = $row_prog_premios_kanban->PREMIO_EFECTIVO;
                $programa_premios_kanban['aprox_anterior']                 = $row_prog_premios_kanban->APROX_ANTERIOR;
                $programa_premios_kanban['aprox_posterior']                = $row_prog_premios_kanban->APROX_POSTERIOR;
                $programa_premios_kanban['cuatro_cifras']                  = $row_prog_premios_kanban->CUATRO_CIFRAS;
                $programa_premios_kanban['tres_cifras']                    = $row_prog_premios_kanban->TRES_CIFRAS;
                $programa_premios_kanban['dos_cifras']                     = $row_prog_premios_kanban->DOS_CIFRAS;
                $programa_premios_kanban['una_cifras']                     = $row_prog_premios_kanban->UNA_CIFRAS;
                $programa_premios_kanban['progresion']                     = $row_prog_premios_kanban->PROGRESION;
                $programa_premios_kanban['dos_ultimas_cifras']             = $row_prog_premios_kanban->DOS_ULTIMAS_CIFRAS;
                $programa_premios_kanban['sale_o_sale']                    = $row_prog_premios_kanban->SALE_O_SALE;
                $programa_premios_kanban['afecta']                         = $row_prog_premios_kanban->AFECTA;
                $programa_premios_kanban['premio_id_especias']             = $row_prog_premios_kanban->PREMIO_ID_ESPECIAS;
                $programa_premios_kanban['aprox_anterior_id_especias']     = $row_prog_premios_kanban->APROX_ANTERIOR_ID_ESPECIAS;
                $programa_premios_kanban['aprox_posterior_id_especias']    = $row_prog_premios_kanban->APROX_POSTERIOR_ID_ESPECIAS;
                $programa_premios_kanban['cuatro_cifras_id_especias']      = $row_prog_premios_kanban->CUATRO_CIFRAS_ID_ESPECIAS;
                $programa_premios_kanban['tres_cifras_id_especias']        = $row_prog_premios_kanban->TRES_CIFRAS_ID_ESPECIAS;
                $programa_premios_kanban['dos_cifras_id_especias']         = $row_prog_premios_kanban->DOS_CIFRAS_ID_ESPECIAS;
                $programa_premios_kanban['una_cifras_id_especias']         = $row_prog_premios_kanban->UNA_CIFRAS_ID_ESPECIAS;
                $programa_premios_kanban['progresion_id_especias']         = $row_prog_premios_kanban->PROGRESION_ID_ESPECIAS;
                $programa_premios_kanban['dos_ultimas_cifras_id_especias'] = $row_prog_premios_kanban->DOS_ULTIMAS_CIFRAS_ID_ESPECIAS;
                $programa_premios_kanban['tipo_premio']                    = $row_prog_premios_kanban->TIPO_PREMIO;
                $programa_premios_kanban['procentaje']                     = $row_prog_premios_kanban->PORCENTAJE;

                //$db->debug = true;
                $rs_prog_premios_sgs = sql("SELECT * FROM SGS.T_PROGRAMA_PREMIOS WHERE ID_PROGRAMA=? AND ID_DESCRIPCION=?", array($sorteo_kanban['id_programa'], $programa_premios_kanban['id_descripcion']));

                if ($rs_prog_premios_sgs->RowCount() > 0) {
                    sql("UPDATE SGS.T_PROGRAMA_PREMIOS SET ID_DESCRIPCION=?,
														PREMIO_EFECTIVO=?,
														APROX_ANTERIOR=?,
														APROX_POSTERIOR=?,
														CUATRO_CIFRAS=?,
														TRES_CIFRAS=?,
														DOS_CIFRAS=?,
														UNA_CIFRAS=?,
														PROGRESION=?,
														DOS_ULTIMAS_CIFRAS=?,
														SALE_O_SALE=?,
														AFECTA=?,
														PREMIO_ID_ESPECIAS=?,
														APROX_ANTERIOR_ID_ESPECIAS=?,
														APROX_POSTERIOR_ID_ESPECIAS=?,
														CUATRO_CIFRAS_ID_ESPECIAS=?,
														TRES_CIFRAS_ID_ESPECIAS=?,
														DOS_CIFRAS_ID_ESPECIAS=?,
														UNA_CIFRAS_ID_ESPECIAS=?,
														PROGRESION_ID_ESPECIAS=?,
														DOS_ULTIMAS_CIFRAS_ID_ESPECIAS=?,
														TIPO_PREMIO=?,
														PORCENTAJE = ?
														WHERE ID_PROGRAMA=? AND ID_DESCRIPCION=?", array($programa_premios_kanban['id_descripcion'],
                        $programa_premios_kanban['premio_efectivo'],
                        $programa_premios_kanban['aprox_anterior'],
                        $programa_premios_kanban['aprox_posterior'],
                        $programa_premios_kanban['cuatro_cifras'],
                        $programa_premios_kanban['tres_cifras'],
                        $programa_premios_kanban['dos_cifras'],
                        $programa_premios_kanban['una_cifras'],
                        $programa_premios_kanban['progresion'],
                        $programa_premios_kanban['dos_ultimas_cifras'],
                        $programa_premios_kanban['sale_o_sale'],
                        $programa_premios_kanban['afecta'],
                        $programa_premios_kanban['premio_id_especias'],
                        $programa_premios_kanban['aprox_anterior_id_especias'],
                        $programa_premios_kanban['aprox_posterior_id_especias'],
                        $programa_premios_kanban['cuatro_cifras_id_especias'],
                        $programa_premios_kanban['tres_cifras_id_especias'],
                        $programa_premios_kanban['dos_cifras_id_especias'],
                        $programa_premios_kanban['una_cifras_id_especias'],
                        $programa_premios_kanban['progresion_id_especias'],
                        $programa_premios_kanban['dos_ultimas_cifras_id_especias'],
                        $programa_premios_kanban['tipo_premio'],
                        $programa_premios_kanban['procentaje'],
                        $sorteo_kanban['id_programa'], $programa_premios_kanban['id_descripcion']));
                } else {
                    //$db->debug = true;
                    sql("INSERT INTO SGS.T_PROGRAMA_PREMIOS (ID_PROGRAMA,
														ID_DESCRIPCION,
														PREMIO_EFECTIVO,
														APROX_ANTERIOR,
														APROX_POSTERIOR,
														CUATRO_CIFRAS,
														TRES_CIFRAS,
														DOS_CIFRAS,
														UNA_CIFRAS,
														PROGRESION,
														DOS_ULTIMAS_CIFRAS,
														SALE_O_SALE,
														AFECTA,
														PREMIO_ID_ESPECIAS,
														APROX_ANTERIOR_ID_ESPECIAS,
														APROX_POSTERIOR_ID_ESPECIAS,
														CUATRO_CIFRAS_ID_ESPECIAS,
														TRES_CIFRAS_ID_ESPECIAS,
														DOS_CIFRAS_ID_ESPECIAS,
														UNA_CIFRAS_ID_ESPECIAS,
														PROGRESION_ID_ESPECIAS,
														DOS_ULTIMAS_CIFRAS_ID_ESPECIAS,
														TIPO_PREMIO,
														PORCENTAJE
														) VALUES
														(?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)", array($sorteo_kanban['id_programa'],
                        $programa_premios_kanban['id_descripcion'],
                        $programa_premios_kanban['premio_efectivo'],
                        $programa_premios_kanban['aprox_anterior'],
                        $programa_premios_kanban['aprox_posterior'],
                        $programa_premios_kanban['cuatro_cifras'],
                        $programa_premios_kanban['tres_cifras'],
                        $programa_premios_kanban['dos_cifras'],
                        $programa_premios_kanban['una_cifras'],
                        $programa_premios_kanban['progresion'],
                        $programa_premios_kanban['dos_ultimas_cifras'],
                        $programa_premios_kanban['sale_o_sale'],
                        $programa_premios_kanban['afecta'],
                        $programa_premios_kanban['premio_id_especias'],
                        $programa_premios_kanban['aprox_anterior_id_especias'],
                        $programa_premios_kanban['aprox_posterior_id_especias'],
                        $programa_premios_kanban['cuatro_cifras_id_especias'],
                        $programa_premios_kanban['tres_cifras_id_especias'],
                        $programa_premios_kanban['dos_cifras_id_especias'],
                        $programa_premios_kanban['una_cifras_id_especias'],
                        $programa_premios_kanban['progresion_id_especias'],
                        $programa_premios_kanban['dos_ultimas_cifras_id_especias'],
                        $programa_premios_kanban['tipo_premio'],
                        $programa_premios_kanban['procentaje'],
                    ));
                }
            }

            //T_ESCRIBANO
            $rs_escribano_kanban = sql_kanban("SELECT *
										  FROM KANBAN.T_ESCRIBANO");
            while ($row_escribano_kanban = siguiente_kanban($rs_escribano_kanban)) {
                $rs_escribano_sgs = sql("SELECT * FROM SGS.T_ESCRIBANO WHERE ID_ESCRIBANO=?", array($row_escribano_kanban->ID_ESCRIBANO));
                if ($rs_escribano_sgs->RowCount() > 0) {
                    sql("UPDATE SGS.T_ESCRIBANO SET DESCRIPCION=?, FECHA_BAJA=? WHERE ID_ESCRIBANO=?", array($row_escribano_kanban->DESCRIPCION, $row_escribano_kanban->FECHA_BAJA, $row_escribano_kanban->ID_ESCRIBANO));
                } else {
                    sql("INSERT INTO SGS.T_ESCRIBANO (ID_ESCRIBANO,DESCRIPCION) VALUES(?,?)", array($row_escribano_kanban->ID_ESCRIBANO, $row_escribano_kanban->DESCRIPCION));
                }
            }

            $rs_sorteo_sgs = sql("SELECT * FROM SGS.T_SORTEO WHERE ID_JUEGO=? AND SORTEO=?", array($id_juego, $sorteo));
            if ($rs_sorteo_sgs->RowCount() > 0) {
                $fraccion = ($sorteo_kanban['fracciones'] == null) ? 0 : $sorteo_kanban['fracciones'];
                sql("UPDATE SGS.T_SORTEO SET FECHA_SORTEO=to_date(?,'YYYY-MM-DD HH24:MI:SS'),
										FECHA_BAJA=?,
										ID_ESCRIBANO=?,
										ID_JEFE=?,
										ID_OPERADOR=?,
										ID_PROGRAMA=?,
										PRIMER_ELEMENTO=?,
										ULTIMO_ELEMENTO=?,
										FRACCIONES=?,
										PROGRESION=?,
										CANTIDAD_SORTEOS_FECHA=?,
										SORTEO_UNICO=?,
										DESCRIPCION=?,
										MONTO_FRACCION=?,
										CANTIDAD_SERIE=?,
										FECHA_HASTA_PAGO_PREMIO=?,
										QUINIELA_ASOC  			= ?
										WHERE ID_JUEGO=? AND SORTEO=?", array($sorteo_kanban['fecha_sorteo'],
                    $sorteo_kanban['fecha_baja'],
                    $sorteo_kanban['id_escribano'],
                    $sorteo_kanban['id_jefe'],
                    $sorteo_kanban['id_operador'],
                    $sorteo_kanban['id_programa'],
                    $sorteo_kanban['primer_elemento'],
                    $sorteo_kanban['ultimo_elemento'],
                    $fraccion,
                    $sorteo_kanban['progresion'],
                    $sorteo_kanban['cantidad_sorteos_fecha'],
                    $sorteo_kanban['sorteo_unico'],
                    $sorteo_kanban['descripcion'],
                    $sorteo_kanban['monto_fraccion'],
                    $sorteo_kanban['cantidad_serie'],
                    $sorteo_kanban['fecha_hasta_pago_premio'],
                    $sorteo_kanban['quiniela_asoc'],
                    $id_juego,
                    $sorteo));
            } else {
                $fraccion = ($sorteo_kanban['fracciones'] == null) ? 0 : $sorteo_kanban['fracciones'];
                sql("INSERT INTO SGS.T_SORTEO ( ID_SORTEO,
											FECHA_SORTEO,
											FECHA_BAJA,
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
											CANTIDAD_SERIE,
											MONTO_FRACCION,
											FECHA_HASTA_PAGO_PREMIO,
											QUINIELA_ASOC,
											ID_JUEGO,
											SORTEO)
										VALUES(?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)", array($sorteo_kanban['id_sorteo'],
                    $sorteo_kanban['fecha_sorteo'],
                    $sorteo_kanban['fecha_baja'],
                    $sorteo_kanban['id_escribano'],
                    $sorteo_kanban['id_jefe'],
                    $sorteo_kanban['id_operador'],
                    $sorteo_kanban['id_programa'],
                    $sorteo_kanban['primer_elemento'],
                    $sorteo_kanban['ultimo_elemento'],
                    $fraccion,
                    $sorteo_kanban['progresion'],
                    $sorteo_kanban['cantidad_sorteos_fecha'],
                    $sorteo_kanban['sorteo_unico'],
                    $sorteo_kanban['descripcion'],
                    $sorteo_kanban['cantidad_serie'],
                    $sorteo_kanban['monto_fraccion'],
                    $sorteo_kanban['fecha_hasta_pago_premio'],
                    $sorteo_kanban['quiniela_asoc'],
                    $id_juego,
                    $sorteo));
            }
            //T_PROGRAMA_PREMIOS_ANEXO
            $rs_anexo_c  = sql("SELECT ID_ANEXO FROM SGS.T_PROGRAMA_ANEXO_CABECERA WHERE ID_PROGRAMA = ?", array($sorteo_kanban['id_programa']));
            $row_anexo_c = siguiente($rs_anexo_c);
            //BORRO
            sql("DELETE FROM SGS.T_PROGRAMA_ANEXO_CABECERA WHERE ID_PROGRAMA = ?", array($sorteo_kanban['id_programa']));
            sql("DELETE FROM SGS.T_PROGRAMA_ANEXO_DETALLE WHERE ID_ANEXO = ?", array($row_anexo_c->ID_ANEXO));

            //CARGO NUEVAMENTE CABECERA Y DETALLE
            $rs_anexo = sql_kanban("SELECT ID_ANEXO, ID_PROGRAMA FROM KANBAN.T_PROGRAMA_ANEXO_CABECERA WHERE ID_PROGRAMA = ?", array($sorteo_kanban['id_programa']));
            $id_anexo = null;
            while ($row_anexo = siguiente_kanban($rs_anexo)) {
                $id_anexo = $row_anexo->ID_ANEXO;
                sql("INSERT INTO SGS.T_PROGRAMA_ANEXO_CABECERA (ID_ANEXO,ID_PROGRAMA)
  						VALUES
					  (
					    ?,
					    ?
					  )", array($row_anexo->ID_ANEXO, $row_anexo->ID_PROGRAMA));
            }

            $rs_anexo_d = sql_kanban("SELECT ID_ANEXO, ID_ESPECIE, IMPORTE, LLEVA_IMPUESTO,ID_ANEXO_DETALLE,ID_DESCRIPCION_PREMIO FROM KANBAN.T_PROGRAMA_ANEXO_DETALLE WHERE ID_ANEXO = ?", array($id_anexo));

            while ($row_anexo_d = siguiente_kanban($rs_anexo_d)) {
                sql("INSERT
						INTO SGS.T_PROGRAMA_ANEXO_DETALLE
						  (
						    ID_ANEXO,
						    ID_ESPECIE,
						    IMPORTE,
						    LLEVA_IMPUESTO,
						    ID_ANEXO_DETALLE,
						    ID_DESCRIPCION_PREMIO
						  )
						  VALUES
						  (
						    ?,
						    ?,
						    ?,
						    ?,
						    ?,
						    ?
						  )", array($row_anexo_d->ID_ANEXO, $row_anexo_d->ID_ESPECIE, $row_anexo_d->IMPORTE, $row_anexo_d->LLEVA_IMPUESTO, $row_anexo_d->ID_ANEXO_DETALLE, $row_anexo_d->ID_DESCRIPCION_PREMIO));
            }

        }
        FinalizarTransaccion($db);
        ok('Se finalizo la importacion de los datos del sorteo ' . $sorteo);
    } catch (exception $e) {
        error('Error en la base de datos' . $db->ErrorMsg());
        exit;
    }
}

function get_duplicates($array)
{
    return array_diff_assoc($array, array_unique($array));
}

function importar_extracto_quiniela_asociada()
{
    global $db_kanban;
    global $db;
    global $sorteo_kanban;

    $sorteo   = $_SESSION['sorteo'];
    $id_juego = $_SESSION['id_juego'];

    if (!isset($db)) {
        conectar_db();
    }

    if (!isset($db_kanban)) {
        conectar_db_kanban();

    }

/*$db_kanban->debug = true;
$db->debug = true;*/

    sql("DELETE FROM SGS.T_EXTRACCION
			WHERE
     			ID_JUEGO = ?
    		AND SORTEO = ?", array($id_juego, $sorteo));

    sql("DELETE FROM SGS.T_PREMIO_EXTRACTO
			WHERE
     			ID_JUEGO = ?
    		AND SORTEO = ?", array($id_juego, $sorteo));

    $rs_extracto = sql("	SELECT
								ID_DESCRIPCION,
								SUBSTR(LPAD(BILLETE, 4, '0'), - 2) AS BILLETE,
								BILLETE   AS BILLETE_ORIGINAL
							FROM
								KANBAN.T_PREMIO_EXTRACTO@KANBAN_ANTICIPADA
							WHERE
								SORTEO = ?
								AND ID_JUEGO = ?
								order by ID_DESCRIPCION", array($sorteo_kanban['quiniela_asoc'], 2));
    $extracto          = array();
    $extracto_original = array();
    while ($row_extracto = siguiente_kanban($rs_extracto)) {
        $extracto[$row_extracto->ID_DESCRIPCION] = $row_extracto->BILLETE;
    }
    $rs_extracto->MoveFirst();
    while ($row_extracto = siguiente_kanban($rs_extracto)) {
        $extracto_original[$row_extracto->ID_DESCRIPCION] = $row_extracto->BILLETE_ORIGINAL;
    }

    $resultado = array_unique($extracto);
    ksort($resultado);
    foreach ($resultado as $key => $value) {
        sql("INSERT
		      INTO SGS.T_EXTRACCION
		        (
		          ID_JUEGO,
		          SORTEO,
		          ORDEN,
		          POSICION,
		          NUMERO,
		          ZONA_JUEGO,
		          SORTEO_ASOC,
		          VALIDO
		        )
		        VALUES
		        (
		          ?,
		          ?,
		          ?,
		          ?,
		          ?,
		          ?,
		          ?,
		          'S'
		        )", array($id_juego, $sorteo, $key, $key, $value, 1, 'QUINIELA ASOCIADA ' . $sorteo_kanban['quiniela_asoc'] . '(' . str_pad($extracto_original[$key], 4, "0", STR_PAD_LEFT) . ')'));

        sql(" INSERT
        INTO SGS.T_PREMIO_EXTRACTO
          (
            ID_DESCRIPCION,
            BILLETE,
            ID_USUARIO,
            HORAEXTRACCION,
            SORTEO,
            SERIE,
            ID_JUEGO,
            ZONA_JUEGO,
		    SORTEO_ASOC
          )
          VALUES
          (
            ?,
            ?,
            ?,
            sysdate,
            ?,
            ?,
            ?,
            ?,
            ?
          )", array($key, $value, 'DU' . $_SESSION['dni'], $sorteo, 1, $id_juego, 1, 'QUINIELA ASOCIADA ' . $sorteo_kanban['quiniela_asoc'] . '(' . str_pad($extracto_original[$key], 4, "0", STR_PAD_LEFT) . ')'));

    }

    $duplicados = get_duplicates($extracto);

    foreach ($duplicados as $key => $value) {

        $rs = sql("	SELECT
					MIN(POSICION) as POSICION_DUPLICADO
				FROM
    				SGS.T_EXTRACCION
    			WHERE SORTEO=? AND ID_JUEGO=? AND NUMERO=?", array($sorteo, $id_juego, $value));
        $row = siguiente_kanban($rs);

        sql("INSERT
		      INTO SGS.T_EXTRACCION
		        (
		          ID_JUEGO,
		          SORTEO,
		          ORDEN,
		          POSICION,
		          NUMERO,
		          ZONA_JUEGO,
		          SORTEO_ASOC,
		          VALIDO,
		          POSICION_DUPLICADO
		        )
		        VALUES
		        (
		          ?,
		          ?,
		          ?,
		          ?,
		          ?,
		          ?,
		          ?,
		          'D',
		          ?
		        )", array($id_juego, $sorteo, $key, $key, $value, 1, 'QUINIELA DUPLICADO ' . $sorteo_kanban['quiniela_asoc'] . '(' . str_pad($extracto_original[$key], 4, "0", STR_PAD_LEFT) . ')', $row->POSICION_DUPLICADO));
        /*$posicion =array_search($value, $extracto);

    sql("    UPDATE SGS.T_EXTRACCION
    SET
    DUPLICADO = DUPLICADO||','||?
    WHERE
    ID_JUEGO = ?
    AND SORTEO = ?
    AND POSICION = ?", array($key,$id_juego, $sorteo, $posicion ));*/
    }
    info('Se finalizo la importacion de los numeros del sorteo, Cantidad: ' . (count($resultado) + count($duplicados)) . ' Fecha ' . date('d/m/Y H:i:s'));

}
