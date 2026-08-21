<?php

declare(strict_types=1);

namespace App;

class Inventario
{
    private array $productos = [];

    public function agregarProducto(Producto $p): void
    {
        $this->productos[] = $p;
    }

    public function listar(): void
    {
        foreach ($this->productos as $producto) {
            echo $producto->describir() . PHP_EOL;
        }
    }

    public function valorTotal(): float
    {
        $total = 0.0;

        foreach ($this->productos as $producto) {
            $total += $producto->subtotal();
        }

        return $total;
    }
}
