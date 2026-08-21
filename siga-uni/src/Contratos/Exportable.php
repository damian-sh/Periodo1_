<?php

declare(strict_types=1);

namespace Siga\Contratos;

/**
 * Contrato de exportación: todo participante sabe volverse texto (JSON).
 */
interface Exportable
{
    public function exportar(): string;
}
