<?php
namespace Domain;

class Shipping
{
    public static function isEligibleForFreeShipping(float $subtotal): bool
    {
        return $subtotal > 100.00;
    }
}