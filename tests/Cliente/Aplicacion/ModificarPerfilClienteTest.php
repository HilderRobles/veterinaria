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
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\UsesClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(ModificarPerfilCliente::class)]
#[UsesClass(ModificarPerfilClientePeticion::class)]
#[UsesClass(ClienteId::class)]
#[UsesClass(CorreoElectronico::class)]
#[UsesClass(Telefono::class)]
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
        $telefonoOriginal = new Telefono("1234567890");

        $clienteMock = $this->createMock(Cliente::class);
        $clienteMock->method('obtenerNombre')->willReturn("Carlos");
        $clienteMock->method('obtenerCorreoElectronico')->willReturn(new CorreoElectronico("viejo@mail.com"));
        $clienteMock->method('obtenerTelefono')->willReturn($telefonoOriginal);

        $this->repositorioMock->method('buscarPorId')->willReturn($clienteMock);
        $this->repositorioMock->method('buscarPorCorreoElectronico')->willReturn(null);

        $this->enviadorMock->expects($this->once())->method('enviarSmsAlerta');
        $this->repositorioMock->expects($this->once())->method('actualizarDatosContacto');

        $this->casoUso->ejecutar($peticion);
    }

    public function test_debe_notificar_por_email_si_cambia_el_telefono(): void {
        $peticion = new ModificarPerfilClientePeticion(1, null, null, "0987654321");
        $correoOriginal = new CorreoElectronico("seguro@mail.com");

        $clienteMock = $this->createMock(Cliente::class);
        $clienteMock->method('obtenerNombre')->willReturn("Carlos");
        $clienteMock->method('obtenerCorreoElectronico')->willReturn($correoOriginal);
        $clienteMock->method('obtenerTelefono')->willReturn(new Telefono("1234567890"));

        $this->repositorioMock->method('buscarPorId')->willReturn($clienteMock);

        $this->enviadorMock->expects($this->once())->method('enviarEmailAlerta');
        $this->repositorioMock->expects($this->once())->method('actualizarDatosContacto');

        $this->casoUso->ejecutar($peticion);
    }

    public function test_debe_fallar_si_el_nuevo_correo_ya_esta_duplicado_en_otro_usuario(): void {
        $peticion = new ModificarPerfilClientePeticion(1, null, "duplicado@mail.com", null);

        $clienteMock = $this->createMock(Cliente::class);
        $clienteMock->method('obtenerCorreoElectronico')->willReturn(new CorreoElectronico("original@mail.com"));

        $this->repositorioMock->method('buscarPorId')->willReturn($clienteMock);
        $this->repositorioMock->method('buscarPorCorreoElectronico')->willReturn($this->createMock(Cliente::class));

        $this->expectException(\DomainException::class);
        $this->expectExceptionMessage("El nuevo correo electrónico ya está registrado por otro propietario.");
        
        $this->casoUso->ejecutar($peticion);
    }

    public function test_debe_actualizar_solo_nombre_sin_disparar_alertas_cruzadas(): void {
        $peticion = new ModificarPerfilClientePeticion(1, "Nuevo Nombre", null, null);
        $correoOriginal = new CorreoElectronico("original@mail.com");
        $telefonoOriginal = new Telefono("1234567890");

        $clienteMock = $this->createMock(Cliente::class);
        $clienteMock->method('obtenerNombre')->willReturn("Nombre Viejo");
        $clienteMock->method('obtenerCorreoElectronico')->willReturn($correoOriginal);
        $clienteMock->method('obtenerTelefono')->willReturn($telefonoOriginal);

        $this->repositorioMock->method('buscarPorId')->willReturn($clienteMock);

        // Aseguramos que NINGUNA alerta cruzada se dispare
        $this->enviadorMock->expects($this->never())->method('enviarSmsAlerta');
        $this->enviadorMock->expects($this->never())->method('enviarEmailAlerta');
        
        $this->repositorioMock->expects($this->once())->method('actualizarDatosContacto');

        $this->casoUso->ejecutar($peticion);
    }
}