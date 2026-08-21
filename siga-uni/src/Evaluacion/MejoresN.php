<?php

declare(strict_types=1);

namespace Siga\Evaluacion;

use Siga\Contratos\Evaluable;

final class MejoresN implements Evaluable
{
    public function __construct(private readonly int $n)
    {
    }

    public function calcular(array $notas): float
    {
        rsort($notas);
        $mejores = array_slice($notas, 0, $this->n);

        return $mejores === [] ? 0.0 : array_sum($mejores) / count($mejores);
    }

    public function descripcion(): string
    {
        return "Mejores {$this->n} notas";
    }
}
