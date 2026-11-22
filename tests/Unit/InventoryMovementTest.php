<?php

namespace Tests\Unit;

use App\Models\InventoryMovement;
use PHPUnit\Framework\TestCase;

class InventoryMovementTest extends TestCase
{
    public function test_muestra_el_nombre_del_concepto_del_kardex(): void
    {
        $movement = new InventoryMovement(['concept' => 'saldo_inicial']);

        $this->assertSame('Saldo inicial', $movement->conceptLabel());
    }
}
