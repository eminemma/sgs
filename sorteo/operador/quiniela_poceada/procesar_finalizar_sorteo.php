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
     NULL AS cordoba,

     DECODE(b.descripcion,'ESTIMULO',a.monto_premio / 100 * porcentaje,'OCHO ACIERTOS',(a.monto_premio -(a.monto_premio * .01)) ,a.monto_premio) -
     IMPUESTOS.F_LEY_20630(NULL,DECODE(b.descripcion,'ESTIMULO',a.monto_premio / 100 * porcentaje,'OCHO ACIERTOS',(a.monto_premio -(a.monto_premio * .01)) ,a.monto_premio),a.id_juego),
     IMPUESTOS.F_LEY_20630(NULL,DECODE(b.descripcion,'ESTIMULO',a.monto_premio / 100 * porcentaje,'OCHO ACIERTOS',(a.monto_premio -(a.monto_premio * .01)) ,a.monto_premio),a.id_juego),
    0,
    (   CASE
             WHEN (DECODE(b.descripcion,'ESTIMULO',a.monto_premio / 100 * porcentaje,'OCHO ACIERTOS',(a.monto_premio -(a.monto_premio * .01)) ,a.monto_premio)) <= (  SELECT
                                                        POLITICA.F_TOPE_PREMIO_CC(?)
                                                    FROM
                                                        DUAL)  THEN
            'S'
        ELSE
            NULL
        END)
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
             a.porcentaje,
             b.monto_fraccion
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
    }

    sql("UPDATE KANBAN.T_SORTEO@KANBAN_ANTICIPADA A SET ESTADO_SORTEO='F' WHERE ID_JUEGO= ? AND SORTEO=?", array($id_juego, $sorteo));

    header('Content-Type: application/json');
    die(json_encode(array("mensaje" => "Se finalizo el sorteo correctamente, se buscaron ganadores y se fijaron los pozos reserva", "tipo" => "success")));
} catch (Exception $e) {
    header('Content-Type: application/json');
    die(json_encode(array("mensaje" => 'Error en la base de datos' . $db->ErrorMsg(), "tipo" => "error")));
}
