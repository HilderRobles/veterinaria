<?php

namespace Test\Cliente\Aplicacion; // 👈 Corregido: 'namespace' completo y en singular

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
        // Creamos los dobles de prueba basados en las interfaces de tu Arquitectura Hexagonal
        $this->repositorioMock = Mockery::mock(RepositorioCliente::class);
        $this->cifradorMock = Mockery::mock(CifradorContrasena::class);
        
        // Inyectamos los mocks en el Caso de Uso real de la aplicación
        $this->casoUso = new RegistrarCliente($this->repositorioMock, $this->cifradorMock);
    }

    /**
     * @Given que el correo :correo está libre para registrarse
     */
    public function queElCorreoEstaLibreParaRegistrarse($correo)
    {
        // Caso Feliz: El repositorio busca el correo y simula que NO existe (retorna null)
        $this->repositorioMock->allows('buscarPorCorreoElectronico')
            ->with($correo)
            ->andReturn(null);

        // El cifrador simula que procesa la clave y devuelve un objeto Contrasena de dominio
        $this->cifradorMock->allows('cifrar')
            ->andReturn(new Contrasena("hash_seguro_123"));

        // Validamos que el caso de uso ejecute el método guardar exactamente una vez
        $this->repositorioMock->expects('guardar')->once();
    }

    /**
     * @Given que el correo :correo ya está registrado
     */
    public function queElCorreoYaEstaRegistrado($correo)
    {
        // Caso Triste: Creamos un usuario real en memoria para simular la colisión
        $clienteExistente = new Cliente(
            "Maria",
            new CorreoElectronico($correo),
            new Telefono("987654321"),
            new Contrasena("hash_antiguo_123"),
            "cliente",
            new ClienteId(1)
        );

        // El repositorio intercepta la búsqueda y simula que SÍ encontró al cliente
        $this->repositorioMock->allows('buscarPorCorreoElectronico')
            ->with($correo)
            ->andReturn($clienteExistente);
        
        // Como va a fallar antes, nos aseguramos de que el método guardar NUNCA se llame
        $this->repositorioMock->expects('guardar')->never();
    }

    /**
     * @Given que el sistema requiere un proveedor de correo electrónico autorizado
     */
    public function queElSistemaRequiereUnProveedorDeCorreoElectronicoAutorizado()
    {
        // Paso semántico de negocio. No requiere lógica técnica de mocks.
    }

    /**
     * @When se intenta registrar a :nombre con el correo :correo
     * @When otra persona intenta registrarse con el correo :correo
     * @When una persona intenta registrarse con el correo :correo
     */
    public function ejecutarIntentoDeRegistro($correo, $nombre = "Usuario Anonimo")
    {
        // Creamos el DTO de entrada para el caso de uso
        $peticion = new RegistrarClientePeticion($nombre, $correo, "923456789", "Clave123");
        
        // Ejecutamos el caso de uso atrapando cualquier excepción de negocio (Domain Exception)
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
        // 👈 Corregido: "completa" en lugar de "completes"
        // Si todo salió bien, no debería haber ninguna excepción capturada
        Assert::assertNull($this->excepcionCapturada);
    }

    /**
     * @Then el registro es rechazado por correo duplicado
     */
    public function elRegistroEsRechazadoPorCorreoDuplicado()
    {
        // Validamos que el caso de uso haya lanzado la excepción correcta
        Assert::assertNotNull($this->excepcionCapturada);
        Assert::assertEquals("El correo electrónico ya se encuentra registrado.", $this->excepcionCapturada->getMessage());
    }

    /**
     * @Then el registro es rechazado por proveedor de correo no válido
     */
    public function elRegistroEsRechazadoPorProveedorDeCorreoNoValido()
    {
        // Validamos la regla de negocio que restringe dominios extraños
        Assert::assertNotNull($this->excepcionCapturada);
        Assert::assertEquals("Solo se permiten registros con cuentas de Gmail.", $this->excepcionCapturada->getMessage());
    }

    /**
     * @AfterScenario
     * Limpieza obligatoria de Mockery para liberar memoria RAM entre escenarios de Behat.
     */
    public function limpiarMocks()
    {
        Mockery::close();
    }
}