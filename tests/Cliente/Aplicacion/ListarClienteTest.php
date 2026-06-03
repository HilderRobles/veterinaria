<?php

namespace Tests\Cliente\Aplicacion;

use App\Cliente\Aplicacion\ListarCliente;
use App\Cliente\Dominio\RepositorioCliente;
use App\Cliente\Dominio\Cliente;
use App\Cliente\Dominio\ObjetoValor\ClienteId;
use App\Cliente\Dominio\ObjetoValor\CorreoElectronico;
use App\Cliente\Dominio\ObjetoValor\Telefono;
use App\Cliente\Dominio\ObjetoValor\Contrasena;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\UsesClass;

#[CoversClass(ListarCliente::class)]
#[UsesClass(Cliente::class)]
#[UsesClass(ClienteId::class)]
#[UsesClass(CorreoElectronico::class)]
#[UsesClass(Telefono::class)]
#[UsesClass(Contrasena::class)]
class ListarClienteTest extends TestCase {

    public function test_debe_retornar_un_arreglo_vacio_cuando_no_hay_clientes(): void {
        // 1. Simulamos el repositorio para que devuelva un array vacío
        $repositorioMock = $this->createMock(RepositorioCliente::class);
        $repositorioMock->expects($this->once())
            ->method('buscarTodos')
            ->willReturn([]);

        // 2. Ejecutamos el caso de uso
        $casoDeUso = new ListarCliente($repositorioMock);
        $resultado = $casoDeUso->ejecutar();

        // 3. Evaluamos que maneje correctamente el escenario vacío
        $this->assertIsArray($resultado);
        $this->assertEmpty($resultado);
    }

    public function test_debe_transformar_las_entidades_de_dominio_en_un_arreglo_plano_valido(): void {
        // 1. Creamos un cliente de prueba usando los objetos de valor reales de tu Dominio
        $clienteFake = new Cliente(
            "Tony Stark",
            new CorreoElectronico("tony@starkindustries.com"),
            new Telefono("923456789"),
            new Contrasena("hash_secreto_123"),
            "admin", // Pasamos el rol que pide tu constructor
            new ClienteId(5) // Pasamos el ID simulando que vino de la BD
        );

        // 2. Configuramos el simulador para que devuelva nuestro cliente de prueba
        $repositorioMock = $this->createMock(RepositorioCliente::class);
        $repositorioMock->expects($this->once())
            ->method('buscarTodos')
            ->willReturn([$clienteFake]);

        // 3. Ejecutamos el caso de uso pasándole el falso repositorio
        $casoDeUso = new ListarCliente($repositorioMock);
        $resultado = $casoDeUso->ejecutar();

        // 4. Aserciones estrictas: Validamos que los objetos complejos se convirtieran en tipos primitivos
        $this->assertIsArray($resultado);
        $this->assertCount(1, $resultado);
        
        $clienteMapeado = $resultado[0];
        $this->assertEquals(5, $clienteMapeado['id_cliente']);
        $this->assertEquals("Tony Stark", $clienteMapeado['nombre']);
        $this->assertEquals("tony@starkindustries.com", $clienteMapeado['email']);
        $this->assertEquals("923456789", $clienteMapeado['telefono']);
        $this->assertEquals("admin", $clienteMapeado['rol']);
    }
}