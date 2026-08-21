<?php

declare(strict_types=1);

namespace App;

class Producto
{
    public function __construct(
        private string $nombre,
        private float $precio,
        private int $cantidad
    ) {
    }

    public function describir(): string
    {
        return "Producto: {$this->nombre} | Precio: \${$this->precio} | Cantidad: {$this->cantidad}";
    }

    public function subtotal(): float
    {
        return $this->precio * $this->cantidad;
    }
}
