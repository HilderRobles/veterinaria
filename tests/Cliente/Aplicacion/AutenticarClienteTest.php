<?php

namespace Tests\Cliente\Aplicacion;

use App\Cliente\Aplicacion\AutenticarCliente;
use App\Cliente\Aplicacion\AutenticarClientePeticion;
use App\Cliente\Dominio\Cliente;
use App\Cliente\Dominio\RepositorioCliente;
use App\Cliente\Dominio\ObjetoValor\ClienteId;
use App\Cliente\Dominio\ObjetoValor\CorreoElectronico;
use App\Cliente\Dominio\ObjetoValor\Telefono;
use App\Cliente\Dominio\ObjetoValor\Contrasena;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\UsesClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(AutenticarCliente::class)]
#[UsesClass(AutenticarClientePeticion::class)]
#[UsesClass(Cliente::class)]
#[UsesClass(ClienteId::class)]
#[UsesClass(CorreoElectronico::class)]
#[UsesClass(Telefono::class)]
#[UsesClass(Contrasena::class)]
class AutenticarClienteTest extends TestCase {

    private $repositorioMock;
    private AutenticarCliente $casoUso;

    protected function setUp(): void {
        $this->repositorioMock = $this->createMock(RepositorioCliente::class);
        $this->casoUso = new AutenticarCliente($this->repositorioMock);
    }

    public function test_debe_autenticar_exitosamente_y_retornar_arreglo(): void {
        $peticion = new AutenticarClientePeticion("test@correo.com", "Clave123");

        // 🟢 SOLUCIÓN: Usamos password_hash real en una instancia legítima de Contrasena
        $hashValido = password_hash("Clave123", PASSWORD_BCRYPT);
        $contrasenaReal = new Contrasena($hashValido);

        // 🟢 Instanciamos un Cliente REAL con datos válidos de negocio
        $clienteReal = new Cliente(
            "Juan",
            new CorreoElectronico("test@correo.com"),
            new Telefono("987654321"), // Celular local peruano válido
            $contrasenaReal,
            "cliente",
            new ClienteId(1)
        );

        $this->repositorioMock->method('buscarPorCorreoElectronico')->willReturn($clienteReal);

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
        // El usuario intenta ingresar con "ClaveErronea"
        $peticion = new AutenticarClientePeticion("test@correo.com", "ClaveErronea");

        // El hash guardado originalmente pertenece a "ClaveCorrecta"
        $hashCorrecto = password_hash("ClaveCorrecta", PASSWORD_BCRYPT);
        $contrasenaReal = new Contrasena($hashCorrecto);

        // 🟢 Instancia REAL del cliente
        $clienteReal = new Cliente(
            "Juan",
            new CorreoElectronico("test@correo.com"),
            new Telefono("987654321"),
            $contrasenaReal,
            "cliente",
            new ClienteId(1)
        );

        $this->repositorioMock->method('buscarPorCorreoElectronico')->willReturn($clienteReal);

        // Al verificar "ClaveErronea" contra el hash de "ClaveCorrecta", fallará internamente
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage("Las credenciales de acceso son incorrectas.");

        $this->casoUso->ejecutar($peticion);
    }
}