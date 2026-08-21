<?php

declare(strict_types=1);

namespace Siga\Traits;

trait RegistraBitacora
{
    /** @var string[] */
    private array $bitacora = [];

    protected function registrar(string $evento): void
    {
        $this->bitacora[] = sprintf('[%s] %s', date('Y-m-d H:i:s'), $evento);
    }

    /** @return string[] */
    public function bitacora(): array
    {
        return $this->bitacora;
    }
}
