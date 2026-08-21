<?php

declare(strict_types=1);

namespace Siga\Participantes;

use InvalidArgumentException;
use Siga\Contratos\Evaluable;
use Siga\Contratos\Exportable;
use Siga\Traits\RegistraBitacora;

abstract class Participante implements Exportable
{
    use RegistraBitacora;

    /** @var float[] */
    private array $notas = [];

    protected function __construct(
        private readonly string $carnet,
        private string $nombre,
        private readonly Evaluable $esquema,
    ) {
        $this->registrar("Alta de participante {$carnet} ({$nombre}).");
    }

    public function carnet(): string
    {
        return $this->carnet;
    }

    public function nombre(): string
    {
        return $this->nombre;
    }

    public function esquema(): Evaluable
    {
        return $this->esquema;
    }

    public function agregarNota(float $nota): void
    {
        if ($nota < 0 || $nota > 10) {
            throw new InvalidArgumentException(
                "Nota fuera de rango (0-10): {$nota}."
            );
        }

        $this->notas[] = $nota;
        $this->registrar("Nota {$nota} registrada para {$this->carnet}.");
    }

    /** @return float[] */
    public function notas(): array
    {
        return $this->notas;
    }

    /**
     * El cálculo se delega en el esquema (polimorfismo de esquemas):
     * el participante no sabe si es simple, ponderado o mejores N.
     */
    public function promedio(): float
    {
        return $this->esquema->calcular($this->notas);
    }

    abstract public function rol(): string;

    /** Etiqueta corta usada para serializar/deserializar en JSON. */
    abstract public static function tipo(): string;

    public function exportar(): string
    {
        return (string) json_encode([
            'tipo'   => static::tipo(),
            'carnet' => $this->carnet,
            'nombre' => $this->nombre,
            'rol'    => $this->rol(),
            'notas'  => $this->notas,
        ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    }
}
