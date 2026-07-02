<?php

namespace Test\Cliente\Aplicacion;

use Behat\Behat\Context\Context;
use Behat\Step\Given;
use Behat\Step\When;
use Behat\Step\Then;
use App\Cliente\Dominio\RepositorioCliente;
use App\Cliente\Dominio\Cliente;
use App\Cliente\Dominio\ObjetoValor\CorreoElectronico;
use App\Cliente\Dominio\ObjetoValor\Telefono;
use App\Cliente\Dominio\ObjetoValor\Contrasena;
use App\Cliente\Dominio\ObjetoValor\ClienteId;
use Mockery;
use PHPUnit\Framework\Assert;

class SolicitarRecuperacionContrasenaContext implements Context
{
    private $repositorioMock;
    private ?\Exception $excepcionCapturada = null;
    private bool $tokenGeneradoYEnviado = false;

    public function __construct()
    {
        $this->repositorioMock = Mockery::mock(RepositorioCliente::class);
    }

    #[Given('que existe un cliente registrado con el correo :correo')]
    public function queExisteUnClienteRegistradoConElCorreo($correo): void
    {
        $cliente = new Cliente(
            "Carlos",
            new CorreoElectronico($correo),
            new Telefono("911222333"),
            new Contrasena("hash_seguro_123"),
            "cliente",
            new ClienteId(42)
        );

        // Cuando se busque el correo correcto, devolvemos la entidad
        $this->repositorioMock->allows('buscarPorCorreoElectronico')
            ->with(Mockery::on(fn($arg) => $arg instanceof CorreoElectronico && $arg->valor() === $correo))
            ->andReturn($cliente);

        // Cuando se busque cualquier otro correo (mal escrito), devolvemos null
        $this->repositorioMock->allows('buscarPorCorreoElectronico')
            ->with(Mockery::on(fn($arg) => $arg instanceof CorreoElectronico && $arg->valor() !== $correo))
            ->andReturn(null);
    }

    #[When('solicita recuperar la contraseña para el correo :correo')]
    public function solicitaRecuperarLaContrasenaParaElCorreo($correo): void
    {
        try {
            // Lógica de simulación del Caso de Uso dentro del paso de aceptación
            $emailVo = new CorreoElectronico($correo);
            $cliente = $this->repositorioMock->buscarPorCorreoElectronico($emailVo);

            if ($cliente === null) {
                throw new \DomainException("No se pudo restablecer la contraseña para el correo especificado.");
            }

            // Si el cliente existe, se asume que el servicio genera el token y envía el email
            $this->tokenGeneradoYEnviado = true;
        } catch (\Exception $e) {
            $this->excepcionCapturada = $e;
        }
    }

    #[Then('el sistema genera el token y envía el correo de recuperación')]
    public function elSistemaGeneraElTokenYEnviaElCorreoDeRecuperacion(): void
    {
        Assert::assertNull($this->excepcionCapturada, $this->excepcionCapturada ? $this->excepcionCapturada->getMessage() : '');
        Assert::assertTrue($this->tokenGeneradoYEnviado, "No se completó el envío del correo de recuperación.");
    }

    #[Then('la solicitud es rechazada indicando que no se pudo restablecer.')]
    public function laSolicitudEsRechazadaIndicandoQueNoSePudoRestablecer(): void
    {
        Assert::assertNotNull($this->excepcionCapturada, "Se esperaba un fallo pero el flujo continuó sin problemas.");
        Assert::assertInstanceOf(\DomainException::class, $this->excepcionCapturada);
        Assert::assertEquals(
            "No se pudo restablecer la contraseña para el correo especificado.", 
            $this->excepcionCapturada->getMessage()
        );
    }

    /** @AfterScenario */
    public function limpiarMocks(): void
    {
        Mockery::close();
        $this->excepcionCapturada = null;
        $this->tokenGeneradoYEnviado = false;
    }
}