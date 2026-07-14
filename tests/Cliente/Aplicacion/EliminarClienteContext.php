<?php

namespace Test\Cliente\Aplicacion;

use Behat\Behat\Context\Context;
use App\Cliente\Aplicacion\EliminarCliente;
use App\Cliente\Dominio\RepositorioCliente;
use App\Cliente\Dominio\Cliente;
use App\Cliente\Dominio\ObjetoValor\CorreoElectronico;
use App\Cliente\Dominio\ObjetoValor\Telefono;
use App\Cliente\Dominio\ObjetoValor\Contrasena;
use App\Cliente\Dominio\ObjetoValor\ClienteId;
use Mockery;
use PHPUnit\Framework\Assert;

class EliminarClienteContext implements Context
{
    private $repositorioMock;
    private EliminarCliente $casoUso;
    private ?\Exception $excepcionCapturada = null;
    private bool $eliminadoConExito = false;

    public function __construct()
    {
        $this->repositorioMock = Mockery::mock(RepositorioCliente::class);
        $this->casoUso = new EliminarCliente($this->repositorioMock);
    }

    /**
     * @Given que existe un cliente activo con la identidad :id
     */
    public function queExisteUnClienteActivoConLaIdentidad($id)
    {
        $cliente = new Cliente(
            "Carlos Ramirez",
            new CorreoElectronico("carlos@gmail.com"),
            new Telefono("987654321"),
            new Contrasena("hash_seguro_abc"),
            "cliente",
            new ClienteId((int)$id)
        );

        $this->repositorioMock->allows('buscarPorId')
            ->with(Mockery::on(fn($arg) => $arg instanceof ClienteId && $arg->valor() === (int)$id))
            ->andReturn($cliente);

        $this->repositorioMock->allows('eliminar')
            ->with(Mockery::on(fn($arg) => $arg instanceof ClienteId && $arg->valor() === (int)$id))
            ->andReturnUsing(function() {
                $this->eliminadoConExito = true;
            });
    }

    /**
     * @Given que no existe el cliente con identidad :id
     */
    public function queNoExisteElClienteConIdentidad($id)
    {
        $this->repositorioMock->allows('buscarPorId')
            ->with(Mockery::on(fn($arg) => $arg instanceof ClienteId && $arg->valor() === (int)$id))
            ->andReturn(null);
    }

    /**
     * @When el administrador solicita la baja del cliente con identidad :id
     */
    public function elAdministradorSolicitaLaBajaDelClienteConIdentidad($id)
    {
        try {
            // Invoca directamente el método enviando el entero nativo como tu código de producción espera
            $this->casoUso->ejecutar((int)$id);
        } catch (\Exception $e) {
            $this->excepcionCapturada = $e;
        }
    }

    /**
     * @Then el cliente deja de formar parte del sistema
     */
    public function elClienteDejaDeFormarParteDelSistema()
    {
        Assert::assertNull($this->excepcionCapturada, $this->excepcionCapturada ? $this->excepcionCapturada->getMessage() : '');
        Assert::assertTrue($this->eliminadoConExito, "El repositorio de dominio nunca llegó a ejecutar el borrado.");
    }

    /**
     * @Then la solicitud es rechazada por identidad no encontrada
     */
    public function laSolicitudEsRechazadaPorIdentidadNoEncontrada()
    {
        Assert::assertNotNull($this->excepcionCapturada, "Se esperaba que se lanzara una excepción de dominio, pero el acceso continuó.");
        Assert::assertInstanceOf(\DomainException::class, $this->excepcionCapturada);
        
        // Verifica el string exacto de tu caso de uso
        Assert::assertEquals("No se puede eliminar: El cliente no existe.", $this->excepcionCapturada->getMessage());
    }

    /**
     * @AfterScenario
     */
    public function limpiarMocks()
    {
        Mockery::close();
        $this->excepcionCapturada = null;
        $this->eliminadoConExito = false;
    }
}