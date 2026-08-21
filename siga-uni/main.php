<?php

declare(strict_types=1);

require __DIR__ . '/vendor/autoload.php';

use Siga\Participantes\Docente;
use Siga\Participantes\EstudiantePosgrado;
use Siga\Participantes\EstudiantePregrado;
use Siga\Persistencia\FabricaParticipantes;
use Siga\Persistencia\RepositorioParticipantes;
use Siga\Reportes\ExportadorCSV;
use Siga\Reportes\ExportadorExcel;
use Siga\Reportes\ExportadorPDF;
use Siga\Reportes\ExportadorTexto;
use Siga\Servicios\ReporteCiclo;

const INSTITUCION = 'Universidad Nacional';
const CICLO = 'II-2026';

$repositorio = new RepositorioParticipantes(__DIR__ . '/data/participantes.json');

/**
 * Carga el registro previo si existe (o lo recrea desde cero).
 *
 * @return array<int, \Siga\Participantes\Participante>
 */
function obtenerParticipantes(RepositorioParticipantes $repositorio): array
{
    $cargar = false;

    if ($repositorio->existe()) {
        if (function_exists('readline')) {
            $respuesta = strtolower(trim((string) readline('data/participantes.json encontrado. ¿Cargar? (S/n): ')));
            $cargar = $respuesta !== 'n';
        } else {
            $cargar = true;
        }
    }

    if ($cargar) {
        try {
            $participantes = array_map(
                fn (array $datos) => FabricaParticipantes::desdeArray($datos),
                $repositorio->cargar()
            );
            echo 'Participantes cargados desde data/participantes.json' . PHP_EOL;

            return $participantes;
        } catch (RuntimeException $e) {
            echo "Aviso: {$e->getMessage()} Se recrearán los datos." . PHP_EOL;
        }
    }

    // Datos iniciales del ciclo (como en el ejercicio original de POO).
    $ana = new EstudiantePregrado('EP001', 'Ana Molina');
    foreach ([8.0, 9.0, 7.5] as $nota) {
        $ana->agregarNota($nota);
    }

    $luis = new EstudiantePregrado('EP002', 'Luis Ramírez');
    foreach ([6.5, 7.0, 8.0] as $nota) {
        $luis->agregarNota($nota);
    }

    $carla = new EstudiantePosgrado('PG010', 'Carla Núñez');
    foreach ([9.0, 8.5, 9.5] as $nota) {
        $carla->agregarNota($nota);
    }

    $mario = new Docente('DC100', 'Mario Solórzano');
    foreach ([7.0, 9.0, 8.5, 6.0] as $nota) {
        $mario->agregarNota($nota);
    }

    return [$ana, $luis, $carla, $mario];
}

$participantes = obtenerParticipantes($repositorio);

// POLIMORFISMO: ReporteCiclo recibe su exportador por inyección y no
// conoce su tipo concreto ni contiene condicionales por formato.
$reporte = new ReporteCiclo(
    INSTITUCION,
    CICLO,
    new ExportadorTexto(),
    $repositorio
);

echo PHP_EOL;
echo str_repeat('-', 60) . PHP_EOL;
echo 'PERSISTENCIA Y EXPORTACIÓN' . PHP_EOL;
echo str_repeat('-', 60) . PHP_EOL;

try {
    $reporte->generar($participantes, __DIR__ . '/reportes');
} catch (RuntimeException $e) {
    echo "No se pudo completar el reporte: {$e->getMessage()}" . PHP_EOL;
}

foreach ([new ExportadorCSV(), new ExportadorExcel(), new ExportadorPDF(INSTITUCION, CICLO)] as $exportador) {
    try {
        $ruta = __DIR__ . '/reportes/reporte.' . $exportador->extensionArchivo();
        $exportador->exportar($participantes, $ruta);
        echo "Reporte generado: {$ruta}" . PHP_EOL;
    } catch (RuntimeException $e) {
        echo 'No se pudo generar el reporte ' . $exportador->extensionArchivo()
            . ": {$e->getMessage()}" . PHP_EOL;
    }
}
