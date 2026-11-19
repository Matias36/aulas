<?php
require_once __DIR__ . '/no_cache.php';
// Devuelve las aulas con la misma forma que data/aulas.json:
//   { "daract2": { "pisos": [ { "nombre": "...", "aulas": [ {..., "horarios": [...] } ] } ] } }
//
// Reglas:
//  - Solo cuentan las clases del cuatrimestre vigente más las anuales.
//  - Las comisiones que comparten materia, aula, día y hora se juntan en un solo bloque.
//  - Los cambios por fecha del panel de administrador (clase cancelada o movida) se aplican a la semana que muestra la app.
//  - Las reservas de aula del panel (charlas, exposiciones) aparecen como eventos.
//  - Para probar otros momentos:  aulas.php?fecha=2026-04-20   o   aulas.php?cuat=1
require __DIR__ . '/conexion.php';
require __DIR__ . '/lib_horarios.php';

const NOMBRES_PISO = [0 => 'Planta baja', 1 => 'Primer piso', 2 => 'Segundo piso', 3 => 'Tercer piso'];

try {
    list($anio, $cuat) = periodo_vigente($pdo);
    header('X-Periodo: ' . $anio . '-' . $cuat);
    $carrerasDe = carreras_por_comision($pdo);
    $fechas = fechas_de_semana();
    $filas = filas_regulares($pdo, $anio, $cuat);

    // Cambios por fecha de la semana: $exc[horario][fecha]
    $exc = [];
    try {
        $st = $pdo->prepare(
            'SELECT x.horario_id, x.fecha, x.tipo, x.aula_id, x.inicio, x.fin, a.etiqueta, ed.nombre AS edificio
               FROM excepciones x
          LEFT JOIN aulas a ON a.id = x.aula_id
          LEFT JOIN edificios ed ON ed.id = a.edificio_id
              WHERE x.fecha BETWEEN ? AND ?'
        );
        $st->execute([$fechas[1], $fechas[6]]);
        foreach ($st->fetchAll() as $x) $exc[(int)$x['horario_id']][$x['fecha']] = $x;
    } catch (PDOException $e) { $exc = []; }   // si todavía no se importó la tabla, la app sigue andando

    $resultado = [];
    foreach ($filas as $f) {
        $dia = (int)$f['dia'];
        $fecha = isset($fechas[$dia]) ? $fechas[$dia] : null;
        $x = ($fecha !== null && isset($exc[(int)$f['id']][$fecha])) ? $exc[(int)$f['id']][$fecha] : null;
        if ($x === null) { $resultado[] = $f; continue; }

        $orig = $f;                       // lugar de siempre: queda marcado como cancelado o mudado
        $orig['estado'] = 'cancelada';
        if ($x['tipo'] !== 'movida') {
            $orig['nota'] = 'Cancelada';
            $resultado[] = $orig;
            continue;
        }
        $mismaAula = ($x['aula_id'] === $f['aula_id']);
        $nuevo = $f;                      // lugar nuevo
        $nuevo['aula_id'] = $x['aula_id'];
        if ($x['inicio'] !== null) $nuevo['inicio'] = $x['inicio'];
        if ($x['fin'] !== null)    $nuevo['fin'] = $x['fin'];
        $nuevo['estado'] = 'cambio';
        $nuevo['nota'] = $mismaAula ? 'Cambio de horario' : 'Cambio de aula (antes: ' . $f['aula'] . ')';
        $orig['nota'] = $mismaAula ? 'Antes: ' . hhmm($f['inicio']) . ' a ' . hhmm($f['fin']) : 'Pasó a ' . $x['edificio'] . ' ' . $x['etiqueta'];
        $resultado[] = $orig;
        $resultado[] = $nuevo;
    }

    // Reservas de aula (actividades de un día puntual) de la semana
    $st = $pdo->prepare(
        'SELECT h.id, h.aula_id, h.dia, h.inicio, h.fin, h.comision_id, h.titulo, h.organizador, h.fecha,
                a.etiqueta AS aula, a.edificio_id, e.nombre AS edificio,
                NULL AS numero, NULL AS materia_id, NULL AS materia, NULL AS docente
           FROM horarios h
           JOIN aulas a ON a.id = h.aula_id
           JOIN edificios e ON e.id = a.edificio_id
          WHERE h.comision_id IS NULL AND h.fecha BETWEEN ? AND ?'
    );
    $st->execute([$fechas[1], $fechas[6]]);
    foreach ($st->fetchAll() as $f) {
        $dia = (int)$f['dia'];
        if (isset($fechas[$dia]) && $fechas[$dia] === $f['fecha']) { $f['evento'] = true; $resultado[] = $f; }
    }

    $horariosDe = [];
    foreach (juntar_en_bloques($resultado, $carrerasDe) as $b) {
        list($docente, $comision, $carrera) = textos_bloque($b['docentes'], $b['numeros'], $b['carreras']);
        $h = [
            'dia' => $b['dia'], 'inicio' => $b['inicio'], 'fin' => $b['fin'],
            'materia' => $b['materia'], 'docente' => $docente,
            'comision' => $comision, 'carrera' => $carrera,
            'ids' => $b['ids'],
        ];
        if ($b['estado'] === 'cancelada') $h['cancelada'] = true;
        if ($b['estado'] === 'cambio')    $h['cambio'] = true;
        if ($b['nota'] !== '')            $h['nota'] = $b['nota'];
        if ($b['evento'])                 $h['evento'] = true;
        $horariosDe[$b['fila']['aula_id']][] = $h;
    }

    // Aulas agrupadas por edificio y piso
    $salida = [];
    $q = $pdo->query('SELECT id, edificio_id, piso, etiqueta, ancho, tipo, compu, detalle, horario_texto FROM aulas ORDER BY edificio_id, piso, orden, etiqueta');
    foreach ($q->fetchAll() as $f) {
        $a = ['id' => $f['id'], 'etiqueta' => $f['etiqueta']];
        if ((int)$f['ancho'] > 1)         $a['ancho'] = (int)$f['ancho'];
        if ($f['tipo'] !== 'aula')        $a['tipo'] = $f['tipo'];
        if ((int)$f['compu'] === 1)       $a['compu'] = true;
        if ($f['detalle'] !== null)       $a['detalle'] = $f['detalle'];
        if ($f['horario_texto'] !== null) $a['horarioTexto'] = $f['horario_texto'];
        $a['horarios'] = isset($horariosDe[$f['id']]) ? $horariosDe[$f['id']] : [];

        $ed = $f['edificio_id'];
        $p  = (int)$f['piso'];
        if (!isset($salida[$ed])) $salida[$ed] = ['pisos' => []];
        $ultimo = count($salida[$ed]['pisos']) - 1;
        if ($ultimo < 0 || $salida[$ed]['pisos'][$ultimo]['_piso'] !== $p) {
            $salida[$ed]['pisos'][] = ['_piso' => $p, 'nombre' => isset(NOMBRES_PISO[$p]) ? NOMBRES_PISO[$p] : ('Piso ' . $p), 'aulas' => []];
            $ultimo++;
        }
        $salida[$ed]['pisos'][$ultimo]['aulas'][] = $a;
    }
    foreach ($salida as $ed => $v) {
        foreach ($v['pisos'] as $i => $p) unset($salida[$ed]['pisos'][$i]['_piso']);
    }

    echo json_encode($salida ? $salida : new stdClass(), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
} catch (PDOException $e) {
    responder_error(500, 'Error al leer las aulas.');
}
