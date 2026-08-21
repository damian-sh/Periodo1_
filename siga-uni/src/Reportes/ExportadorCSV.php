<?php

declare(strict_types=1);

namespace Siga\Reportes;

use RuntimeException;
use SplFileObject;
use Siga\Contratos\ExportadorReporte;
use Siga\Participantes\Participante;

/**
 * Exporta el detalle de notas por estudiante a CSV usando fputcsv().
 */
final class ExportadorCSV implements ExportadorReporte
{
    public function exportar(array $participantes, string $rutaDestino): void
    {
        $this->asegurarDestino($rutaDestino);

        $archivo = fopen($rutaDestino, 'w');

        if ($archivo === false) {
            throw new RuntimeException("No se pudo abrir {$rutaDestino} para escritura.");
        }

        try {
            fputcsv($archivo, ['Carnet', 'Nombre', 'Rol', 'Promedio']);

            foreach ($participantes as $p) {
                fputcsv($archivo, [
                    $p->carnet(),
                    $p->nombre(),
                    $p->rol(),
                    number_format($p->promedio(), 2, '.', ''),
                ]);
            }
        } finally {
            fclose($archivo);
        }

        // Verificación (RT-37): releer con SplFileObject en modo iterable.
        $filas = 0;

        foreach (new SplFileObject($rutaDestino, 'r') as $linea) {
            if (trim((string) $linea) !== '') {
                $filas++;
            }
        }

        if ($filas !== count($participantes) + 1) {
            throw new RuntimeException("El CSV generado en {$rutaDestino} parece incompleto.");
        }
    }

    public function extensionArchivo(): string
    {
        return 'csv';
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
