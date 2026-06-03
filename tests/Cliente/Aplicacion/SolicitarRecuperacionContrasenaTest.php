<?php

namespace Tests\Cliente\Aplicacion;

use App\Cliente\Aplicacion\EnviadorNotificaciones;
use App\Cliente\Aplicacion\RecuperarContrasenaPeticion;
use App\Cliente\Aplicacion\SolicitarRecuperacionContrasena;
use App\Cliente\Dominio\Cliente;
use App\Cliente\Dominio\RepositorioCliente;
use App\Cliente\Dominio\ObjetoValor\CorreoElectronico;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\UsesClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(SolicitarRecuperacionContrasena::class)]
#[UsesClass(RecuperarContrasenaPeticion::class)]
#[UsesClass(CorreoElectronico::class)]
class SolicitarRecuperacionContrasenaTest extends TestCase {

    private $repositorioMock;
    private $enviadorMock;
    private SolicitarRecuperacionContrasena $casoUso;

    protected function setUp(): void {
        $this->repositorioMock = $this->createMock(RepositorioCliente::class);
        $this->enviadorMock = $this->createMock(EnviadorNotificaciones::class);
        $this->casoUso = new SolicitarRecuperacionContrasena($this->repositorioMock, $this->enviadorMock);
    }

    public function test_debe_enviar_correo_si_el_usuario_existe(): void {
        $peticion = new RecuperarContrasenaPeticion("existe@mail.com");

        $this->repositorioMock->method('buscarPorCorreoElectronico')->willReturn($this->createMock(Cliente::class));
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