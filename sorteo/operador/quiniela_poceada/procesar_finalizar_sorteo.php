<?php
//ini_set('display_errors', 1);
@session_start();
include_once dirname(__FILE__) . '/../../../mensajes.php';
include_once dirname(__FILE__) . '/../../../db.php';

$sorteo   = $_SESSION['sorteo'];
$id_juego = $_SESSION['id_juego'];
conectar_db();
//$db->debug = true;
$rs = sql('SELECT ID_DESCRIPCION
 									FROM KANBAN.T_PREMIOS@KANBAN_ANTICIPADA
 									WHERE SORTEO = ? AND ID_JUEGO = ? ', array($sorteo, $id_juego));

$rs_politica = sql(' SELECT
                        ID_JUEGO_POLITICA
                    FROM
                        KAIZEN.JUEGO@KANBAN_ANTICIPADA KJ
                    WHERE
                        KJ.COD_JUEGO = ? ', array($id_juego));
$row_politica = $rs_politica->FetchNextObject($toupper = true);

if ($rs->RecordCount() > 0) {
    header('Content-Type: application/json');
    die(json_encode(array("mensaje" => 'Ya existen premios generados', "tipo" => "error")));
}

$rs_sorteo = sql('  SELECT  MONTO_FRACCION
                    FROM KANBAN.T_SORTEO@KANBAN_ANTICIPADA
                    WHERE   SORTEO   = ?
                        AND ID_JUEGO = ? ', array($sorteo, $id_juego));

$row_sorteo = $rs_sorteo->FetchNextObject($toupper = true);

$m_fraccion = $row_sorteo->MONTO_FRACCION;

try {
    $db->Execute("BEGIN
    INSERT INTO KANBAN.T_PREMIOS@KANBAN_ANTICIPADA (
        FRACCION,
        IMPORTE,
        PAGADO,
        ID_DESCRIPCION,
        BILLETE,
        ID_JUEGO,
        SORTEO,
        SERIE,
        ESPECIE,
        CONCEPTO,
        PRIMER_PREMIO,
        FECHA_ALTA,
        VALIDADO,
        PRESCRIPTO,
        FECHA_PAGA,
        OCR,
        SUC_BAN,
        NRO_AGEN,
        PAGO_AGENCIA,
        ID_SORTEO_ANTICIPADO,
        USUARIO,
        ORIGINAL,
        CORDOBA,
        IMPORTE_NETO,
        LEY20630,
        LEY9505,
        MAYOR
    )
        SELECT
            1 AS FRACCION,
            DECODE(B.DESCRIPCION, 'ESTIMULO', A.MONTO_PREMIO / 100 * PORCENTAJE, 'OCHO ACIERTOS',(A.MONTO_PREMIO -(A.MONTO_PREMIO
            *.01)), A.MONTO_PREMIO) AS IMPORTE,
            NULL AS PAGADO,
            B.ID_DESCRIPCION,
            A.SECUENCIA      AS BILLETE,
            A.ID_JUEGO,
            A.SORTEO,
            1 AS SERIE,
            NULL AS ESPECIE,
            B.DESCRIPCION    AS CONCEPTO,
            NULL AS PRIMER_PREMIO,
            SYSDATE          AS FECHA_ALTA,
            NULL AS VALIDADO,
            NULL AS PRESCRIPTO,
            NULL AS FECHA_PAGA,
            LPAD(A.COD_JUEGO, 2, '0')
            || LPAD(A.CONCURSO, 5, '0')
            || LPAD(A.OCR, 9, '0') AS OCR,
            A.NRO_SUCURSAL   AS SUC_BAN,
            A.NRO_AGENCIA    AS NRO_AGEN,
            NULL AS PAGO_AGENCIA,
            NULL AS ID_SORTEO_ANTICIPADO,
            NULL AS USUARIO,
            NULL AS ORIGINAL,
            NULL AS CORDOBA,
            DECODE(B.DESCRIPCION, 'ESTIMULO', A.MONTO_PREMIO / 100 * PORCENTAJE, 'OCHO ACIERTOS',(A.MONTO_PREMIO -(A.MONTO_PREMIO
            *.01)), A.MONTO_PREMIO) - IMPUESTOS.F_LEY_20630@KANBAN_ANTICIPADA(NULL, DECODE(B.DESCRIPCION, 'ESTIMULO', A.MONTO_PREMIO
            / 100 * PORCENTAJE, 'OCHO ACIERTOS',(A.MONTO_PREMIO -(A.MONTO_PREMIO *.01)), A.MONTO_PREMIO), A.ID_JUEGO),
            IMPUESTOS.F_LEY_20630@KANBAN_ANTICIPADA(NULL, DECODE(B.DESCRIPCION, 'ESTIMULO', A.MONTO_PREMIO / 100 * PORCENTAJE, 'OCHO ACIERTOS'
            ,(A.MONTO_PREMIO -(A.MONTO_PREMIO *.01)), A.MONTO_PREMIO), A.ID_JUEGO),
            0,
            (
                CASE
                    WHEN ( DECODE(B.DESCRIPCION, 'ESTIMULO', A.MONTO_PREMIO / 100 * PORCENTAJE, 'OCHO ACIERTOS',(A.MONTO_PREMIO -
                    (A.MONTO_PREMIO *.01)), A.MONTO_PREMIO) ) >= (
                        SELECT
                            POLITICA.F_TOPE_PREMIO_CC@KANBAN_ANTICIPADA(?)
                        FROM
                            DUAL
                    ) THEN
                        'S'
                    ELSE
                        'N'
                END
            )
        FROM
            (
                SELECT
                    A.ID_JUEGO,
                    A.SORTEO,
                    A.COINCIDENCIAS,
                    A.MONTO AS MONTO_TOTAL,
                    B.OCR,
                    --A.MONTO / C.CANTIDAD AS MONTO_PREMIO,

                     CASE A.COINCIDENCIAS
                     	when  5 then

                     		$m_fraccion

                     	else
                     		A.MONTO / C.CANTIDAD
                     	END AS MONTO_PREMIO,



                    B.SECUENCIA,
                    B.NRO_AGENCIA,
                    B.NRO_SUCURSAL,
                    COD_JUEGO,
                    CONCURSO
                FROM
                    (
                        SELECT
                            ID_JUEGO,
                            SORTEO,
                            8 AS COINCIDENCIAS,
                            TOTAL_PREMIOS_8_ACIERTOS AS MONTO
                        FROM
                            KANBAN.T_TT_RECAUDACION@KANBAN_ANTICIPADA
                        WHERE
                            ID_JUEGO = $id_juego
                            AND SORTEO = $sorteo
                        UNION
                        SELECT
                            ID_JUEGO,
                            SORTEO,
                            7 AS ACIERTOS,
                            TOTAL_PREMIOS_7_ACIERTOS AS MONTO
                        FROM
                            KANBAN.T_TT_RECAUDACION@KANBAN_ANTICIPADA
                        WHERE
                            ID_JUEGO = $id_juego
                            AND SORTEO = $sorteo
                        UNION
                        SELECT
                            ID_JUEGO,
                            SORTEO,
                            6 AS ACIERTOS,
                            TOTAL_PREMIOS_6_ACIERTOS AS MONTO
                        FROM
                            KANBAN.T_TT_RECAUDACION@KANBAN_ANTICIPADA
                        WHERE
                            ID_JUEGO = $id_juego
                            AND SORTEO = $sorteo
                        UNION
                        SELECT
                            ID_JUEGO,
                            SORTEO,
                            5 AS ACIERTOS,
                            TOTAL_PREMIOS_5_ACIERTOS AS MONTO
                        FROM
                            KANBAN.T_TT_RECAUDACION@KANBAN_ANTICIPADA
                        WHERE
                            ID_JUEGO = $id_juego
                            AND SORTEO = $sorteo
                    ) A,
                    (
                        SELECT
                            COD_JUEGO,
                            CONCURSO,
                            NRO_AGENCIA,
                            NRO_SUCURSAL,
                            OCR,
                            SECUENCIA,
                            COUNT(*) AS COINCIDENCIAS
                        FROM
                            (
                                SELECT
                                    COD_JUEGO,
                                    CONCURSO,
                                    A.NRO_AGENCIA,
                                    A.NRO_SUCURSAL,
                                    A.OCR,
                                    SECUENCIA,
                                    LPAD(BILLETE, 2, '0') AS EXTRACCION,
                                    SUBSTR(APUESTA, - 2),
                                    SUBSTR(APUESTA, - 4, 2),
                                    SUBSTR(APUESTA, - 6, 2),
                                    SUBSTR(APUESTA, - 8, 2),
                                    SUBSTR(APUESTA, - 10, 2),
                                    SUBSTR(APUESTA, - 12, 2),
                                    SUBSTR(APUESTA, - 14, 2),
                                    SUBSTR(APUESTA, - 16, 2)
                                FROM
                                    FACTURACION_BOLDT.APUESTAS_NACIONALES@KANBAN_ANTICIPADA   A,
                                    SGS.T_PREMIO_EXTRACTO                                     B
                                WHERE
                                    A.COD_JUEGO = $id_juego
                                    AND A.CONCURSO = $sorteo
                                    AND ( SUBSTR(APUESTA, - 2) = LPAD(BILLETE, 2, '0')
                                          OR SUBSTR(APUESTA, - 4, 2) = LPAD(BILLETE, 2, '0')
                                          OR SUBSTR(APUESTA, - 6, 2) = LPAD(BILLETE, 2, '0')
                                          OR SUBSTR(APUESTA, - 8, 2) = LPAD(BILLETE, 2, '0')
                                          OR SUBSTR(APUESTA, - 10, 2) = LPAD(BILLETE, 2, '0')
                                          OR SUBSTR(APUESTA, - 12, 2) = LPAD(BILLETE, 2, '0')
                                          OR SUBSTR(APUESTA, - 14, 2) = LPAD(BILLETE, 2, '0')
                                          OR SUBSTR(APUESTA, - 16, 2) = LPAD(BILLETE, 2, '0') )
                                    AND B.ID_JUEGO = $id_juego
                                    AND B.SORTEO = $sorteo
                                    AND SUBSTR(SORTEO_ASOC, 1, 8) != 'COINCIDE'
                            )
                        GROUP BY
                            COD_JUEGO,
                            CONCURSO,
                            OCR,
                            SECUENCIA,
                            NRO_AGENCIA,
                            NRO_SUCURSAL
                        HAVING
                            COUNT(*) IN (
                                8,
                                7,
                                6,
                                5
                            )
                    ) B,
                    (
                        SELECT
                            COINCIDENCIAS,
                            COUNT(*) AS CANTIDAD
                        FROM
                            (
                                SELECT
                                    COD_JUEGO,
                                    CONCURSO,
                                    NRO_AGENCIA,
                                    NRO_SUCURSAL,
                                    OCR,
                                    SECUENCIA,
                                    COUNT(*) AS COINCIDENCIAS
                                FROM
                                    (
                                        SELECT
                                            COD_JUEGO,
                                            CONCURSO,
                                            A.NRO_AGENCIA,
                                            A.NRO_SUCURSAL,
                                            A.OCR,
                                            SECUENCIA,
                                            LPAD(BILLETE, 2, '0') AS EXTRACCION,
                                            SUBSTR(APUESTA, - 2),
                                            SUBSTR(APUESTA, - 4, 2),
                                            SUBSTR(APUESTA, - 6, 2),
                                            SUBSTR(APUESTA, - 8, 2),
                                            SUBSTR(APUESTA, - 10, 2),
                                            SUBSTR(APUESTA, - 12, 2),
                                            SUBSTR(APUESTA, - 14, 2),
                                            SUBSTR(APUESTA, - 16, 2)
                                        FROM
                                            FACTURACION_BOLDT.APUESTAS_NACIONALES@KANBAN_ANTICIPADA   A,
                                            SGS.T_PREMIO_EXTRACTO                                     B
                                        WHERE
                                            A.COD_JUEGO = $id_juego
                                            AND A.CONCURSO = $sorteo
                                            AND ( SUBSTR(APUESTA, - 2) = LPAD(BILLETE, 2, '0')
                                                  OR SUBSTR(APUESTA, - 4, 2) = LPAD(BILLETE, 2, '0')
                                                  OR SUBSTR(APUESTA, - 6, 2) = LPAD(BILLETE, 2, '0')
                                                  OR SUBSTR(APUESTA, - 8, 2) = LPAD(BILLETE, 2, '0')
                                                  OR SUBSTR(APUESTA, - 10, 2) = LPAD(BILLETE, 2, '0')
                                                  OR SUBSTR(APUESTA, - 12, 2) = LPAD(BILLETE, 2, '0')
                                                  OR SUBSTR(APUESTA, - 14, 2) = LPAD(BILLETE, 2, '0')
                                                  OR SUBSTR(APUESTA, - 16, 2) = LPAD(BILLETE, 2, '0') )
                                            AND B.ID_JUEGO = $id_juego
                                            AND B.SORTEO = $sorteo
                                            AND SUBSTR(SORTEO_ASOC, 1, 8) != 'COINCIDE'
                                    )
                                GROUP BY
                                    COD_JUEGO,
                                    CONCURSO,
                                    OCR,
                                    SECUENCIA,
                                    NRO_AGENCIA,
                                    NRO_SUCURSAL
                                HAVING
                                    COUNT(*) IN (
                                        8,
                                        7,
                                        6,
                                        5
                                    )
                            )
                        WHERE
                            COINCIDENCIAS IN (
                                8,
                                7,
                                6,
                                5
                            )
                        GROUP BY
                            COINCIDENCIAS
                    ) C
                WHERE
                    A.COINCIDENCIAS = B.COINCIDENCIAS
                    AND B.COINCIDENCIAS = C.COINCIDENCIAS
            ) A,
            (
                SELECT
                    CASE REGEXP_SUBSTR(C.DESCRIPCION, '(\S*)')
                        WHEN 'OCHO'       THEN
                            8
                        WHEN 'SIETE'      THEN
                            7
                        WHEN 'SEIS'       THEN
                            6
                        WHEN 'CINCO'      THEN
                            5
                        WHEN 'ESTIMULO'   THEN
                            8
                    END AS COINCIDENCIAS,
                    A.ID_DESCRIPCION,
                    C.DESCRIPCION,
                    A.PORCENTAJE,
                    B.MONTO_FRACCION
                FROM
                    KANBAN.T_PROGRAMA_PREMIOS@KANBAN_ANTICIPADA     A,
                    KANBAN.T_SORTEO@KANBAN_ANTICIPADA               B,
                    KANBAN.T_PREMIO_DESCRIPCION@KANBAN_ANTICIPADA   C
                WHERE
                    A.ID_PROGRAMA = B.ID_PROGRAMA
                    AND A.ID_DESCRIPCION = C.ID_DESCRIPCION
                    AND B.ID_JUEGO = $id_juego
                    AND B.SORTEO = $sorteo
            ) B
        WHERE
            A.COINCIDENCIAS = B.COINCIDENCIAS
        ORDER BY
            B.COINCIDENCIAS,
            DESCRIPCION DESC;

    COMMIT;
END;", array($row_politica->ID_JUEGO_POLITICA));

    $rs = sql('SELECT ID_DESCRIPCION
 									FROM KANBAN.T_PREMIOS@KANBAN_ANTICIPADA
 									WHERE SORTEO = ? AND ID_JUEGO = ?
                                    and ID_DESCRIPCION = 82', array($sorteo, $id_juego));

    if ($rs->RecordCount() == 0) {
        sql("UPDATE KANBAN.T_TT_RECAUDACION@KANBAN_ANTICIPADA A SET POZO_RESERVA_8_PROX_SORTEO=
				(SELECT (PROP_8_ACIERTOS * .10)
			FROM KANBAN.T_TT_RECAUDACION@KANBAN_ANTICIPADA
			WHERE ID_JUEGO=A.ID_JUEGO AND SORTEO=A.SORTEO)
			WHERE A.ID_JUEGO=? AND A.SORTEO=?", array($id_juego, $sorteo));
    } else {
        sql("UPDATE KANBAN.T_TT_RECAUDACION@KANBAN_ANTICIPADA A SET POZO_RESERVA_8_PROX_SORTEO=0
            WHERE A.ID_JUEGO=? AND A.SORTEO=?", array($id_juego, $sorteo));
    }

    $rs = sql('SELECT ID_DESCRIPCION
                                    FROM KANBAN.T_PREMIOS@KANBAN_ANTICIPADA
                                    WHERE SORTEO = ? AND ID_JUEGO = ?
                                    and ID_DESCRIPCION = 83', array($sorteo, $id_juego));

    if ($rs->RecordCount() == 0) {
        sql("UPDATE KANBAN.T_TT_RECAUDACION@KANBAN_ANTICIPADA A SET POZO_RESERVA_7_PROX_SORTEO=
				(SELECT (PROP_7_ACIERTOS * .10)
			 FROM KANBAN.T_TT_RECAUDACION@KANBAN_ANTICIPADA
			 WHERE ID_JUEGO=A.ID_JUEGO AND SORTEO=A.SORTEO)
			 WHERE A.ID_JUEGO=? AND A.SORTEO=?", array($id_juego, $sorteo));
    } else {
        sql("UPDATE KANBAN.T_TT_RECAUDACION@KANBAN_ANTICIPADA A SET POZO_RESERVA_7_PROX_SORTEO=0
            WHERE A.ID_JUEGO=? AND A.SORTEO=?", array($id_juego, $sorteo));
    }
    $rs = sql('SELECT ID_DESCRIPCION
                                    FROM KANBAN.T_PREMIOS@KANBAN_ANTICIPADA
                                    WHERE SORTEO = ? AND ID_JUEGO = ?
                                    and ID_DESCRIPCION = 84', array($sorteo, $id_juego));
    if ($rs->RecordCount() == 0) {
        sql("UPDATE KANBAN.T_TT_RECAUDACION@KANBAN_ANTICIPADA A SET POZO_RESERVA_6_PROX_SORTEO=
				(SELECT (PROP_6_ACIERTOS * .10)
			FROM KANBAN.T_TT_RECAUDACION@KANBAN_ANTICIPADA
			WHERE ID_JUEGO=A.ID_JUEGO AND SORTEO=A.SORTEO)
			WHERE A.ID_JUEGO=? AND A.SORTEO=?", array($id_juego, $sorteo));
    } else {
        sql("UPDATE KANBAN.T_TT_RECAUDACION@KANBAN_ANTICIPADA A SET POZO_RESERVA_6_PROX_SORTEO=0
            WHERE A.ID_JUEGO=? AND A.SORTEO=?", array($id_juego, $sorteo));
    }

    $rs_premios = sql('SELECT SUM(IMPORTE) AS TOTAL_PREMIOS
                                    FROM KANBAN.T_PREMIOS@KANBAN_ANTICIPADA
                                    WHERE SORTEO = ? AND ID_JUEGO = ?
                                    and ID_DESCRIPCION = 85', array($sorteo, $id_juego));
    //$db->debug = true;
    if ($rs_premios->RecordCount() > 0) {
        $row_premios = $rs_premios->FetchNextObject($toupper = true);
        $rs_pozo     = sql('SELECT PROP_5_ACIERTOS
            FROM KANBAN.T_TT_RECAUDACION@KANBAN_ANTICIPADA
            WHERE ID_JUEGO=? AND SORTEO=?', array($id_juego, $sorteo));

        $row_pozo = $rs_pozo->FetchNextObject($toupper = true);
        //POZO DEL HOY MENOS LOS PREMIOS, SI QUEDA PLATA VA A LA RESERVA SINO NO VA A LA RESERVA ?? NEGATIVA
        $reserva = $row_pozo->PROP_5_ACIERTOS - $row_premios->TOTAL_PREMIOS;

        sql("UPDATE KANBAN.T_TT_RECAUDACION@KANBAN_ANTICIPADA A SET POZO_RESERVA_5_PROX_SORTEO=?
            WHERE A.ID_JUEGO=? AND A.SORTEO=?", array($reserva, $id_juego, $sorteo));
    } else {
        sql("UPDATE KANBAN.T_TT_RECAUDACION@KANBAN_ANTICIPADA A SET POZO_RESERVA_5_PROX_SORTEO=0
            WHERE A.ID_JUEGO=? AND A.SORTEO=?", array($id_juego, $sorteo));
    }

    sql("UPDATE KANBAN.T_SORTEO@KANBAN_ANTICIPADA A SET ESTADO_SORTEO='F' WHERE ID_JUEGO= ? AND SORTEO=?", array($id_juego, $sorteo));

    header('Content-Type: application/json');
    die(json_encode(array("mensaje" => "Se finalizo el sorteo correctamente, se buscaron ganadores y se fijaron los pozos reserva", "tipo" => "success")));
} catch (Exception $e) {
    header('Content-Type: application/json');
    die(json_encode(array("mensaje" => 'Error en la base de datos' . $db->ErrorMsg(), "tipo" => "error")));
}
