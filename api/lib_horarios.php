<?php
// Funciones compartidas por aulas.php y seguimientos.php

const PARTICULAS = ['de', 'del', 'di', 'da', 'van', 'dell', 'la', 'los'];

function hhmm($t) { return substr($t, 0, 5); }

// "Estarellas Victoria" -> "Estarellas"; "De Rose Roberto" -> "De Rose"
function apellido($nombre) {
    $p = preg_split('/\s+/', trim($nombre));
    $a = $p[0];
    if (count($p) > 1 && in_array(strtolower($p[0]), PARTICULAS, true)) $a .= ' ' . $p[1];
    return $a;
}

// ["COM 1","COM 2"] -> "COM 1 y 2" ; tres -> "COM 1, 2 y 3"
function texto_comisiones($numeros) {
    $nums = [];
    $otros = [];
    foreach ($numeros as $n) {
        if (preg_match('/^COM\s+(\d+)$/i', $n, $m)) $nums[(int)$m[1]] = true;
        else $otros[$n] = true;
    }
    $nums = array_keys($nums);
    sort($nums);
    $partes = [];
    if ($nums) {
        $ultimo = array_pop($nums);
        $partes[] = 'COM ' . ($nums ? implode(', ', $nums) . ' y ' : '') . $ultimo;
    }
    foreach (array_keys($otros) as $o) $partes[] = $o;
    return implode(' y ', $partes);
}

// "Diseño Multimedial" -> "Dis. Multimedial"; "Lic. en Economía" -> "Lic. Economía"
function carrera_corta($n) {
    $n = preg_replace('/^Diseño (de |en )?/u', 'Dis. ', $n);
    $n = preg_replace('/^Lic\. en /u', 'Lic. ', $n);
    $n = preg_replace('/^Ingeniería /u', 'Ing. ', $n);
    $n = str_replace('Comunicación Visual', 'Com. Visual', $n);
    return $n;
}

// Textos de un bloque (varias comisiones juntas): [docente, comisión, carrera]
function textos_bloque($docentes, $numeros, $carreras) {
    // Un docente: nombre completo. Varios: apellidos separados por " / "
    if (count($docentes) > 1) {
        $aps = [];
        foreach ($docentes as $d) { $ap = apellido($d); if (!in_array($ap, $aps, true)) $aps[] = $ap; }
        $docente = implode(' / ', $aps);
    } else {
        $docente = $docentes ? $docentes[0] : '';
    }
    // Una o dos carreras: nombres cortos. Tres o más: "Varias carreras"
    $n = count($carreras);
    if ($n === 0)    $carrera = '';
    elseif ($n <= 2) $carrera = implode(', ', array_map('carrera_corta', $carreras));
    else             $carrera = 'Varias carreras';
    $comision = $numeros ? texto_comisiones($numeros) : '';
    return [$docente, $comision, $carrera];
}

// Período vigente: [año, cuatrimestre]. Para probar: ?fecha=2026-04-20  o  ?cuat=1
function periodo_vigente($pdo, $fecha = null) {
    $hoy = date('Y-m-d');
    if ($fecha !== null) $hoy = $fecha;
    elseif (isset($_GET['fecha']) && preg_match('/^\d{4}-\d{2}-\d{2}$/', $_GET['fecha'])) $hoy = $_GET['fecha'];

    $st = $pdo->prepare('SELECT anio, cuatrimestre FROM periodos WHERE ? BETWEEN inicio AND fin LIMIT 1');
    $st->execute([$hoy]);
    $periodo = $st->fetch();
    if (!$periodo) {   // fuera de cursada: el último período ya empezado, o el próximo
        $st = $pdo->prepare('SELECT anio, cuatrimestre FROM periodos WHERE inicio <= ? ORDER BY inicio DESC LIMIT 1');
        $st->execute([$hoy]);
        $periodo = $st->fetch();
    }
    if (!$periodo) {
        $periodo = $pdo->query('SELECT anio, cuatrimestre FROM periodos ORDER BY inicio ASC LIMIT 1')->fetch();
    }
    if (!$periodo) {
        $periodo = ['anio' => (int)substr($hoy, 0, 4), 'cuatrimestre' => ((int)substr($hoy, 5, 2) >= 8 ? 2 : 1)];
    }
    if (isset($_GET['cuat']) && in_array($_GET['cuat'], ['1', '2'], true)) $periodo['cuatrimestre'] = (int)$_GET['cuat'];
    return [(int)$periodo['anio'], (int)$periodo['cuatrimestre']];
}

// comision_id => [nombres de carreras]
function carreras_por_comision($pdo) {
    $res = [];
    $q = $pdo->query('SELECT cc.comision_id, ca.nombre FROM comision_carreras cc JOIN carreras ca ON ca.id = cc.carrera_id ORDER BY ca.id');
    foreach ($q->fetchAll() as $f) $res[(int)$f['comision_id']][] = $f['nombre'];
    return $res;
}

// Quita mayúsculas y tildes para comparar textos ("Morfología" == "morfologia")
function plano($s) {
    return strtolower(strtr($s, [
        'á' => 'a', 'é' => 'e', 'í' => 'i', 'ó' => 'o', 'ú' => 'u', 'ü' => 'u', 'ñ' => 'n',
        'Á' => 'a', 'É' => 'e', 'Í' => 'i', 'Ó' => 'o', 'Ú' => 'u', 'Ü' => 'u', 'Ñ' => 'n',
    ]));
}

// Junta filas de horarios en bloques (misma aula + día + hora + materia).
// Cada fila necesita: id, aula_id, dia, inicio, fin, comision_id, titulo, organizador, numero, materia_id, materia, docente.
// Cada bloque trae además 'fila' (la primera fila, por si hace falta algún dato extra).
function juntar_en_bloques($filas, $carrerasDe) {
    $bloques = [];
    foreach ($filas as $f) {
        $esClase = $f['comision_id'] !== null;
        $estado = isset($f['estado']) ? $f['estado'] : '';   // '', 'cancelada' o 'cambio' (lo pone el panel de administrador)
        $clave = $esClase
            ? $f['aula_id'] . '|' . $f['dia'] . '|' . $f['inicio'] . '|' . $f['fin'] . '|' . $f['materia_id'] . '|' . $estado
            : 'actividad|' . $f['id'] . '|' . $estado;
        if (!isset($bloques[$clave])) {
            $bloques[$clave] = [
                'estado' => $estado, 'nota' => isset($f['nota']) ? $f['nota'] : '', 'evento' => !empty($f['evento']),
                'fila' => $f, 'dia' => (int)$f['dia'],
                'inicio' => hhmm($f['inicio']), 'fin' => hhmm($f['fin']),
                'materia' => $esClase ? $f['materia'] : $f['titulo'],
                'docentes' => [], 'numeros' => [], 'carreras' => [], 'ids' => [],
            ];
        }
        $b = &$bloques[$clave];
        $b['ids'][] = (int)$f['id'];
        if ($esClase) {
            if ($f['docente'] !== null && !in_array($f['docente'], $b['docentes'], true)) $b['docentes'][] = $f['docente'];
            $b['numeros'][] = $f['numero'];
            foreach (($carrerasDe[(int)$f['comision_id']] ?? []) as $c) {
                if (!in_array($c, $b['carreras'], true)) $b['carreras'][] = $c;
            }
        } elseif ($f['organizador'] !== null) {
            $b['docentes'][] = $f['organizador'];
        }
        unset($b);
    }
    return array_values($bloques);
}

// Filas del horario regular (el que se repite cada semana) del período indicado, con aula y edificio
function filas_regulares($pdo, $anio, $cuat) {
    $st = $pdo->prepare(
        'SELECT h.id, h.aula_id, h.dia, h.inicio, h.fin, h.comision_id, h.titulo, h.organizador, h.fecha,
                a.etiqueta AS aula, a.edificio_id, e.nombre AS edificio,
                c.numero, m.id AS materia_id, m.nombre AS materia, d.nombre AS docente
           FROM horarios h
           JOIN aulas a ON a.id = h.aula_id
           JOIN edificios e ON e.id = a.edificio_id
      LEFT JOIN comisiones c ON c.id = h.comision_id
      LEFT JOIN materias   m ON m.id = c.materia_id
      LEFT JOIN docentes   d ON d.id = c.docente_id
          WHERE h.fecha IS NULL
            AND (h.comision_id IS NULL OR (c.anio = ? AND c.cuatrimestre IN (?, 3)))
       ORDER BY h.aula_id, h.dia, h.inicio, c.numero'
    );
    $st->execute([$anio, $cuat]);
    return $st->fetchAll();
}

// Fechas (AAAA-MM-DD) de lunes a sábado de la semana que muestra la app: la actual, o la próxima si hoy es domingo.
// Devuelve [1 => lunes, ..., 6 => sábado]. Para probar: ?fecha=2026-04-20
function fechas_de_semana() {
    $hoy = date('Y-m-d');
    if (isset($_GET['fecha']) && preg_match('/^\d{4}-\d{2}-\d{2}$/', $_GET['fecha'])) $hoy = $_GET['fecha'];
    $d = new DateTime($hoy);
    $n = (int)$d->format('N');
    if ($n === 7) { $d->modify('+1 day'); $n = 1; }
    $d->modify('-' . ($n - 1) . ' days');
    $fechas = [];
    for ($i = 1; $i <= 6; $i++) { $fechas[$i] = $d->format('Y-m-d'); $d->modify('+1 day'); }
    return $fechas;
}
