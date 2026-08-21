<?php

declare(strict_types=1);

namespace App;

class Autor
{
    public function __construct(
        private string $nombre,
        private string $nacionalidad
    ) {
    }

    public function presentarse(): string
    {
        return "{$this->nombre} ({$this->nacionalidad})";
    }

    public function nombre(): string
    {
        return $this->nombre;
    }
}
