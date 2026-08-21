<?php

declare(strict_types=1);

namespace Siga\Reportes;

use Dompdf\Dompdf;
use Dompdf\Options;
use RuntimeException;
use Siga\Contratos\ExportadorReporte;
use Siga\Participantes\Participante;
use Throwable;

/**
 * Genera reportes/reporte.pdf a partir de una plantilla HTML (Dompdf).
 */
final class ExportadorPDF implements ExportadorReporte
{
    public function __construct(
        private readonly string $institucion,
        private readonly string $ciclo,
    ) {
    }

    public function exportar(array $participantes, string $rutaDestino): void
    {
        $this->asegurarDestino($rutaDestino);

        try {
            $filas = '';

            foreach ($participantes as $p) {
                $filas .= sprintf(
                    '<tr><td>%s</td><td>%s</td><td>%s</td><td style="text-align:center;">%.2f</td></tr>',
                    htmlspecialchars($p->carnet(), ENT_QUOTES, 'UTF-8'),
                    htmlspecialchars($p->nombre(), ENT_QUOTES, 'UTF-8'),
                    htmlspecialchars($p->rol(), ENT_QUOTES, 'UTF-8'),
                    $p->promedio()
                );
            }

            // Encabezado institucional + ciclo + fecha de generación (RT-44).
            $html = <<<HTML
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: Helvetica, Arial, sans-serif; font-size: 12px; color: #222; }
        header { border-bottom: 3px solid #1a3c6e; padding-bottom: 8px; margin-bottom: 16px; }
        h1 { font-size: 20px; color: #1a3c6e; margin: 0; }
        h2 { font-size: 14px; color: #444; margin: 4px 0 0 0; font-weight: normal; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th { background-color: #1a3c6e; color: #ffffff; padding: 6px 8px; text-align: left; }
        td { border-bottom: 1px solid #cccccc; padding: 5px 8px; }
        .pie { margin-top: 18px; font-size: 10px; color: #777; text-align: center; }
    </style>
</head>
<body>
    <header>
        <h1>{$this->institucion}</h1>
        <h2>Reporte de registro académico — Ciclo {$this->ciclo} — Generado el {$this->fecha()}</h2>
    </header>
    <table>
        <thead>
            <tr><th>Carnet</th><th>Nombre</th><th>Rol</th><th>Promedio</th></tr>
        </thead>
        <tbody>{$filas}</tbody>
    </table>
    <p class="pie">Documento generado automáticamente por SIGA-UNI v2.</p>
</body>
</html>
HTML;

            $opciones = new Options();
            $opciones->set('defaultFont', 'Helvetica');
            $opciones->set('isHtml5ParserEnabled', true);

            $dompdf = new Dompdf($opciones);
            $dompdf->loadHtml($html);
            $dompdf->setPaper('A4', 'portrait');
            $dompdf->render();

            $resultado = file_put_contents($rutaDestino, (string) $dompdf->output());

            if ($resultado === false) {
                throw new RuntimeException("No se pudo escribir el PDF en {$rutaDestino}.");
            }
        } catch (RuntimeException $e) {
            throw $e;
        } catch (Throwable $e) {
            throw new RuntimeException('No se pudo generar el PDF: ' . $e->getMessage(), 0, $e);
        }
    }

    public function extensionArchivo(): string
    {
        return 'pdf';
    }

    private function fecha(): string
    {
        return date('d/m/Y H:i');
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
