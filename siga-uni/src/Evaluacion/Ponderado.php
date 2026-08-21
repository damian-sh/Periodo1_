<?php

declare(strict_types=1);

namespace Siga\Evaluacion;

use Siga\Contratos\Evaluable;

final class Ponderado implements Evaluable
{
    /**
     * @param float[] $ponderaciones pesos alineados por índice con las notas.
     */
    public function __construct(private readonly array $ponderaciones)
    {
    }

    public function calcular(array $notas): float
    {
        $acumulado = 0.0;
        $pesoTotal = 0.0;

        foreach ($this->ponderaciones as $i => $peso) {
            if (!isset($notas[$i])) {
                break;
            }
            $acumulado += $notas[$i] * $peso;
            $pesoTotal += $peso;
        }

        return $pesoTotal > 0 ? $acumulado / $pesoTotal : 0.0;
    }

    public function descripcion(): string
    {
        return 'Ponderado (' . implode(', ', $this->ponderaciones) . ')';
    }
}
