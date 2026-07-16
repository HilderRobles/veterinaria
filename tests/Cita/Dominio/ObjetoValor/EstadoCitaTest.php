<?php
declare(strict_types=1);
namespace Tests\Cita\Dominio\ObjetoValor;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use App\Cita\Dominio\ObjetoValor\EstadoCita;
use Exception;


#[CoversClass(EstadoCita::class)]
final class EstadoCitaTest extends TestCase
{
    public function test_crea_estado_valido(): void
    {
        $estado = new EstadoCita('pendiente');
        $this->assertSame('pendiente', $estado->getValor());
    }

    public function test_lanza_excepcion_con_estado_invalido(): void
    {
        $this->expectException(Exception::class);
        new EstadoCita('inexistente');
    }
}