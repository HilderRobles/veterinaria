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
use App\Cliente\Dominio\ObjetoValor\ClienteId;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\UsesClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(RegistrarCliente::class)]
#[UsesClass(ClienteId::class)]
#[UsesClass(RegistrarClientePeticion::class)]
#[UsesClass(Cliente::class)]
#[UsesClass(CorreoElectronico::class)]
#[UsesClass(Telefono::class)]
#[UsesClass(Contrasena::class)]
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
        // Petición con teléfono celular local válido (9 dígitos, empieza con 9)
        $peticion = new RegistrarClientePeticion("Pepe", "pepe@mail.com", "923456789", "Clave");

        // El correo está libre
        $this->repositorioMock->method('buscarPorCorreoElectronico')->willReturn(null);
        
        // 🟢 SOLUCIÓN: El cifrador devuelve un objeto Contrasena REAL, no un mock de una clase final
        $contrasenaCifradaReal = new Contrasena("hash_seguro_123");
        $this->cifradorMock->method('cifrar')->willReturn($contrasenaCifradaReal);

        // Aseguramos que se intente persistir en la BD
        $this->repositorioMock->expects($this->once())->method('guardar');

        $this->casoUso->ejecutar($peticion);
    }

    public function test_debe_fallar_si_el_correo_ya_existe(): void {
        $peticion = new RegistrarClientePeticion("Pepe", "pepe@mail.com", "923456789", "Clave");

        // 🟢 SOLUCIÓN: Creamos un Cliente REAL para simular que el correo ya está ocupado.
        // Al usar 'new', evitamos que PHPUnit intente duplicar las propiedades internas que son 'final'
        $clienteExistenteReal = new Cliente(
            "Pepe Clon",
            new CorreoElectronico("pepe@mail.com"),
            new Telefono("999888777"),
            new Contrasena("hash_antiguo"),
            "cliente",
            new ClienteId(1)
        );

        $this->repositorioMock->method('buscarPorCorreoElectronico')->willReturn($clienteExistenteReal);

        $this->expectException(\DomainException::class);
        $this->expectExceptionMessage("El correo electrónico ya se encuentra registrado.");
        
        $this->casoUso->ejecutar($peticion);
    }
}