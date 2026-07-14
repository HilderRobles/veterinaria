<?php

namespace Test\Cliente\Aplicacion;

use Behat\Behat\Context\Context;
use App\Cliente\Aplicacion\AutenticarCliente;
use App\Cliente\Aplicacion\AutenticarClientePeticion;
use App\Cliente\Dominio\RepositorioCliente;
use App\Cliente\Dominio\Cliente;
use App\Cliente\Dominio\ObjetoValor\CorreoElectronico;
use App\Cliente\Dominio\ObjetoValor\Telefono;
use App\Cliente\Dominio\ObjetoValor\Contrasena;
use App\Cliente\Dominio\ObjetoValor\ClienteId;
use Mockery;
use PHPUnit\Framework\Assert;

class AutenticarClienteContext implements Context
{
    private $repositorioMock;
    private AutenticarCliente $casoUso;
    private ?array $resultadoCliente = null;
    private ?\Exception $excepcionCapturada = null;

    public function __construct()
    {
        $this->repositorioMock = Mockery::mock(RepositorioCliente::class);
        $this->casoUso = new AutenticarCliente($this->repositorioMock);
    }

    /**
     * @Given que existe un cliente registrado con el correo :correo y contraseña :clave
     */
    public function queExisteUnClienteRegistradoConElCorreoYContrasena($correo, $clave)
    {
        // 🎯 SOLUCIÓN: Generamos un hash real para que password_verify() pueda validarlo con éxito
        $hashSeguro = password_hash($clave, PASSWORD_BCRYPT);
        $contrasenaReal = new Contrasena($hashSeguro); 

        $cliente = new Cliente(
            "Juan Perez",
            new CorreoElectronico($correo),
            new Telefono("987654321"),
            $contrasenaReal,
            "cliente",
            new ClienteId(1)
        );

        $this->repositorioMock->allows('buscarPorCorreoElectronico')
            ->with(Mockery::on(fn($arg) => $arg instanceof CorreoElectronico && $arg->valor() === $correo))
            ->andReturn($cliente);
    }

    /**
     * @Given que no existe ningún cliente registrado con el correo :correo
     */
    public function queNoExisteNingunClienteRegistradoConElCorreo($correo)
    {
        $this->repositorioMock->allows('buscarPorCorreoElectronico')
            ->with(Mockery::on(fn($arg) => $arg instanceof CorreoElectronico && $arg->valor() === $correo))
            ->andReturn(null);
    }

    /**
     * @When intenta iniciar sesión con el correo :correo y la contraseña :clave
     */
    public function intentaIniciarSesionConElCorreoYLaContrasena($correo, $clave)
    {
        $peticion = new AutenticarClientePeticion($correo, $clave);

        try {
            $this->resultadoCliente = $this->casoUso->ejecutar($peticion);
        } catch (\Exception $e) {
            $this->excepcionCapturada = $e;
        }
    }

    /**
     * @Then la autenticación es exitosa y se genera un token de acceso
     */
    public function laAutenticacionEsExitosaYSeGeneraUnTokenDeAcceso()
    {
        Assert::assertNull($this->excepcionCapturada, $this->excepcionCapturada ? $this->excepcionCapturada->getMessage() : '');
        Assert::assertIsArray($this->resultadoCliente);
        Assert::assertArrayHasKey('correo_electronico', $this->resultadoCliente);
    }

    /**
     * @Then el acceso es rechazado por credenciales inválidas
     */
    public function elAccesoEsRechazadoPorCredencialesInvalidas()
    {
        Assert::assertNotNull($this->excepcionCapturada, "Se esperaba un fallo de autenticación.");
        Assert::assertEquals("Las credenciales de acceso son incorrectas.", $this->excepcionCapturada->getMessage());
    }

    /**
     * @AfterScenario
     */
    public function limpiarMocks()
    {
        Mockery::close();
        $this->resultadoCliente = null;
        $this->excepcionCapturada = null;
    }
}