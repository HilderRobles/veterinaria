<?php

namespace Test\Cliente\Aplicacion;

use Behat\Behat\Context\Context;
use App\Cliente\Aplicacion\RegistrarCliente;
use App\Cliente\Aplicacion\RegistrarClientePeticion;
use App\Cliente\Aplicacion\CifradorContrasena;
use App\Cliente\Dominio\Cliente;
use App\Cliente\Dominio\RepositorioCliente;
use App\Cliente\Dominio\ObjetoValor\CorreoElectronico;
use App\Cliente\Dominio\ObjetoValor\Telefono;
use App\Cliente\Dominio\ObjetoValor\Contrasena;
use App\Cliente\Dominio\ObjetoValor\ClienteId;
use Mockery;
use PHPUnit\Framework\Assert;

class RegistrarClienteContext implements Context
{
    private $repositorioMock;
    private $cifradorMock;
    private RegistrarCliente $casoUso;
    private ?\Exception $excepcionCapturada = null;

    /**
     * El constructor inicializa los Mocks antes de cada escenario.
     */
    public function __construct()
    {
        $this->repositorioMock = Mockery::mock(RepositorioCliente::class);
        $this->cifradorMock = Mockery::mock(CifradorContrasena::class);
        
        // 💡 CONFIGURACIÓN POR DEFECTO: Evita el error de "no expectations were specified"
        $this->cifradorMock->allows('cifrar')
            ->byDefault()
            ->andReturn(new Contrasena("hash_seguro_123"));

        $this->casoUso = new RegistrarCliente($this->repositorioMock, $this->cifradorMock);
    }

    /**
     * @Given que el correo :correo está libre para registrarse
     */
    public function queElCorreoEstaLibreParaRegistrarse($correo)
    {
        $this->repositorioMock->allows('buscarPorCorreoElectronico')
            ->with($correo)
            ->andReturn(null);

        $this->repositorioMock->expects('guardar')->zeroOrMoreTimes();
    }

    /**
     * @Given que el correo :correo ya está registrado
     */
    public function queElCorreoYaEstaRegistrado($correo)
    {
        $clienteExistente = new Cliente(
            "Maria",
            new CorreoElectronico($correo),
            new Telefono("987654321"),
            new Contrasena("hash_antiguo_123"),
            "cliente",
            new ClienteId(1)
        );

        $this->repositorioMock->allows('buscarPorCorreoElectronico')
            ->with($correo)
            ->andReturn($clienteExistente);
        
        $this->repositorioMock->expects('guardar')->never();
    }

    /**
     * @Given que el sistema requiere un proveedor de correo electrónico autorizado
     */
    public function queElSistemaRequiereUnProveedorDeCorreoElectronicoAutorizado()
    {
        $this->repositorioMock->allows('buscarPorCorreoElectronico')->andReturn(null);
        $this->repositorioMock->expects('guardar')->never();
    }

    /**
     * @When se intenta registrar a :nombre con el correo :correo
     */
    public function ejecutarIntentoDeRegistro($nombre, $correo)
    {
        $peticion = new RegistrarClientePeticion($nombre, $correo, "923456789", "Clave123");
        
        try {
            $this->casoUso->ejecutar($peticion);
        } catch (\Exception $e) {
            $this->excepcionCapturada = $e;
        }
    }

    /**
     * @When otra persona intenta registrarse con el correo :correo
     * @When una persona intenta registrarse con el correo :correo
     */
    public function ejecutarIntentoDeRegistroSoloCorreo($correo)
    {
        $peticion = new RegistrarClientePeticion("Usuario Anonimo", $correo, "923456789", "Clave123");
        
        try {
            $this->casoUso->ejecutar($peticion);
        } catch (\Exception $e) {
            $this->excepcionCapturada = $e;
        }
    }

    /**
     * @Then el registro se completa con éxito
     */
    public function elRegistroSeCompletaConExito()
    {
        Assert::assertNull($this->excepcionCapturada, $this->excepcionCapturada ? $this->excepcionCapturada->getMessage() : '');
    }

    /**
     * @Then el registro es rechazado por correo duplicado
     */
    public function elRegistroEsRechazadoPorCorreoDuplicado()
    {
        Assert::assertNotNull($this->excepcionCapturada, "Se esperaba una excepción pero el caso de uso terminó con éxito.");
        Assert::assertEquals("El correo electrónico ya se encuentra registrado.", $this->excepcionCapturada->getMessage());
    }

    /**
     * @Then el registro es rechazado por proveedor de correo no válido
     */
    public function elRegistroEsRechazadoPorProveedorDeCorreoNoValido()
    {
        Assert::assertNotNull($this->excepcionCapturada, "Se esperaba una excepción de dominio de correo.");
        Assert::assertEquals("Solo se permiten registros con cuentas de Gmail.", $this->excepcionCapturada->getMessage());
    }

    /**
     * @AfterScenario
     */
    public function limpiarMocks()
    {
        // Ejecuta las verificaciones estrictas de Mockery (como ->expects('guardar')->never())
        Mockery::close();
    }
}