<?php
@session_start();
include_once dirname(__FILE__) . '/../db.php';
include_once dirname(__FILE__) . '/../librerias/alambre/funcion.inc.php';
$accion   = isset($_GET['accion']) ? $_GET['accion'] : '';
$sorteo   = $_SESSION['sorteo'];
$id_juego = $_SESSION['id_juego'];
switch ($accion) {
    case 'iniciar_sorteo':
        try {
            conectar_db();

            $rs = sql("  SELECT
                        count(*) as CANTIDAD
                    FROM
                        T_HIST_SITUACION_SORTEO
                    WHERE
                        sorteo       = ?
                        and id_juego = ?
                        and estado   = 'I'", array($sorteo, $id_juego));
            $row = siguiente($rs);
            sql("  INSERT INTO SGS.T_HIST_SITUACION_SORTEO (
                ESTADO,
                SITUACION,
                SORTEO,
                ID_JUEGO
            ) VALUES (
                ?,
                ?,
                ?,
                ?
            )", array('I', 'Se ' . ($row->CANTIDAD > 0 ? ' reinicio ' : ' inicio ') . ' el sorteo', $sorteo, $id_juego));

            sql("DECLARE
                    l_jobno pls_integer;
                BEGIN
                dbms_job.submit( l_jobno,
                                'BEGIN SGS.PR_EXTRACTO_ONLINE(); END;',
                                sysdate + interval '1' second );
                commit;
                END;");

            $mensaje = array("tipo" => "success");
        } catch (exception $e) {
            $mensaje = array("mensaje" => "Error al iniciar: " . $db->ErrorMsg(), "tipo" => "error");
        }
        header('Content-Type: application/json');
        die(json_encode($mensaje));
        break;
    case 'sorteo_demorado':
        try {
            conectar_db();

            sql("  INSERT INTO SGS.T_HIST_SITUACION_SORTEO (
                ESTADO,
                SITUACION,
                SORTEO,
                ID_JUEGO
            ) VALUES (
                ?,
                ?,
                ?,
                ?
            )", array('P', 'El sorteo se encuentra demorado, ', $sorteo, $id_juego));
            $mensaje = array("tipo" => "success");
        } catch (exception $e) {
            $mensaje = array("mensaje" => "Error al iniciar: " . $db->ErrorMsg(), "tipo" => "error");
        }
        header('Content-Type: application/json');
        die(json_encode($mensaje));
        break;
    case 'detener_sorteo':
        try {
            conectar_db();
            $descripcion = $_POST['descripcion'];
            $situacion   = $_POST['situacion'];

            sql("  INSERT INTO SGS.T_HIST_SITUACION_SORTEO (
                ESTADO,
                SITUACION,
                SORTEO,
                ID_JUEGO
            ) VALUES (
                ?,
                ?,
                ?,
                ?
            )", array($situacion, 'Se detuvo el sorteo, ' . $descripcion, $sorteo, $id_juego));
            $mensaje = array("tipo" => "success");
        } catch (exception $e) {
            $mensaje = array("mensaje" => "Error al iniciar: " . $db->ErrorMsg(), "tipo" => "error");
        }
        header('Content-Type: application/json');
        die(json_encode($mensaje));
        break;
    case 'finalizar_sorteo':
        try {
            conectar_db();
            sql("  INSERT INTO SGS.T_HIST_SITUACION_SORTEO (
                ESTADO,
                SITUACION,
                SORTEO,
                ID_JUEGO
            ) VALUES (
                ?,
                ?,
                ?,
                ?
            )", array('F', 'Se finalizo el sorteo', $sorteo, $id_juego));
            $mensaje = array("tipo" => "success");
        } catch (exception $e) {
            $mensaje = array("mensaje" => "Error al iniciar: " . $db->ErrorMsg(), "tipo" => "error");
        }
        header('Content-Type: application/json');
        die(json_encode($mensaje));
        break;
    case 'es_jefe_sorteo':
        try {
            conectar_db();
            //$db->debug  = true;
            $id_usuario = 'DU' . $_SESSION['dni'];
            $rs         = sql(" SELECT
                            *
                        FROM
                            SGS.T_SORTEO
                        WHERE ID_JEFE=?
                        and id_juego=?
                        and sorteo= ?", array($id_usuario, $id_juego, $sorteo));
            if ($rs->RecordCount() > 0) {
                $mensaje = array("esJefe" => true);
            } else {
                $mensaje = array("esJefe" => false);
            }

        } catch (exception $e) {
            $mensaje = array("mensaje" => "Error al iniciar: " . $db->ErrorMsg(), "tipo" => "error");
        }
        header('Content-Type: application/json');
        die(json_encode($mensaje));
        break;
    case 'situacion_actual':
        try {
            conectar_db();
            $rs = sql("   SELECT
                        *
                    FROM
                        (
                            SELECT
                                ID,
                                SITUACION,
                                SORTEO,
                                ID_JUEGO,
                                ESTADO
                            FROM
                                SGS.T_HIST_SITUACION_SORTEO
                            WHERE ID_JUEGO = ?
                            AND SORTEO = ?
                            ORDER BY
                                ID DESC
                        )
                    WHERE
                        ROWNUM = 1", array($id_juego, $sorteo));
            if ($row = siguiente($rs)) {
                $mensaje = array("tipo" => "success", "situacion" => $row->ESTADO);
            } else {
                $mensaje = array("tipo" => "success", "situacion" => $row->ESTADO);
            }

        } catch (exception $e) {
            $mensaje = array("mensaje" => "Error al iniciar: " . $db->ErrorMsg(), "tipo" => "error");
        }
        header('Content-Type: application/json');
        die(json_encode($mensaje));
        break;
    case 'listar_eventos':
        try {
            conectar_db();
            $rs = sql("SELECT
                    ID,
                    SITUACION,
                    SORTEO,
                    ID_JUEGO,
                    ESTADO,
                    TO_CHAR(FECHA,'DD/MM/YYYY HH24:MI:SS') AS FECHA_EVENTO
                FROM
                    SGS.T_HIST_SITUACION_SORTEO
                WHERE SORTEO = ?
                AND ID_JUEGO = ?
                ORDER BY ID DESC", array($sorteo, $id_juego));
            if ($rs->RecordCount() == 0) {
                die(info('Sin notificaciones'));
            }
            while ($row = siguiente($rs)) {
                $alert = ($row->ESTADO == 'I') ? 'class="alert alert-success"' : (($row->ESTADO == 'S' || $row->ESTADO == 'C' || $row->ESTADO == 'D') ? 'class="alert alert-error"' : 'class="alert alert-info"');
                echo '<div ' . $alert . ' style="text-align: left;"><strong>' . $row->FECHA_EVENTO . '</strong> - ' . $row->SITUACION . '</div>';
            }
        } catch (exception $e) {
            die(error($db->ErrorMsg()));
        }
        break;
    default:
        # code...
        break;
}
