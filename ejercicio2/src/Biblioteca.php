<?php

declare(strict_types=1);

namespace App;

class Biblioteca
{
    private array $libros = [];

    public function agregarLibro(Libro $l): void
    {
        $this->libros[] = $l;
    }

    public function listar(): void
    {
        foreach ($this->libros as $libro) {
            echo $libro->describir() . PHP_EOL;
        }
    }

    public function buscarPorAutor(string $nombreAutor): void
    {
        foreach ($this->libros as $libro) {
            // La búsqueda se hace sobre el objeto Autor contenido en cada Libro.
            if (strcasecmp($nombreAutor, $libro->autor()->nombre()) === 0) {
                echo $libro->describir() . PHP_EOL;
            }
        }
    }
}
