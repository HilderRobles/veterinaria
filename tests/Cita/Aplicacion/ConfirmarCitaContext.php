<?php
declare(strict_types=1);
namespace Test\Cita\Aplicacion;

use Behat\Behat\Context\Context;
use App\Cita\Aplicacion\ConfirmarCita;
use App\Cita\Aplicacion\NotificadorCita;
use App\Cita\Dominio\RepositorioCita;
use App\Cita\Dominio\Cita;
use Mockery;
use PHPUnit\Framework\Assert;

class ConfirmarCitaContext implements Context
{
    private $repositorioMock;
    private $notificadorMock;
    private ConfirmarCita $casoUso;
    private ?\Exception $excepcionCapturada = null;
    private ?Cita $citaMock = null;

    public function __construct()
    {
        $this->repositorioMock = Mockery::mock(RepositorioCita::class);
        $this->notificadorMock = Mockery::mock(NotificadorCita::class);
        
        // El mock ignora la notificación de WhatsApp para el test
        $this->notificadorMock->allows('notificarConfirmacion')->andReturnNull();

        $this->casoUso = new ConfirmarCita($this->repositorioMock, $this->notificadorMock);
    }

    /**
     * @Given una cita registrada en estado :estado
     */
    public function unaCitaRegistradaEnEstado($estado)
    {
        $this->citaMock = Cita::reconstituir(1, 1, 1, 1, '2028-10-10', '10:00:00', 'Test', $estado);
        
        $this->repositorioMock->allows('buscarPorId')->with(1)->andReturn($this->citaMock);
        $this->repositorioMock->allows('actualizar')->andReturnNull();
    }

    /**
     * @When la doctora confirma la atención de la cita
     */
    public function laDoctoraConfirmaLaCita()
    {
        try {
            $this->casoUso->ejecutar(1);
        } catch (\Exception $e) {
            $this->excepcionCapturada = $e;
        }
    }

    /**
     * @When se intenta confirmar nuevamente la cita
     */
    public function seIntentaConfirmarNuevamente()
    {
        $this->laDoctoraConfirmaLaCita(); 
    }

    /**
     * @Then la cita cambia a estado confirmada
     */
    public function laCitaCambiaAEstadoConfirmada()
    {
        Assert::assertNull($this->excepcionCapturada);
        Assert::assertEquals('confirmada', $this->citaMock->getEstado());
    }

    /**
     * @Then el sistema debe impedir la confirmación
     */
    public function elSistemaDebeImpedirConfirmacion()
    {
        Assert::assertNotNull($this->excepcionCapturada);
        Assert::assertStringContainsString('INV-01', $this->excepcionCapturada->getMessage());
    }

    /** @AfterScenario */
    public function limpiarMocks()
    {
        Mockery::close();
    }
}