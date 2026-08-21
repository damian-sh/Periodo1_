<?php

declare(strict_types=1);

namespace Siga\Contratos;

use Siga\Participantes\Participante;

interface ExportadorReporte
{
    /**
     * @param Participante[] $participantes
     */
    public function exportar(array $participantes, string $rutaDestino): void;

    public function extensionArchivo(): string;
}
