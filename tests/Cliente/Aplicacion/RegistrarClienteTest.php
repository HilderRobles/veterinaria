<?php

namespace Tests\Cliente\Aplicacion;

use App\Cliente\Aplicacion\CifradorContrasena;
use App\Cliente\Aplicacion\RegistrarCliente;
use App\Cliente\Aplicacion\RegistrarClientePeticion;
use App\Cliente\Dominio\Cliente;
use App\Cliente\Dominio\RepositorioCliente;
use App\Cliente\Dominio\ObjetoValor\CorreoElectronico;
use App\Cliente\Dominio\ObjetoValor\Telefono;
use App\Cliente\Dominio\ObjetoValor\Contrasena;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\UsesClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(RegistrarCliente::class)]
#[UsesClass(RegistrarClientePeticion::class)]
#[UsesClass(Cliente::class)]
#[UsesClass(CorreoElectronico::class)]
#[UsesClass(Telefono::class)]
class RegistrarClienteTest extends TestCase {

    private $repositorioMock;
    private $cifradorMock;
    private RegistrarCliente $casoUso;

    protected function setUp(): void {
        $this->repositorioMock = $this->createMock(RepositorioCliente::class);
        $this->cifradorMock = $this->createMock(CifradorContrasena::class);
        $this->casoUso = new RegistrarCliente($this->repositorioMock, $this->cifradorMock);
    }

    public function test_debe_registrar_cliente_exitosamente(): void {
        $peticion = new RegistrarClientePeticion("Pepe", "pepe@mail.com", "1234567890", "Clave");

        $this->repositorioMock->method('buscarPorCorreoElectronico')->willReturn(null);
        $this->cifradorMock->method('cifrar')->willReturn($this->createMock(Contrasena::class));

        $this->repositorioMock->expects($this->once())->method('guardar');

        $this->casoUso->ejecutar($peticion);
    }

    public function test_debe_fallar_si_el_correo_ya_existe(): void {
        $peticion = new RegistrarClientePeticion("Pepe", "pepe@mail.com", "1234567890", "Clave");

        $this->repositorioMock->method('buscarPorCorreoElectronico')->willReturn($this->createMock(Cliente::class));

        $this->expectException(\DomainException::class);
        $this->expectExceptionMessage("El correo electrónico ya se encuentra registrado.");
        
        $this->casoUso->ejecutar($peticion);
    }
}