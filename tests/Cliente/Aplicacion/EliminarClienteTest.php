<?php

namespace Tests\Cliente\Aplicacion;

use App\Cliente\Aplicacion\EliminarCliente;
use App\Cliente\Dominio\Cliente;
use App\Cliente\Dominio\RepositorioCliente;
use App\Cliente\Dominio\ObjetoValor\ClienteId;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\UsesClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(EliminarCliente::class)]
#[UsesClass(ClienteId::class)]
class EliminarClienteTest extends TestCase {

    private $repositorioMock;
    private EliminarCliente $casoUso;

    protected function setUp(): void {
        $this->repositorioMock = $this->createMock(RepositorioCliente::class);
        $this->casoUso = new EliminarCliente($this->repositorioMock);
    }

    public function test_debe_eliminar_exitosamente_si_existe(): void {
        $this->repositorioMock->method('buscarPorId')->willReturn($this->createMock(Cliente::class));
        $this->repositorioMock->expects($this->once())->method('eliminar');

        $this->casoUso->ejecutar(1);
    }

    public function test_debe_fallar_al_eliminar_si_no_existe(): void {
        $this->repositorioMock->method('buscarPorId')->willReturn(null);

        $this->expectException(\DomainException::class);
        $this->expectExceptionMessage("No se puede eliminar: El cliente no existe.");
        
        $this->casoUso->ejecutar(99);
    }
}