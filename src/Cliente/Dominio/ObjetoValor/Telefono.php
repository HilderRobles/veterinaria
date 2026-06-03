<?php
namespace App\Cliente\Dominio\ObjetoValor;

/**
 * Objeto de Valor inmutable para el teléfono del cliente.
 * Restringido estrictamente a telefonía móvil local (Perú).
 */
final class Telefono {
    private readonly string $valor;

    public function __construct(string $valor) {
        // Removemos espacios en blanco accidentales que envíe el usuario
        $valorLimpio = trim($valor);

        // ^9       -> Obliga a empezar con el dígito 9 (Prefijo celular local)
        // [0-9]{8}$ -> Obliga a que le sigan exactamente 8 números más (Total = 9 dígitos)
        if (!preg_match('/^^9[0-9]{8}$/', $valorLimpio)) {
            throw new \DomainException("El teléfono debe ser un número celular local válido (9 dígitos y empezar con 9).");
        }

        $this->valor = $valorLimpio;
    }

    public function valor(): string { 
        return $this->valor; 
    }

    /**
     * Comparación semántica de objetos de valor.
     */
    public function esIgualA(Telefono $otro): bool {
        return $this->valor === $otro->valor();
    }
}