<?php

declare(strict_types=1);

namespace Siga\Persistencia;

use RuntimeException;
use Siga\Participantes\Docente;
use Siga\Participantes\EstudiantePosgrado;
use Siga\Participantes\EstudiantePregrado;
use Siga\Participantes\Participante;

/**
 * Reconstruye objetos Participante a partir de su representación JSON.
 */
final class FabricaParticipantes
{
    /**
     * @param array<string, mixed> $datos
     */
    public static function desdeArray(array $datos): Participante
    {
        $tipo = (string) ($datos['tipo'] ?? '');
        $carnet = (string) ($datos['carnet'] ?? '');
        $nombre = (string) ($datos['nombre'] ?? '');

        $participante = match ($tipo) {
            'pregrado' => new EstudiantePregrado($carnet, $nombre),
            'posgrado' => new EstudiantePosgrado($carnet, $nombre),
            'docente'  => new Docente($carnet, $nombre),
            default    => throw new RuntimeException("Tipo de participante desconocido: '{$tipo}'."),
        };

        foreach ((array) ($datos['notas'] ?? []) as $nota) {
            $participante->agregarNota((float) $nota);
        }

        return $participante;
    }
}
