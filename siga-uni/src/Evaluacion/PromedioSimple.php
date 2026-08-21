<?php

declare(strict_types=1);

namespace Siga\Evaluacion;

use Siga\Contratos\Evaluable;

final class PromedioSimple implements Evaluable
{
    public function calcular(array $notas): float
    {
        return $notas === [] ? 0.0 : array_sum($notas) / count($notas);
    }

    public function descripcion(): string
    {
        return 'Promedio simple';
    }
}
