<?php

declare(strict_types=1);

namespace Siga\Participantes;

use Siga\Contratos\Evaluable;
use Siga\Evaluacion\Ponderado;

final class EstudiantePosgrado extends Participante
{
    public function __construct(
        string $carnet,
        string $nombre,
        ?Evaluable $esquema = null,
    ) {
        parent::__construct(
            $carnet,
            $nombre,
            $esquema ?? new Ponderado([0.3, 0.3, 0.4]),
        );
    }

    public function rol(): string
    {
        return 'Estudiante de posgrado';
    }

    public static function tipo(): string
    {
        return 'posgrado';
    }
}
