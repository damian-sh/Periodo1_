<?php

declare(strict_types=1);

namespace App;

class Libro
{
    public function __construct(
        private string $titulo,
        private int $anio,
        private Autor $autor
    ) {
    }

    public function describir(): string
    {
        return "{$this->titulo} ({$this->anio}) — " . $this->autor->presentarse();
    }

    public function autor(): Autor
    {
        return $this->autor;
    }
}
