<?php

use Behat\Behat\Context\Context;
use App\Cliente\Aplicacion\AutenticarCliente;
use App\Cliente\Aplicacion\AutenticarClientePeticion;
use App\Cliente\Dominio\Cliente;
use App\Cliente\Dominio\RepositorioCliente;
use App\Cliente\Dominio\ObjetoValor\ClienteId;
use App\Cliente\Dominio\ObjetoValor\CorreoElectronico;
use App\Cliente\Dominio\ObjetoValor\Telefono;
use App\Cliente\Dominio\ObjetoValor\Contrasena;
use PHPUnit\Framework\Assert;

class AutenticarClienteContext implements Context
{
    private $repositorioMock;
    private AutenticarCliente $casoUso;
    private ?array $resultado = null;
    private ?Exception $excepcionLanzada = null;
    private ?Cliente $clienteEnMemoria = null;

    public function __construct()
    {
        // Creamos el mock del repositorio de Dominio
        $this->repositorioMock = Mockery::mock(RepositorioCliente::class);
        $this->casoUso = new AutenticarCliente($this->repositorioMock);
    }

    /**
     * @Dado que existe un cliente registrado con el correo :correo y contraseña :password
     */
    public function queExisteUnClienteRegistradoConElCorreoYContrasena(string $correo, string $password)
    {
        $hashValido = password_hash($password, PASSWORD_BCRYPT);
        
        // Creamos la entidad de dominio real
        $this->clienteEnMemoria = new Cliente(
            "Juan",
            new CorreoElectronico($correo),
            new Telefono("987654321"),
            new Contrasena($hashValido),
            "cliente",
            new ClienteId(1)
        );

        // Configuramos el Mock para que cuando el caso de uso busque este correo, devuelva el cliente
        $this->repositorioMock
            ->shouldReceive('buscarPorCorreoElectronico')
            ->with(Mockery::on(function ($argumentoCorreo) use ($correo) {
                return $argumentoCorreo->valor() === $correo; // O el método que uses para extraer el string del VO
            }))
            ->andReturn($this->clienteEnMemoria)
            ->byDefault();
    }

    /**
     * @Dado que no existe ningún cliente registrado con el correo :correo
     */
    public function queNoExisteNingunClienteRegistradoConElCorreo(string $correo)
    {
        // Configuramos el Mock para que devuelva null (usuario inexistente)
        $this->repositorioMock
            ->shouldReceive('buscarPorCorreoElectronico')
            ->andReturn(null)
            ->byDefault();
    }

    /**
     * @Cuando intenta iniciar sesión con el correo :correo y la contraseña :password
     */
    public function intentaIniciarSesionConElCorreoYLaContrasena(string $correo, string $password)
    {
        $peticion = new AutenticarClientePeticion($correo, $password);

        try {
            // Ejecutamos el caso de uso de la capa de aplicación
            $this->resultado = $this->casoUso->ejecutar($peticion);
        } catch (Exception $e) {
            // Si falla (credenciales incorrectas), atrapamos la excepción para evaluarla en el "Entonces"
            $this->excepcionLanzada = $e;
        }
    }

    /**
     * @Entonces la autenticación es exitosa y se genera un token de acceso
     */
    public function laAutenticacionEsExitosaYSeGeneraUnTokenDeAcceso()
    {
        Assert::assertNull($this->excepcionLanzada, "Se lanzó una excepción inesperada: " . ($this->excepcionLanzada?->getMessage()));
        Assert::assertIsArray($this->resultado);
        Assert::assertEquals('Juan', $this->resultado['nombre']);
    }

    /**
     * @Entonces el acceso es rechazado por credenciales inválidas
     */
    public function elAccesoEsRechazadoPorCredencialesInvalidas()
    {
        Assert::assertNotNull($this->excepcionLanzada, "Se esperaba un fallo de autenticación pero fue exitoso.");
        Assert::assertInstanceOf(\RuntimeException::class, $this->excepcionLanzada);
        Assert::assertEquals("Las credenciales de acceso son incorrectas.", $this->excepcionLanzada->getMessage());
    }
}