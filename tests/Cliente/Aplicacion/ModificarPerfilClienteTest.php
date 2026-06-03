<?php

namespace Tests\Cliente\Aplicacion;

use App\Cliente\Aplicacion\EnviadorNotificaciones;
use App\Cliente\Aplicacion\ModificarPerfilCliente;
use App\Cliente\Aplicacion\ModificarPerfilClientePeticion;
use App\Cliente\Dominio\Cliente;
use App\Cliente\Dominio\RepositorioCliente;
use App\Cliente\Dominio\ObjetoValor\ClienteId;
use App\Cliente\Dominio\ObjetoValor\CorreoElectronico;
use App\Cliente\Dominio\ObjetoValor\Telefono;
use App\Cliente\Dominio\ObjetoValor\Contrasena;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\UsesClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(ModificarPerfilCliente::class)]
#[UsesClass(ModificarPerfilClientePeticion::class)]
#[UsesClass(ClienteId::class)]
#[UsesClass(CorreoElectronico::class)]
#[UsesClass(Telefono::class)]
#[UsesClass(Contrasena::class)]
#[UsesClass(Cliente::class)]
class ModificarPerfilClienteTest extends TestCase {

    private $repositorioMock;
    private $enviadorMock;
    private ModificarPerfilCliente $casoUso;

    protected function setUp(): void {
        $this->repositorioMock = $this->createMock(RepositorioCliente::class);
        $this->enviadorMock = $this->createMock(EnviadorNotificaciones::class);
        $this->casoUso = new ModificarPerfilCliente($this->repositorioMock, $this->enviadorMock);
    }

    public function test_debe_lanzar_excepcion_si_usuario_no_existe(): void {
        $peticion = new ModificarPerfilClientePeticion(999);
        $this->repositorioMock->method('buscarPorId')->willReturn(null);

        $this->expectException(\DomainException::class);
        $this->expectExceptionMessage("El propietario solicitado no existe en el sistema.");        
        $this->casoUso->ejecutar($peticion);
    }

    public function test_debe_notificar_por_sms_si_cambia_el_correo_y_no_esta_duplicado(): void {
        $peticion = new ModificarPerfilClientePeticion(1, null, "nuevo@mail.com", null);

        // 🟢 Instancia REAL en lugar de Mock
        $clienteReal = new Cliente(
            "Carlos",
            new CorreoElectronico("viejo@mail.com"),
            new Telefono("923456789"),
            new Contrasena("hash123"),
            "cliente",
            new ClienteId(1)
        );

        $this->repositorioMock->method('buscarPorId')->willReturn($clienteReal);
        $this->repositorioMock->method('buscarPorCorreoElectronico')->willReturn(null);

        $this->enviadorMock->expects($this->once())->method('enviarSmsAlerta');
        $this->repositorioMock->expects($this->once())->method('actualizarDatosContacto');

        $this->casoUso->ejecutar($peticion);
    }

    public function test_debe_notificar_por_email_si_cambia_el_telefono(): void {
        $peticion = new ModificarPerfilClientePeticion(1, null, null, "987654321");

        // 🟢 Instancia REAL en lugar de Mock
        $clienteReal = new Cliente(
            "Carlos",
            new CorreoElectronico("seguro@mail.com"),
            new Telefono("923456789"),
            new Contrasena("hash123"),
            "cliente",
            new ClienteId(1)
        );

        $this->repositorioMock->method('buscarPorId')->willReturn($clienteReal);

        $this->enviadorMock->expects($this->once())->method('enviarEmailAlerta');
        $this->repositorioMock->expects($this->once())->method('actualizarDatosContacto');

        $this->casoUso->ejecutar($peticion);
    }

    public function test_debe_fallar_si_el_nuevo_correo_ya_esta_duplicado_en_otro_usuario(): void {
        $peticion = new ModificarPerfilClientePeticion(1, null, "duplicado@mail.com", null);

        // 🟢 Ambos clientes se configuran como instancias REALES
        $clienteActualReal = new Cliente(
            "Carlos",
            new CorreoElectronico("original@mail.com"),
            new Telefono("923456789"),
            new Contrasena("hash123"),
            "cliente",
            new ClienteId(1)
        );

        $clienteDuplicadoReal = new Cliente(
            "Otro Propietario",
            new CorreoElectronico("duplicado@mail.com"),
            new Telefono("911222333"),
            new Contrasena("hash456"),
            "cliente",
            new ClienteId(2) // ID distinto detonará la regla de duplicados
        );

        $this->repositorioMock->method('buscarPorId')->willReturn($clienteActualReal);
        $this->repositorioMock->method('buscarPorCorreoElectronico')->willReturn($clienteDuplicadoReal);

        $this->expectException(\DomainException::class);
        $this->expectExceptionMessage("El nuevo correo electrónico ya está registrado por otro propietario.");
        
        $this->casoUso->ejecutar($peticion);
    }

    public function test_debe_actualizar_solo_nombre_sin_disparar_alertas_cruzadas(): void {
        $peticion = new ModificarPerfilClientePeticion(1, "Nuevo Nombre", null, null);

        // 🟢 Instancia REAL en lugar de Mock
        $clienteReal = new Cliente(
            "Nombre Viejo",
            new CorreoElectronico("original@mail.com"),
            new Telefono("923456789"),
            new Contrasena("hash123"),
            "cliente",
            new ClienteId(1)
        );

        $this->repositorioMock->method('buscarPorId')->willReturn($clienteReal);

        $this->enviadorMock->expects($this->never())->method('enviarSmsAlerta');
        $this->enviadorMock->expects($this->never())->method('enviarEmailAlerta');
        $this->repositorioMock->expects($this->once())->method('actualizarDatosContacto');

        $this->casoUso->ejecutar($peticion);
    }
    public function test_no_deberia_buscar_correo_duplicado_si_el_correo_es_nulo(): void {
        // Petición donde NO se envía un nuevo correo (es null)
        $peticion = new ModificarPerfilClientePeticion(1, "Solo Nombre", null, "987654321");

        $clienteReal = new Cliente(
            "Carlos",
            new CorreoElectronico("seguro@mail.com"),
            new Telefono("923456789"),
            new Contrasena("hash123"),
            "cliente",
            new ClienteId(1)
        );

        $this->repositorioMock->method('buscarPorId')->willReturn($clienteReal);

        // 🔥 AQUÍ MORIRÁ EL MUTANTE:
        // Aseguramos de manera estricta que el repositorio NUNCA reciba una llamada 
        // para comprobar correos si el usuario no solicitó cambiarlo.
        $this->repositorioMock->expects($this->never())->method('buscarPorCorreoElectronico');

        $this->casoUso->ejecutar($peticion);
    }
}