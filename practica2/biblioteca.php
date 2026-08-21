<?php

declare(strict_types=1);

// [VARIABLE] Constante global con el nombre de la institución.
const BIBLIOTECA = "Biblioteca Central Universitaria";

// [ABSTRACCIÓN] Define QUÉ hace todo material, no CÓMO lo hace
// cada tipo. No se puede instanciar con new.
// [CLASE] Modela cualquier material prestable.
abstract class Material
{
    // [ENCAPSULAMIENTO] Estado interno protegido; readonly impide
    // modificar el código del material después de crearlo.
    protected string $prestadoA = "";
    protected int $diasTranscurridos = 0;

    public function __construct(
        protected readonly string $codigo,
        protected string $titulo,
        protected int $anio
    ) {
        // [ENCAPSULAMIENTO] El objeto protege su propia regla de negocio.
        if ($anio < 1900 || $anio > 2026) {
            throw new InvalidArgumentException("Año inválido en {$codigo}.");
        }
    }

    // [ABSTRACCIÓN] Cada tipo define sus propios valores.
    abstract public function diasPrestamo(): int;
    abstract public function multaDiaria(): float;
    abstract public function describir(): string;

    // Método CONCRETO: se escribe una sola vez, pero el resultado
    // cambia según el tipo real del objeto.
    public function calcularMulta(): float
    {
        $exceso = $this->diasTranscurridos - $this->diasPrestamo();
        return $exceso > 0 ? $exceso * $this->multaDiaria() : 0.0;
    }

    public function prestar(string $persona, int $dias): void
    {
        $this->prestadoA = $persona;
        $this->diasTranscurridos = $dias;
    }

    public function getPrestadoA(): string { return $this->prestadoA; }
    public function getDias(): int { return $this->diasTranscurridos; }
}

// [HERENCIA] Libro reutiliza propiedades, validación y calcularMulta().
// [CLASE] Modela un libro del catálogo.
class Libro extends Material
{
    public function __construct(
        string $codigo,
        string $titulo,
        int $anio,
        // [ENCAPSULAMIENTO] Dato propio del libro, privado.
        private string $autor
    ) {
        // [HERENCIA] Se delega al padre la inicialización común.
        parent::__construct($codigo, $titulo, $anio);
    }

    public function diasPrestamo(): int { return 8; }
    public function multaDiaria(): float { return 0.25; }

    public function describir(): string
    {
        return sprintf("LIBRO [%s] %s — %s (%d)",
            $this->codigo, $this->titulo, $this->autor, $this->anio);
    }
}

// [HERENCIA] Revista especializa Material con sus propias reglas.
// [CLASE] Modela una revista del catálogo.
class Revista extends Material
{
    public function __construct(
        string $codigo,
        string $titulo,
        int $anio,
        // [ENCAPSULAMIENTO] Dato propio de la revista, privado.
        private int $edicion
    ) {
        // [HERENCIA] El constructor común vive en el padre.
        parent::__construct($codigo, $titulo, $anio);
    }

    public function diasPrestamo(): int { return 3; }
    public function multaDiaria(): float { return 0.50; }

    public function describir(): string
    {
        return sprintf("REVISTA [%s] %s (%d) | Edición %d",
            $this->codigo, $this->titulo, $this->anio, $this->edicion);
    }
}

// [ARREGLO] Arreglo indexado con todo el catálogo.
// [OBJETO] Cada elemento es una instancia creada con new.
$catalogo = [
    new Libro("L001", "Cien años de soledad", 1967, "G. García Márquez"),
    new Libro("L002", "El código limpio", 2008, "Robert C. Martin"),
    new Revista("R001", "National Geographic", 2025, 312),
    new Revista("R002", "Investigación y Ciencia", 2024, 587),
];

$catalogo[0]->prestar("Ana López", 12);
$catalogo[1]->prestar("Beto Ruiz", 6);
$catalogo[2]->prestar("Cira Mena", 5);
$catalogo[3]->prestar("Dani Soto", 3);

echo str_repeat("=", 58) . PHP_EOL;
echo "          " . BIBLIOTECA . PHP_EOL;
echo str_repeat("=", 58) . PHP_EOL;

$totalMultas = 0.0;

// [POLIMORFISMO] El arreglo contiene objetos de clases distintas.
// Se invoca el MISMO método sobre todos y cada uno responde
// según su propia implementación.
foreach ($catalogo as $material) {
    $multa = $material->calcularMulta();
    $exceso = $material->getDias() - $material->diasPrestamo();

    $condicion = match(true) {
        $exceso <= 0 => "A TIEMPO",
        default => "RETRASO"
    };

    echo $material->describir() . PHP_EOL;
    printf("  %s (%d días) | %s | Multa: \$%.2f%s",
        $material->getPrestadoA(), $material->getDias(),
        $condicion, $multa, PHP_EOL . PHP_EOL);

    $totalMultas += $multa;
}

printf("Total de multas: \$%.2f%s", $totalMultas, PHP_EOL);
