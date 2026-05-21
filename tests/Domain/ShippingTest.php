<?php

use PHPUnit\Framework\TestCase;
use Domain\Shipping;

class ShippingTest extends TestCase
{
    // Prueba débil original (solo casos normales)
    public function testFreeShippingForLargeAmounts()
    {
        $this->assertTrue(Shipping::isEligibleForFreeShipping(150.00));
        $this->assertFalse(Shipping::isEligibleForFreeShipping(50.00));
    }

    // NUEVA PRUEBA - Boundary Value Analysis (EL DESAFÍO)
    public function testFreeShippingExactlyAtBoundary()
    {
        // Valor límite exacto: debe ser false (no gratis)
        $this->assertFalse(Shipping::isEligibleForFreeShipping(100.00));
    }
}