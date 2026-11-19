<?php
require_once __DIR__ . '/no_cache.php';
// Buscador de materias: GET buscar.php?q=morfo
// Busca por nombre de materia o de docente (sin importar tildes ni mayúsculas) entre las clases
// del cuatrimestre vigente y las anuales. Devuelve en qué aula, día y horario se cursa cada una.
require __DIR__ . '/conexion.php';
require __DIR__ . '/lib_horarios.php';

$q = isset($_GET['q']) ? trim(preg_replace('/\s+/u', ' ', (string)$_GET['q'])) : '';
if (!preg_match('/^.{2,60}$/u', $q)) { echo '[]'; exit; }   // muy corto o muy largo: sin resultados

$palabras = array_values(array_filter(explode(' ', plano($q))));

try {
    list($anio, $cuat) = periodo_vigente($pdo);
    $carrerasDe = carreras_por_comision($pdo);

    $st = $pdo->prepare(
        'SELECT h.id, h.aula_id, h.dia, h.inicio, h.fin, h.comision_id, h.titulo, h.organizador,
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
    $bloques = juntar_en_bloques($st->fetchAll(), $carrerasDe);
} catch (PDOException $e) {
    responder_error(500, 'Error al buscar.');
}

// Se queda con los bloques donde aparecen TODAS las palabras escritas (en la materia o en algún docente)
$hallados = [];
foreach ($bloques as $b) {
    $enMateria = plano($b['materia']);
    $todo = $enMateria . ' ' . plano(implode(' ', $b['docentes']));
    $ok = true;
    $enNombre = true;
    foreach ($palabras as $p) {
        if (strpos($todo, $p) === false) { $ok = false; break; }
        if (strpos($enMateria, $p) === false) $enNombre = false;
    }
    if (!$ok) continue;
    list($docente, $comision, $carrera) = textos_bloque($b['docentes'], $b['numeros'], $b['carreras']);
    $hallados[] = [
        'orden' => ($enNombre ? '0' : '1') . plano($b['materia']) . sprintf('%d%s', $b['dia'], $b['inicio']),
        'dato' => [
            'ids' => $b['ids'], 'materia' => $b['materia'],
            'dia' => $b['dia'], 'inicio' => $b['inicio'], 'fin' => $b['fin'],
            'edificioId' => $b['fila']['edificio_id'], 'edificio' => $b['fila']['edificio'],
            'aulaId' => $b['fila']['aula_id'], 'aula' => $b['fila']['aula'],
            'docente' => $docente, 'comision' => $comision, 'carrera' => $carrera,
        ],
    ];
}
usort($hallados, function ($x, $y) { return strcmp($x['orden'], $y['orden']); });

$salida = [];
foreach (array_slice($hallados, 0, 40) as $h) $salida[] = $h['dato'];
echo json_encode($salida, JSON_UNESCAPED_UNICODE);
