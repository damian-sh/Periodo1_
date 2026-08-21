<?php

declare(strict_types=1);

namespace Siga\Participantes;

use Siga\Contratos\Evaluable;
use Siga\Evaluacion\PromedioSimple;

final class EstudiantePregrado extends Participante
{
    public function __construct(
        string $carnet,
        string $nombre,
        ?Evaluable $esquema = null,
    ) {
        parent::__construct($carnet, $nombre, $esquema ?? new PromedioSimple());
    }

    public function rol(): string
    {
        return 'Estudiante de pregrado';
    }

    public static function tipo(): string
    {
        return 'pregrado';
    }
}
