<?php

declare(strict_types=1);

namespace Siga\Contratos;

/**
 * Esquema de evaluación intercambiable: el participante calcula su
 * promedio sin conocer la estrategia concreta (Strategy).
 */
interface Evaluable
{
    /**
     * @param float[] $notas
     */
    public function calcular(array $notas): float;

    public function descripcion(): string;
}
