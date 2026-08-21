<?php

declare(strict_types=1);

namespace Siga\Servicios;

use RuntimeException;
use Siga\Contratos\ExportadorReporte;
use Siga\Persistencia\RepositorioParticipantes;
use Siga\Participantes\Participante;
use Siga\Traits\RegistraBitacora;

/**
 * Servicio de reporte del ciclo académico.
 *
 * Polimorfismo (RT-42/RT-45): recibe el exportador por inyección de
 * dependencias y NO contiene ningún if/switch que pregunte por el formato.
 */
final class ReporteCiclo
{
    use RegistraBitacora;

    public function __construct(
        private readonly string $institucion,
        private readonly string $ciclo,
        private readonly ExportadorReporte $exportador,
        private readonly ?RepositorioParticipantes $repositorio = null,
    ) {
    }

    /**
     * Imprime el reporte en consola, persiste el registro y delega la
     * exportación en el exportador inyectado.
     *
     * @param Participante[] $participantes
     */
    public function generar(array $participantes, string $carpetaReportes = 'reportes'): void
    {
        $this->imprimirEncabezado();

        foreach ($participantes as $p) {
            printf(
                "  %-10s %-25s %-25s %6.2f%s",
                $p->carnet(),
                $p->nombre(),
                $p->rol(),
                $p->promedio(),
                PHP_EOL
            );
        }

        $this->imprimirResumen($participantes);
        $this->persistir($participantes);

        $ruta = rtrim($carpetaReportes, '/\\') . '/reporte.' . $this->exportador->extensionArchivo();
        $this->exportador->exportar($participantes, $ruta);
        echo "Reporte generado: {$ruta}" . PHP_EOL;
    }

    /** @param Participante[] $participantes */
    private function imprimirEncabezado(array $participantes = []): void
    {
        echo str_repeat('=', 60) . PHP_EOL;
        echo "{$this->institucion} | Ciclo {$this->ciclo}" . PHP_EOL;
        echo 'REPORTE DE REGISTRO ACADÉMICO' . PHP_EOL;
        echo str_repeat('=', 60) . PHP_EOL;
        printf("  %-10s %-25s %-25s %6s%s", 'Carnet', 'Nombre', 'Rol', 'Prom.', PHP_EOL);
        echo str_repeat('-', 60) . PHP_EOL;
    }

    /** @param Participante[] $participantes */
    private function imprimirResumen(array $participantes): void
    {
        $total = count($participantes);
        $promedios = array_map(fn (Participante $p) => $p->promedio(), $participantes);
        $promedioGeneral = $total > 0 ? array_sum($promedios) / $total : 0.0;

        echo str_repeat('-', 60) . PHP_EOL;
        echo "  Total de participantes: {$total}" . PHP_EOL;
        printf('  Promedio general: %.2f%s', $promedioGeneral, PHP_EOL);

        if ($total > 0) {
            $mejor = array_reduce(
                array_slice($participantes, 1),
                fn (Participante $a, Participante $b) => $b->promedio() > $a->promedio() ? $b : $a,
                $participantes[0]
            );
            printf(
                '  Mejor promedio: %s (%s) con %.2f%s',
                $mejor->nombre(),
                $mejor->carnet(),
                $mejor->promedio(),
                PHP_EOL
            );
        }
    }

    /** @param Participante[] $participantes */
    private function persistir(array $participantes): void
    {
        if ($this->repositorio === null) {
            return;
        }

        try {
            $this->repositorio->guardar(
                array_map(
                    fn (Participante $p) => json_decode($p->exportar(), true),
                    $participantes
                )
            );
            echo 'Registro guardado en data/participantes.json' . PHP_EOL;
        } catch (RuntimeException $e) {
            echo "Aviso: no se pudo guardar el registro ({$e->getMessage()})" . PHP_EOL;
        }
    }
}
