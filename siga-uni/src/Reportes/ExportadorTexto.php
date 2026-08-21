<?php

declare(strict_types=1);

namespace Siga\Reportes;

use RuntimeException;
use Siga\Contratos\ExportadorReporte;
use Siga\Participantes\Participante;

/**
 * Exporta el resumen estadístico del ciclo a un archivo de texto plano.
 */
final class ExportadorTexto implements ExportadorReporte
{
    public function exportar(array $participantes, string $rutaDestino): void
    {
        $this->asegurarDestino($rutaDestino);

        $total = count($participantes);
        $promedios = array_map(fn (Participante $p) => $p->promedio(), $participantes);
        $promedioGeneral = $total > 0 ? array_sum($promedios) / $total : 0.0;

        $lineas = [
            str_repeat('=', 60),
            'REPORTE DE REGISTRO ACADÉMICO (TEXTO PLANO)',
            'Generado: ' . date('Y-m-d H:i:s'),
            str_repeat('=', 60),
            sprintf('%-10s %-25s %-25s %8s', 'Carnet', 'Nombre', 'Rol', 'Promedio'),
            str_repeat('-', 60),
        ];

        foreach ($participantes as $p) {
            $lineas[] = sprintf(
                '%-10s %-25s %-25s %8.2f',
                $p->carnet(),
                $p->nombre(),
                $p->rol(),
                $p->promedio()
            );
        }

        $lineas[] = str_repeat('-', 60);
        $lineas[] = "Total de participantes: {$total}";
        $lineas[] = sprintf('Promedio general: %.2f', $promedioGeneral);

        if ($total > 0) {
            $mejor = array_reduce(
                array_slice($participantes, 1),
                fn (Participante $a, Participante $b) => $b->promedio() > $a->promedio() ? $b : $a,
                $participantes[0]
            );
            $lineas[] = sprintf(
                'Mejor promedio: %s (%s) con %.2f',
                $mejor->nombre(),
                $mejor->carnet(),
                $mejor->promedio()
            );
        }

        $contenido = implode(PHP_EOL, $lineas) . PHP_EOL;
        $resultado = file_put_contents($rutaDestino, $contenido);

        if ($resultado === false) {
            throw new RuntimeException("No se pudo escribir el reporte en {$rutaDestino}.");
        }
    }

    public function extensionArchivo(): string
    {
        return 'txt';
    }

    private function asegurarDestino(string $rutaDestino): void
    {
        $carpeta = dirname($rutaDestino);

        if (!is_dir($carpeta)) {
            mkdir($carpeta, 0777, true);
        }

        if (!is_writable($carpeta)) {
            throw new RuntimeException("Sin permisos de escritura en {$carpeta}.");
        }
    }
}
