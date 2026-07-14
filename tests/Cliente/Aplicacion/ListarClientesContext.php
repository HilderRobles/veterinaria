<?php

namespace Test\Cliente\Aplicacion;

use Behat\Behat\Context\Context;
use Behat\Gherkin\Node\TableNode;
use App\Cliente\Aplicacion\ListarCliente;
use App\Cliente\Dominio\RepositorioCliente;
use App\Cliente\Dominio\Cliente;
use App\Cliente\Dominio\ObjetoValor\ClienteId;
use App\Cliente\Dominio\ObjetoValor\CorreoElectronico;
use App\Cliente\Dominio\ObjetoValor\Telefono;
use App\Cliente\Dominio\ObjetoValor\Contrasena;
use Mockery;
use PHPUnit\Framework\Assert;

class ListarClientesContext implements Context
{
    private $repositorioMock;
    private ListarCliente $casoUso;
    private array $clientesSimulados = [];
    private ?array $resultadoColeccion = null;

    public function __construct()
    {
        $this->repositorioMock = Mockery::mock(RepositorioCliente::class);
        $this->casoUso = new ListarCliente($this->repositorioMock);
    }

    /**
     * @Given que existen los siguientes clientes registrados:
     */
    public function queExistenLosSiguientesClientesRegistrados(TableNode $tabla)
    {
        $idAutoIncremental = 1;

        foreach ($tabla->getHash() as $fila) {
            // Instanciamos el modelo real respetando la firma de tu test base
            $this->clientesSimulados[] = new Cliente(
                $fila['nombre'],
                new CorreoElectronico($fila['correo']),
                new Telefono($fila['telefono']),
                new Contrasena("hash_por_defecto_123"),
                "cliente", // Rol por defecto
                new ClienteId($idAutoIncremental++)
            );
        }

        // Programamos el mock para que devuelva la colección armada
        $this->repositorioMock->allows('buscarTodos')
            ->andReturn($this->clientesSimulados);
    }

    /**
     * @When el administrador solicita la lista de clientes
     */
    public function elAdministradorSolicitaLaListaDeClientes()
    {
        // Ejecuta tu caso de uso real de producción
        $this->resultadoColeccion = $this->casoUso->ejecutar();
    }

    /**
     * @Then debe ver una colección con :cantidad clientes
     */
    public function debeVerUnaColeccionConClientes($cantidad)
    {
        Assert::assertIsArray($this->resultadoColeccion);
        Assert::assertCount((int)$cantidad, $this->resultadoColeccion);

        // Opcional: Validamos que el mapeo interno coincida con las claves de tu test ('nombre', 'email', etc.)
        if ((int)$cantidad > 0) {
            $primerCliente = $this->resultadoColeccion[0];
            Assert::assertArrayHasKey('id_cliente', $primerCliente);
            Assert::assertArrayHasKey('nombre', $primerCliente);
            Assert::assertArrayHasKey('email', $primerCliente);
        }
    }

    /**
     * @AfterScenario
     */
    public function limpiarMocks()
    {
        Mockery::close();
        $this->clientesSimulados = [];
        $this->resultadoColeccion = null;
    }
}