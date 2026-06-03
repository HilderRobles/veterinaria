<?php

namespace Tests\Cliente\Aplicacion;

use App\Cliente\Aplicacion\EnviadorNotificaciones;
use App\Cliente\Aplicacion\RecuperarContrasenaPeticion;
use App\Cliente\Aplicacion\SolicitarRecuperacionContrasena;
use App\Cliente\Dominio\Cliente;
use App\Cliente\Dominio\RepositorioCliente; // 🟢 CORREGIDO: Ruta sin el sub-namespace duplicado
use App\Cliente\Dominio\ObjetoValor\ClienteId;
use App\Cliente\Dominio\ObjetoValor\CorreoElectronico;
use App\Cliente\Dominio\ObjetoValor\Telefono;
use App\Cliente\Dominio\ObjetoValor\Contrasena;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\UsesClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(SolicitarRecuperacionContrasena::class)]
#[UsesClass(ClienteId::class)]
#[UsesClass(RecuperarContrasenaPeticion::class)]
#[UsesClass(Cliente::class)]
#[UsesClass(CorreoElectronico::class)]
#[UsesClass(Telefono::class)]
#[UsesClass(Contrasena::class)]
class SolicitarRecuperacionContrasenaTest extends TestCase {

    private $repositorioMock;
    private $enviadorMock;
    private SolicitarRecuperacionContrasena $casoUso;

    protected function setUp(): void {
        // 🟢 Ahora encuentra perfectamente la interfaz legítima para fabricar el Mock
        $this->repositorioMock = $this->createMock(RepositorioCliente::class);
        $this->enviadorMock = $this->createMock(EnviadorNotificaciones::class);
        $this->casoUso = new SolicitarRecuperacionContrasena($this->repositorioMock, $this->enviadorMock);
    }

    public function test_debe_enviar_correo_si_el_usuario_existe(): void {
        $peticion = new RecuperarContrasenaPeticion("existe@mail.com");

        // 🟢 SOLUCIÓN: Instanciamos un Cliente REAL para evitar los bloqueos de PHPUnit con clases 'final'
        $clienteReal = new Cliente(
            "Juan Dueño",
            new CorreoElectronico("existe@mail.com"),
            new Telefono("987654321"), // Celular local válido
            new Contrasena("hash_seguro"),
            "cliente",
            new ClienteId(1)
        );

        $this->repositorioMock->method('buscarPorCorreoElectronico')->willReturn($clienteReal);
        $this->enviadorMock->expects($this->once())->method('enviarCorreoRestablecimiento');

        $this->casoUso->ejecutar($peticion);
    }

    public function test_debe_terminar_silenciosamente_si_el_usuario_no_existe(): void {
        $peticion = new RecuperarContrasenaPeticion("noexiste@mail.com");

        $this->repositorioMock->method('buscarPorCorreoElectronico')->willReturn(null);
        $this->enviadorMock->expects($this->never())->method('enviarCorreoRestablecimiento');

        $this->casoUso->ejecutar($peticion);
    }
}