<?php

namespace Tests\Cliente\Aplicacion;

use App\Cliente\Aplicacion\AutenticarCliente;
use App\Cliente\Aplicacion\AutenticarClientePeticion;
use App\Cliente\Dominio\Cliente;
use App\Cliente\Dominio\RepositorioCliente;
use App\Cliente\Dominio\ObjetoValor\CorreoElectronico;
use App\Cliente\Dominio\ObjetoValor\Contrasena;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\UsesClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(AutenticarCliente::class)]
#[UsesClass(AutenticarClientePeticion::class)]
#[UsesClass(CorreoElectronico::class)]
class AutenticarClienteTest extends TestCase {

    private $repositorioMock;
    private AutenticarCliente $casoUso;

    protected function setUp(): void {
        $this->repositorioMock = $this->createMock(RepositorioCliente::class);
        $this->casoUso = new AutenticarCliente($this->repositorioMock);
    }

    public function test_debe_autenticar_exitosamente_y_retornar_arreglo(): void {
        $peticion = new AutenticarClientePeticion("test@correo.com", "Clave123");

        $contrasenaMock = $this->createMock(Contrasena::class);
        $contrasenaMock->method('verificar')->with("Clave123")->willReturn(true);

        $clienteMock = $this->createMock(Cliente::class);
        $clienteMock->method('obtenerContrasena')->willReturn($contrasenaMock);
        $clienteMock->method('mapearAArreglo')->willReturn(['id' => 1, 'nombre' => 'Juan']);

        $this->repositorioMock->method('buscarPorCorreoElectronico')->willReturn($clienteMock);

        $resultado = $this->casoUso->ejecutar($peticion);
        $this->assertEquals('Juan', $resultado['nombre']);
    }

    public function test_debe_lanzar_excepcion_si_el_usuario_no_existe(): void {
        $peticion = new AutenticarClientePeticion("no_existe@correo.com", "Clave123");
        $this->repositorioMock->method('buscarPorCorreoElectronico')->willReturn(null);

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage("Las credenciales de acceso son incorrectas.");

        $this->casoUso->ejecutar($peticion);
    }

    public function test_debe_lanzar_excepcion_si_la_contrasena_es_incorrecta(): void {
        $peticion = new AutenticarClientePeticion("test@correo.com", "ClaveErronea");

        $contrasenaMock = $this->createMock(Contrasena::class);
        $contrasenaMock->method('verificar')->with("ClaveErronea")->willReturn(false);

        $clienteMock = $this->createMock(Cliente::class);
        $clienteMock->method('obtenerContrasena')->willReturn($contrasenaMock);

        $this->repositorioMock->method('buscarPorCorreoElectronico')->willReturn($clienteMock);

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage("Las credenciales de acceso son incorrectas.");

        $this->casoUso->ejecutar($peticion);
    }
}