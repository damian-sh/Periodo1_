<?php

declare(strict_types=1);

require __DIR__ . '/../vendor/autoload.php';

use App\Inventario;
use App\Producto;

$inventario = new Inventario();

$inventario->agregarProducto(new Producto("Teclado", 25.50, 10));
$inventario->agregarProducto(new Producto("Mouse", 12.00, 15));

$inventario->listar();

echo "Valor total del inventario: $" . $inventario->valorTotal() . PHP_EOL;
