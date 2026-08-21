<?php

declare(strict_types=1);

namespace Siga\Participantes;

use Siga\Contratos\Evaluable;
use Siga\Evaluacion\MejoresN;

final class Docente extends Participante
{
    public function __construct(
        string $carnet,
        string $nombre,
        ?Evaluable $esquema = null,
    ) {
        parent::__construct($carnet, $nombre, $esquema ?? new MejoresN(3));
    }

    public function rol(): string
    {
        return 'Docente';
    }

    public static function tipo(): string
    {
        return 'docente';
    }
}
