<?php

namespace Test\Cliente\Aplicacion;

use Behat\Behat\Context\Context;
use App\Cliente\Aplicacion\SolicitarRecuperacionContrasena;
use App\Cliente\Aplicacion\RecuperarContrasenaPeticion;
use App\Cliente\Aplicacion\EnviadorNotificaciones;
use App\Cliente\Dominio\RepositorioCliente;
use App\Cliente\Dominio\Cliente;
use App\Cliente\Dominio\ObjetoValor\CorreoElectronico;
use App\Cliente\Dominio\ObjetoValor\Telefono;
use App\Cliente\Dominio\ObjetoValor\Contrasena;
use App\Cliente\Dominio\ObjetoValor\ClienteId;
use PHPUnit\Framework\Assert;

class SolicitarRecuperacionContrasenaContext implements Context
{
    private $repositorioMock;
    private $enviadorMock;
    private SolicitarRecuperacionContrasena $casoUso;
    private ?\Exception $excepcionCapturada = null;

    public function __construct()
    {
        // Usamos PHPUnit mocks simulados manualmente o mediante un Framework mock de tu setup
        $this->repositorioMock = \Mockery::mock(RepositorioCliente::class);
        $this->enviadorMock = \Mockery::mock(EnviadorNotificaciones::class);
        
        $this->casoUso = new SolicitarRecuperacionContrasena($this->repositorioMock, $this->enviadorMock);
    }

    /**
     * @Given que existe un cliente registrado con el correo :correo
     */
    public function queExisteUnClienteRegistradoConElCorreo($correo)
    {
        $cliente = new Cliente(
            "Carlos Perez",
            new CorreoElectronico($correo),
            new Telefono("987654321"),
            new Contrasena("hash_seguro"),
            "cliente",
            new ClienteId(1)
        );

        $this->repositorioMock->allows('buscarPorCorreoElectronico')
            ->with(\Mockery::on(fn($arg) => $arg instanceof CorreoElectronico && $arg->valor() === $correo))
            ->andReturn($cliente);
    }

    /**
     * @When solicita recuperar la contraseña para el correo :correo
     */
    public function solicitaRecuperarLaContrasenaParaElCorreo($correo)
    {
        // Para correos que no existen, preparamos el mock de antemano
        if ($correo === "carlso@gmail.com") {
            $this->repositorioMock->allows('buscarPorCorreoElectronico')
                ->with(\Mockery::on(fn($arg) => $arg instanceof CorreoElectronico && $arg->valor() === $correo))
                ->andReturn(null);
        }

        $peticion = new RecuperarContrasenaPeticion($correo);

        try {
            $this->casoUso->ejecutar($peticion);
        } catch (\Exception $e) {
            $this->excepcionCapturada = $e;
        }
    }

    /**
     * @Then el sistema genera el token y envía el correo de recuperación
     */
    public function elSistemaGeneraElTokenYEnviaElCorreoDeRecuperacion()
    {
        Assert::assertNull($this->excepcionCapturada);
    }

    /**
     * @Then la solicitud es rechazada indicando que el correo no está registrado
     */
    public function laSolicitudEsRechazadaIndicandoQueElCorreoNoEstaRegistrado()
    {
        Assert::assertNotNull($this->excepcionCapturada, "Se esperaba un fallo por correo inexistente.");
        Assert::assertEquals("El correo electrónico proporcionado no se encuentra registrado.", $this->excepcionCapturada->getMessage());
    }

    /**
     * @AfterScenario
     */
    public function limpiarMocks()
    {
        \Mockery::close();
    }
}