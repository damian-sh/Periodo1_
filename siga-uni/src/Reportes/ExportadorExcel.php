<?php

declare(strict_types=1);

namespace Siga\Reportes;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use RuntimeException;
use Siga\Contratos\ExportadorReporte;
use Siga\Participantes\Participante;
use Throwable;

/**
 * Genera reportes/reporte.xlsx con dos hojas: "Participantes" y "Resumen".
 */
final class ExportadorExcel implements ExportadorReporte
{
    public function exportar(array $participantes, string $rutaDestino): void
    {
        $this->asegurarDestino($rutaDestino);

        try {
            $libro = new Spreadsheet();

            // Hoja 1: Participantes.
            $hoja = $libro->getActiveSheet();
            $hoja->setTitle('Participantes');
            $hoja->fromArray([['Carnet', 'Nombre', 'Rol', 'Promedio']], null, 'A1');

            $fila = 2;

            foreach ($participantes as $p) {
                $hoja->fromArray([[
                    $p->carnet(),
                    $p->nombre(),
                    $p->rol(),
                    round($p->promedio(), 2),
                ]], null, "A{$fila}");
                $fila++;
            }

            // Estilo (RT-43): encabezados en negrita y columnas autoajustadas.
            $hoja->getStyle('A1:D1')->getFont()->setBold(true);
            foreach (['A', 'B', 'C', 'D'] as $columna) {
                $hoja->getColumnDimension($columna)->setAutoSize(true);
            }

            // Hoja 2: Resumen estadístico del ciclo.
            $resumen = $libro->createSheet();
            $resumen->setTitle('Resumen');
            $resumen->fromArray([['Indicador', 'Valor']], null, 'A1');

            $total = count($participantes);
            $promedios = array_map(fn (Participante $p) => $p->promedio(), $participantes);
            $promedioGeneral = $total > 0 ? array_sum($promedios) / $total : 0.0;

            $resumen->fromArray([['Total de participantes', $total]], null, 'A2');
            $resumen->fromArray([['Promedio general', round($promedioGeneral, 2)]], null, 'A3');
            $resumen->fromArray([['Fecha de generación', date('Y-m-d H:i')]], null, 'A4');

            if ($total > 0) {
                $mejor = array_reduce(
                    array_slice($participantes, 1),
                    fn (Participante $a, Participante $b) => $b->promedio() > $a->promedio() ? $b : $a,
                    $participantes[0]
                );
                $resumen->fromArray([[
                    'Mejor promedio',
                    sprintf('%s (%.2f)', $mejor->nombre(), $mejor->promedio()),
                ]], null, 'A5');
            }

            $resumen->getStyle('A1:B1')->getFont()->setBold(true);
            $resumen->getColumnDimension('A')->setAutoSize(true);
            $resumen->getColumnDimension('B')->setAutoSize(true);

            (new Xlsx($libro))->save($rutaDestino);
        } catch (Throwable $e) {
            throw new RuntimeException('No se pudo generar el Excel: ' . $e->getMessage(), 0, $e);
        }
    }

    public function extensionArchivo(): string
    {
        return 'xlsx';
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
