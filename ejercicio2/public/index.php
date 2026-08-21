<?php

declare(strict_types=1);

require __DIR__ . '/../vendor/autoload.php';

use App\Autor;
use App\Biblioteca;
use App\Libro;

$autor1 = new Autor("Gabriel García Márquez", "Colombia");
$autor2 = new Autor("Isabel Allende", "Chile");

$biblioteca = new Biblioteca();

$biblioteca->agregarLibro(new Libro("Cien años de soledad", 1967, $autor1));
$biblioteca->agregarLibro(new Libro("La casa de los espíritus", 1982, $autor2));

$biblioteca->listar();

echo "Libros de García Márquez:" . PHP_EOL;
$biblioteca->buscarPorAutor("Gabriel García Márquez");
