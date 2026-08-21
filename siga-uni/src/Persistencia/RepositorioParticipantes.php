<?php

declare(strict_types=1);

namespace Siga\Persistencia;

use RuntimeException;

final class RepositorioParticipantes
{
    public function __construct(private readonly string $rutaArchivo)
    {
    }

    public function existe(): bool
    {
        return file_exists($this->rutaArchivo);
    }

    /**
     * @param array<int, array<string, mixed>> $datos
     */
    public function guardar(array $datos): void
    {
        $carpeta = dirname($this->rutaArchivo);

        if (!is_dir($carpeta)) {
            mkdir($carpeta, 0777, true);
        }

        $json = json_encode(
            $datos,
            JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE
        );

        if ($json === false) {
            throw new RuntimeException('No se pudo serializar el registro académico.');
        }

        $resultado = file_put_contents($this->rutaArchivo, $json);

        if ($resultado === false) {
            throw new RuntimeException("No se pudo escribir en {$this->rutaArchivo}.");
        }
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function cargar(): array
    {
        if (!$this->existe()) {
            throw new RuntimeException("El archivo {$this->rutaArchivo} no existe.");
        }

        $contenido = file_get_contents($this->rutaArchivo);

        if ($contenido === false) {
            throw new RuntimeException("No se pudo leer {$this->rutaArchivo}.");
        }

        $datos = json_decode($contenido, true);

        if (!is_array($datos)) {
            throw new RuntimeException("El archivo {$this->rutaArchivo} está corrupto o malformado.");
        }

        return $datos;
    }
}
