<?php

namespace Test\Cliente\Aplicacion;

use Behat\Behat\Context\Context;
use App\Cliente\Aplicacion\RegistrarCliente;
use App\Cliente\Aplicacion\RegistrarClientePeticion;
use App\Cliente\Aplicacion\CifradorContrasena;
use App\Cliente\Aplicacion\ValidadorExistenciaCorreo; // Ajustado al nuevo nombre estratégico
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
    private $validadorExistenciaMock;
    private RegistrarCliente $casoUso;
    private ?\Exception $excepcionCapturada = null;

    /**
     * El constructor inicializa los Mocks antes de cada escenario.
     */
    public function __construct()
    {
        $this->repositorioMock = Mockery::mock(RepositorioCliente::class);
        $this->cifradorMock = Mockery::mock(CifradorContrasena::class);
        $this->validadorExistenciaMock = Mockery::mock(ValidadorExistenciaCorreo::class);
        
        // Configuración por defecto para el cifrador
        $this->cifradorMock->allows('cifrar')
            ->byDefault()
            ->andReturn(new Contrasena("hash_seguro_123"));

        // Por defecto asumimos que los correos existen/son vigentes
        $this->validadorExistenciaMock->allows('existe')->byDefault()->andReturn(true);

        $this->casoUso = new RegistrarCliente(
            $this->repositorioMock, 
            $this->cifradorMock,
            $this->validadorExistenciaMock
        );
    }

    /**
     * @Given que el correo :correo está libre para registrarse
     */
    public function queElCorreoEstaLibreParaRegistrarse($correo)
    {
        // 💡 CORRECCIÓN: Permitimos cualquier objeto o validamos dinámicamente el valor interno
        $this->repositorioMock->allows('buscarPorCorreoElectronico')
            ->with(Mockery::on(fn($arg) => $arg instanceof CorreoElectronico && $arg->valor() === $correo))
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

        // 💡 CORRECCIÓN: Coincidencia exacta con la instancia del Objeto de Valor
        $this->repositorioMock->allows('buscarPorCorreoElectronico')
            ->with(Mockery::on(fn($arg) => $arg instanceof CorreoElectronico && $arg->valor() === $correo))
            ->andReturn($clienteExistente);
        
        $this->repositorioMock->expects('guardar')->never();
    }

    /**
     * @Given que el sistema requiere un correo electrónico vigente
     */
    public function queElSistemaRequiereUnCorreoElectronicoVigente()
    {
        // El validador externo dirá que no existe en el proveedor real
        $this->validadorExistenciaMock->allows('existe')->andReturn(false);
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
     * @Then el registro es rechazado porque el correo no esta vigente
     */
    public function elRegistroEsRechazadoPorqueElCorreoNoEstaVigente()
    {
        Assert::assertNotNull($this->excepcionCapturada, "Se esperaba una excepción de vigencia de correo.");
        Assert::assertEquals("El correo electrónico proporcionado no está vigente.", $this->excepcionCapturada->getMessage());
    }

    /**
     * @AfterScenario
     */
    public function limpiarMocks()
    {
        Mockery::close();
    }
}