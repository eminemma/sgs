<?php
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
if ($rs->RecordCount() > 0) {
    header('Content-Type: application/json');
    die(json_encode(array("mensaje" => 'Ya existen premios generados', "tipo" => "error")));
}
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
        CORDOBA
    )
       SELECT
     1 AS fraccion,
     DECODE(b.descripcion,'ESTIMULO',a.monto_premio / 100 * porcentaje,'OCHO ACIERTOS',(a.monto_premio -(a.monto_premio * .01)) ,a.monto_premio) AS importe,
     NULL AS pagado,
     b.id_descripcion,
     a.secuencia      AS billete,
     a.id_juego,
     a.sorteo,
     1 AS serie,
     NULL AS especie,
     b.descripcion    AS concepto,
     NULL AS primer_premio,
     SYSDATE          AS fecha_alta,
     NULL AS validado,
     NULL AS prescripto,
     NULL AS fecha_paga,
     lpad(a.cod_juego,2,'0')
     || lpad(a.concurso,5,'0')
     || lpad(a.ocr,9,'0') AS ocr,
     a.nro_sucursal   AS suc_ban,
     a.nro_agencia    AS nro_agen,
     NULL AS pago_agencia,
     NULL AS id_sorteo_anticipado,
     NULL AS usuario,
     NULL AS original,
     NULL AS cordoba
 FROM
     (
         SELECT
             a.id_juego,
             a.sorteo,
             a.coincidencias,
             a.monto   AS monto_total,
             b.ocr,
             a.monto / c.cantidad AS monto_premio,
             b.secuencia,
             b.nro_agencia,
             b.nro_sucursal,
             cod_juego,
             concurso
         FROM
             (
                 SELECT
                     id_juego,
                     sorteo,
                     8 AS coincidencias,
                     total_premios_8_aciertos   AS monto
                 FROM
                     kanban.t_tt_recaudacion@kanban_anticipada
                 WHERE
                     id_juego = $id_juego
                     AND sorteo = $sorteo
                 UNION
                 SELECT
                     id_juego,
                     sorteo,
                     7 AS aciertos,
                     total_premios_7_aciertos   AS monto
                 FROM
                     kanban.t_tt_recaudacion@kanban_anticipada
                 WHERE
                     id_juego = $id_juego
                     AND sorteo = $sorteo
                 UNION
                 SELECT
                     id_juego,
                     sorteo,
                     6 AS aciertos,
                     total_premios_6_aciertos   AS monto
                 FROM
                     kanban.t_tt_recaudacion@kanban_anticipada
                 WHERE
                     id_juego = $id_juego
                     AND sorteo = $sorteo
             ) a,
             (
                 SELECT
                     cod_juego,
                     concurso,
                     nro_agencia,
                     nro_sucursal,
                     ocr,
                     secuencia,
                     COUNT(*) AS coincidencias
                 FROM
                     (
                         SELECT
                             cod_juego,
                             concurso,
                             a.nro_agencia,
                             a.nro_sucursal,
                             a.ocr,
                             secuencia,
                             lpad(billete,2,'0') AS extraccion,
                             substr(apuesta,-2),
                             substr(apuesta,-4,2),
                             substr(apuesta,-6,2),
                             substr(apuesta,-8,2),
                             substr(apuesta,-10,2),
                             substr(apuesta,-12,2),
                             substr(apuesta,-14,2),
                             substr(apuesta,-16,2)
                         FROM
                             facturacion_boldt.apuestas_nacionales@kanban_anticipada a,
                             sgs.t_premio_extracto b
                         WHERE
                             a.cod_juego = $id_juego
                             AND a.concurso = $sorteo
                             AND ( substr(apuesta,-2) = lpad(billete,2,'0')
                                   OR substr(apuesta,-4,2) = lpad(billete,2,'0')
                                   OR substr(apuesta,-6,2) = lpad(billete,2,'0')
                                   OR substr(apuesta,-8,2) = lpad(billete,2,'0')
                                   OR substr(apuesta,-10,2) = lpad(billete,2,'0')
                                   OR substr(apuesta,-12,2) = lpad(billete,2,'0')
                                   OR substr(apuesta,-14,2) = lpad(billete,2,'0')
                                   OR substr(apuesta,-16,2) = lpad(billete,2,'0') )
                             AND b.id_juego = $id_juego
                             AND b.sorteo = $sorteo
                             AND substr(sorteo_asoc,1,8) != 'COINCIDE'
                     )
                 GROUP BY
                     cod_juego,
                     concurso,
                     ocr,
                     secuencia,
                     nro_agencia,
                     nro_sucursal
                 HAVING
                     COUNT(*) IN (
                         8,
                         7,
                         6
                     )
             ) b,
             (
                 SELECT
                     coincidencias,
                     COUNT(*) AS cantidad
                 FROM
                     (
                         SELECT
                             cod_juego,
                             concurso,
                             nro_agencia,
                             nro_sucursal,
                             ocr,
                             secuencia,
                             COUNT(*) AS coincidencias
                         FROM
                             (
                                 SELECT
                                     cod_juego,
                                     concurso,
                                     a.nro_agencia,
                                     a.nro_sucursal,
                                     a.ocr,
                                     secuencia,
                                     lpad(billete,2,'0') AS extraccion,
                                     substr(apuesta,-2),
                                     substr(apuesta,-4,2),
                                     substr(apuesta,-6,2),
                                     substr(apuesta,-8,2),
                                     substr(apuesta,-10,2),
                                     substr(apuesta,-12,2),
                                     substr(apuesta,-14,2),
                                     substr(apuesta,-16,2)
                                 FROM
                                     facturacion_boldt.apuestas_nacionales@kanban_anticipada a,
                                     sgs.t_premio_extracto b
                                 WHERE
                                     a.cod_juego = $id_juego
                                     AND a.concurso = $sorteo
                                     AND ( substr(apuesta,-2) = lpad(billete,2,'0')
                                           OR substr(apuesta,-4,2) = lpad(billete,2,'0')
                                           OR substr(apuesta,-6,2) = lpad(billete,2,'0')
                                           OR substr(apuesta,-8,2) = lpad(billete,2,'0')
                                           OR substr(apuesta,-10,2) = lpad(billete,2,'0')
                                           OR substr(apuesta,-12,2) = lpad(billete,2,'0')
                                           OR substr(apuesta,-14,2) = lpad(billete,2,'0')
                                           OR substr(apuesta,-16,2) = lpad(billete,2,'0') )
                                     AND b.id_juego = $id_juego
                                     AND b.sorteo = $sorteo
                                     AND substr(sorteo_asoc,1,8) != 'COINCIDE'
                             )
                         GROUP BY
                             cod_juego,
                             concurso,
                             ocr,
                             secuencia,
                             nro_agencia,
                             nro_sucursal
                         HAVING
                             COUNT(*) IN (
                                 8,
                                 7,
                                 6
                             )
                     )
                 WHERE
                     coincidencias IN (
                         8,
                         7,
                         6
                     )
                 GROUP BY
                     coincidencias
             ) c
         WHERE
             a.coincidencias = b.coincidencias
             AND b.coincidencias = c.coincidencias
     ) a,
     (
         SELECT
             CASE regexp_substr(c.descripcion,'(\S*)')
                 WHEN 'OCHO'       THEN 8
                 WHEN 'SIETE'      THEN 7
                 WHEN 'SEIS'       THEN 6
                 WHEN 'ESTIMULO'   THEN 8
             END AS coincidencias,
             a.id_descripcion,
             c.descripcion,
             a.porcentaje
         FROM
             kanban.t_programa_premios@kanban_anticipada a,
             kanban.t_sorteo@kanban_anticipada b,
             kanban.t_premio_descripcion@kanban_anticipada c
         WHERE
             a.id_programa = b.id_programa
             AND a.id_descripcion = c.id_descripcion
             AND b.id_juego = $id_juego
             AND b.sorteo = $sorteo
     ) b
 WHERE
     a.coincidencias = b.coincidencias
 ORDER BY
     b.coincidencias,
     descripcion DESC;
            commit;
 END;");
    $db->debug = true;
    $rs        = sql('SELECT ID_DESCRIPCION
 									FROM KANBAN.T_PREMIOS@KANBAN_ANTICIPADA
 									WHERE SORTEO = ? AND ID_JUEGO = ?
                                    and ID_DESCRIPCION = 82', array($sorteo, $id_juego));

    $rs_recaudacion = sql("     SELECT
                                        tr.TOTAL_PREMIOS_8_ACIERTOS,
                                        tr.POZO_MINIMO_8_ASEGURADO,
                                        tr.PROP_8_ACIERTOS,
                                        tr_ant.FONDO_RESERVA_8_ACIERTOS,
                                        tr.TOTAL_PREMIOS_7_ACIERTOS,
                                        tr.POZO_MINIMO_7_ASEGURADO,
                                        tr.PROP_7_ACIERTOS,
                                        tr_ant.FONDO_RESERVA_7_ACIERTOS,
                                        tr.TOTAL_PREMIOS_6_ACIERTOS,
                                        tr.POZO_MINIMO_6_ASEGURADO,
                                        tr.PROP_6_ACIERTOS,
                                        tr_ant.FONDO_RESERVA_6_ACIERTOS,
                                        tr.POZO_RESULTANTE_6,
                                        tr.POZO_RESULTANTE_7,
                                        tr.POZO_RESULTANTE_8,
                                        NVL(TR_ANT.POZO_RESERVA_8_PROX_SORTEO,0) AS POZO_RESERVA_8_PROX_SORTEO,
                                        NVL(TR_ANT.POZO_RESERVA_7_PROX_SORTEO,0) AS POZO_RESERVA_7_PROX_SORTEO,
                                        NVL(TR_ANT.POZO_RESERVA_6_PROX_SORTEO,0) AS POZO_RESERVA_6_PROX_SORTEO,
                                        NVL(TR.APORTE_VOLUNTARIO_8_ACIERTOS,0) as APORTE_VOLUNTARIO_8_ACIERTOS,
                                        NVL(TR.APORTE_VOLUNTARIO_7_ACIERTOS,0) as APORTE_VOLUNTARIO_7_ACIERTOS,
                                         NVL(TR.APORTE_VOLUNTARIO_6_ACIERTOS,0) as APORTE_VOLUNTARIO_6_ACIERTOS
                        FROM KANBAN.T_TT_RECAUDACION@KANBAN_ANTICIPADA tr,
                          (
                                                      SELECT    FONDO_RESERVA_8_ACIERTOS,
                                                                FONDO_RESERVA_7_ACIERTOS,
                                                                FONDO_RESERVA_6_ACIERTOS,
                                                                ID_JUEGO,
                                                                POZO_RESERVA_6_PROX_SORTEO,
                                                                POZO_RESERVA_7_PROX_SORTEO,
                                                                POZO_RESERVA_8_PROX_SORTEO
                                                  FROM KANBAN.T_TT_RECAUDACION@KANBAN_ANTICIPADA
                                                      WHERE (SORTEO,ID_JUEGO) IN (
                                                      SELECT
                                                          SORTEO,ID_JUEGO
                                                      FROM
                                                          (
                                                              SELECT
                                                                  SORTEO,ID_JUEGO
                                                              FROM
                                                                  KANBAN.T_SORTEO@KANBAN_ANTICIPADA
                                                              WHERE
                                                                  ID_JUEGO  = ?
                                                                  AND SORTEO < ?
                                                              ORDER BY SORTEO DESC
                                                          )
                                                      WHERE
                                                          ROWNUM = 1)
                                                ) TR_ANT
                        WHERE
                                tr.ID_JUEGO = ?
                            AND tr.SORTEO   = ?
                            AND TR_ANT.ID_JUEGO(+) = tr.ID_JUEGO", array($id_juego, $sorteo, $id_juego, $sorteo));
    $row                      = siguiente($rs_recaudacion);
    $aporte_voluntario        = 0;
    $total_premios_8_aciertos = 0;
    $acumulado_8_aciertos     = 0;
    $acumulado_7_aciertos     = 0;
    $acumulado_6_aciertos     = 0;
    $aporte_fondo             = 0;
    $retiro_fondo             = 0;
    $fondo_reserva_anterior   = 0;
    $rs                       = sql('SELECT ID_DESCRIPCION
                                    FROM KANBAN.T_PREMIOS@KANBAN_ANTICIPADA
                                    WHERE SORTEO = ? AND ID_JUEGO = ?
                                    and ID_DESCRIPCION = 82', array($sorteo, $id_juego));

    if ($rs->RecordCount() == 0) {
        $aporte_fondo           = $row->PROP_8_ACIERTOS * 0.1;
        $retiro_fondo           = 0;
        $fondo_reserva_anterior = $row->FONDO_RESERVA_8_ACIERTOS;
        $acumulado_8_aciertos   = $row->PROP_8_ACIERTOS * 0.9 + $row->POZO_RESERVA_8_PROX_SORTEO;

    } else {
        $aporte_fondo           = 0;
        $retiro_fondo           = $row->APORTE_VOLUNTARIO_8_ACIERTOS;
        $fondo_reserva_anterior = $row->FONDO_RESERVA_8_ACIERTOS;

    }
    $fondo_reserva_8_aciertos = $aporte_fondo - $retiro_fondo + $fondo_reserva_anterior;

    $aporte_fondo           = 0;
    $retiro_fondo           = 0;
    $fondo_reserva_anterior = 0;

    $rs = sql('SELECT ID_DESCRIPCION
                                    FROM KANBAN.T_PREMIOS@KANBAN_ANTICIPADA
                                    WHERE SORTEO = ? AND ID_JUEGO = ?
                                    and ID_DESCRIPCION = 83', array($sorteo, $id_juego));

    if ($rs->RecordCount() == 0) {
        $aporte_fondo           = $row->PROP_7_ACIERTOS * 0.1;
        $retiro_fondo           = 0;
        $fondo_reserva_anterior = $row->FONDO_RESERVA_7_ACIERTOS;
        $acumulado_7_aciertos   = $row->PROP_7_ACIERTOS * 0.9 + $row->POZO_RESERVA_7_PROX_SORTEO;

    } else {
        $aporte_fondo           = 0;
        $retiro_fondo           = $row->APORTE_VOLUNTARIO_7_ACIERTOS;
        $fondo_reserva_anterior = $row->FONDO_RESERVA_7_ACIERTOS;

    }
    $fondo_reserva_7_aciertos = $aporte_fondo - $retiro_fondo + $fondo_reserva_anterior;

    $aporte_fondo           = 0;
    $retiro_fondo           = 0;
    $fondo_reserva_anterior = 0;
    $rs                     = sql('SELECT ID_DESCRIPCION
                                    FROM KANBAN.T_PREMIOS@KANBAN_ANTICIPADA
                                    WHERE SORTEO = ? AND ID_JUEGO = ?
                                    and ID_DESCRIPCION = 84', array($sorteo, $id_juego));

    if ($rs->RecordCount() == 0) {
        $aporte_fondo           = $row->PROP_6_ACIERTOS * 0.1;
        $retiro_fondo           = 0;
        $fondo_reserva_anterior = $row->FONDO_RESERVA_6_ACIERTOS;
        $acumulado_6_aciertos   = $row->PROP_6_ACIERTOS * 0.9 + $row->POZO_RESERVA_6_PROX_SORTEO;

    } else {
        $aporte_fondo           = 0;
        $retiro_fondo           = $row->APORTE_VOLUNTARIO_6_ACIERTOS;
        $fondo_reserva_anterior = $row->FONDO_RESERVA_6_ACIERTOS;

    }
    $fondo_reserva_6_aciertos = $aporte_fondo - $retiro_fondo + $fondo_reserva_anterior;
    sql("UPDATE KANBAN.T_TT_RECAUDACION@KANBAN_ANTICIPADA A
            SET ACUM_8_ACIERTOS_PROX_SORTEO   = ?,
                FONDO_RESERVA_8_ACIERTOS     = ?,
                ACUM_7_ACIERTOS_PROX_SORTEO   = ?,
                FONDO_RESERVA_7_ACIERTOS     = ?,
                ACUM_6_ACIERTOS_PROX_SORTEO   = ?,
                FONDO_RESERVA_6_ACIERTOS     = ?
            WHERE   A.ID_JUEGO = ?
                AND A.SORTEO   = ?",
        array(
            $acumulado_8_aciertos,
            $fondo_reserva_8_aciertos,
            $acumulado_7_aciertos,
            $fondo_reserva_7_aciertos,
            $acumulado_6_aciertos,
            $fondo_reserva_6_aciertos,
            $id_juego,
            $sorteo,
        )
    );

    /* $rs = sql('SELECT ID_DESCRIPCION
    FROM KANBAN.T_PREMIOS@KANBAN_ANTICIPADA
    WHERE SORTEO = ? AND ID_JUEGO = ?
    and ID_DESCRIPCION = 83', array($sorteo, $id_juego));
    $aporte_voluntario          = 0;
    $total_premios_7_aciertos   = 0;
    $pozo_reserva_7_prox_sorteo = 0;

    if ($rs->RecordCount() == 0) {
    //SIN GANADORES 8 ACIERTOS
    $fondo_reserva_7_aciertos   = $row->PROP_7_ACIERTOS * 0.1 + $row->FONDO_RESERVA_7_ACIERTOS;
    $pozo_reserva_7_prox_sorteo = $row->PROP_7_ACIERTOS * 0.9 + $row->POZO_RESERVA_7_PROX_SORTEO;

    } else {
    //CON GANADORES 8 ACIERTOS

    if ($aporte_voluntario > 0) {
    $fondo_reserva_7_aciertos = 0 - $aporte_voluntario + $row->FONDO_RESERVA_7_ACIERTOS;
    }

    $total_premios_7_aciertos = ($aporte_voluntario + $row->POZO_RESULTANTE_7) * 0.99;

    }
    sql("UPDATE KANBAN.T_TT_RECAUDACION@KANBAN_ANTICIPADA A
    SET POZO_RESERVA_7_PROX_SORTEO   = ?,
    FONDO_RESERVA_7_ACIERTOS     = ?,
    TOTAL_PREMIOS_7_ACIERTOS     = ?
    WHERE A.ID_JUEGO = ?
    AND A.SORTEO   = ?",
    array(
    $pozo_reserva_7_prox_sorteo,
    $fondo_reserva_7_aciertos,
    $total_premios_7_aciertos,
    $id_juego,
    $sorteo,
    )
    );

    $rs = sql('SELECT ID_DESCRIPCION
    FROM KANBAN.T_PREMIOS@KANBAN_ANTICIPADA
    WHERE SORTEO = ? AND ID_JUEGO = ?
    and ID_DESCRIPCION = 84', array($sorteo, $id_juego));
    $aporte_voluntario          = 0;
    $total_premios_6_aciertos   = 0;
    $pozo_reserva_6_prox_sorteo = 0;

    if ($rs->RecordCount() == 0) {
    //SIN GANADORES 8 ACIERTOS
    $fondo_reserva_6_aciertos   = $row->PROP_6_ACIERTOS * 0.1 + $row->FONDO_RESERVA_6_ACIERTOS;
    $pozo_reserva_6_prox_sorteo = $row->PROP_6_ACIERTOS * 0.9 + $row->POZO_RESERVA_6_PROX_SORTEO;

    } else {
    //CON GANADORES 8 ACIERTOS

    if ($aporte_voluntario > 0) {
    $fondo_reserva_6_aciertos = 0 - $aporte_voluntario + $row->FONDO_RESERVA_6_ACIERTOS;
    }

    $total_premios_6_aciertos = ($aporte_voluntario + $row->POZO_RESULTANTE_6) * 0.99;

    }
    sql("UPDATE KANBAN.T_TT_RECAUDACION@KANBAN_ANTICIPADA A
    SET POZO_RESERVA_6_PROX_SORTEO   = ?,
    FONDO_RESERVA_6_ACIERTOS     = ?,
    TOTAL_PREMIOS_6_ACIERTOS     = ?
    WHERE A.ID_JUEGO = ?
    AND A.SORTEO   = ?",
    array(
    $pozo_reserva_7_prox_sorteo,
    $fondo_reserva_6_aciertos,
    $total_premios_6_aciertos,
    $id_juego,
    $sorteo));*/

    sql("UPDATE KANBAN.T_SORTEO@KANBAN_ANTICIPADA A SET ESTADO_SORTEO='F' WHERE ID_JUEGO= ? AND SORTEO=?", array($id_juego, $sorteo));

    header('Content-Type: application/json');
    die(json_encode(array("mensaje" => "Se finalizo el sorteo correctamente, se buscaron ganadores y se fijaron los pozos reserva", "tipo" => "success")));
} catch (Exception $e) {
    header('Content-Type: application/json');
    die(json_encode(array("mensaje" => 'Error en la base de datos' . $db->ErrorMsg(), "tipo" => "error")));
}
