<?php

namespace Test\Cliente\Dominio;

use App\Cliente\Dominio\Cliente;
use App\Cliente\Dominio\ObjetoValor\ClienteId;
use App\Cliente\Dominio\ObjetoValor\CorreoElectronico;
use App\Cliente\Dominio\ObjetoValor\Telefono;
use App\Cliente\Dominio\ObjetoValor\Contrasena;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

use PHPUnit\Framework\Attributes\UsesClass; // 💡 Importamos el atributo correcto

#[CoversClass(Cliente::class)]
#[UsesClass(ClienteId::class)]
#[UsesClass(CorreoElectronico::class)]
#[UsesClass(Telefono::class)]
#[UsesClass(Contrasena::class)]
class ClienteTest extends TestCase {
    
    private function crearAtributosValidos(): array {
        return [
            'nombre' => 'Carlos Mendoza',
            'correo' => new CorreoElectronico('carlos@correo.com'),
            'telefono' => new Telefono("987654321"),
            'contrasena' => new Contrasena('$2y$10$EjemploHashSeguroQueSimulaLaBaseDeDatos')
        ];
    }

    public function test_debe_crear_un_cliente_con_valores_correctos_y_rol_por_defecto(): void {
        $attrs = $this->crearAtributosValidos();

        $cliente = new Cliente(
            $attrs['nombre'],
            $attrs['correo'],
            $attrs['telefono'],
            $attrs['contrasena']
        );

        $this->assertEquals("Carlos Mendoza", $cliente->obtenerNombre());
        $this->assertSame($attrs['correo'], $cliente->obtenerCorreoElectronico());
        $this->assertSame($attrs['telefono'], $cliente->obtenerTelefono());
        $this->assertSame($attrs['contrasena'], $cliente->obtenerContrasena());
        $this->assertEquals("cliente", $cliente->obtenerRol());
        $this->assertNull($cliente->obtenerId());
    }

    public function test_debe_permitir_asignar_un_id_valido_en_el_constructor(): void {
        $attrs = $this->crearAtributosValidos();
        $idEsperado = new ClienteId(42);

        $cliente = new Cliente(
            $attrs['nombre'],
            $attrs['correo'],
            $attrs['telefono'],
            $attrs['contrasena'],
            'cliente',
            $idEsperado
        );

        $this->assertSame($idEsperado, $cliente->obtenerId());
    }

    public function test_no_debe_permitir_un_nombre_vacio(): void {
        $attrs = $this->crearAtributosValidos();

        $this->expectException(\DomainException::class);
        $this->expectExceptionMessage("El nombre del cliente no puede estar vacío.");

        new Cliente(
            "   ", 
            $attrs['correo'],
            $attrs['telefono'],
            $attrs['contrasena']
        );
    }

    public function test_no_debe_permitir_un_rol_invalido(): void {
        $attrs = $this->crearAtributosValidos();

        $this->expectException(\DomainException::class);
        $this->expectExceptionMessage("El rol 'invitado' no está permitido en la veterinaria.");

        new Cliente(
            $attrs['nombre'],
            $attrs['correo'],
            $attrs['telefono'],
            $attrs['contrasena'],
            'invitado'
        );
    }

    public function test_debe_promover_a_administrador_correctamente(): void {
        $attrs = $this->crearAtributosValidos();
        $cliente = new Cliente($attrs['nombre'], $attrs['correo'], $attrs['telefono'], $attrs['contrasena']);

        $cliente->promoverAAdministrador();

        $this->assertEquals("admin", $cliente->obtenerRol());
    }

    public function test_debe_promover_a_veterinario_correctamente(): void {
        $attrs = $this->crearAtributosValidos();
        $cliente = new Cliente($attrs['nombre'], $attrs['correo'], $attrs['telefono'], $attrs['contrasena']);

        $cliente->promoverAVeterinario();

        $this->assertEquals("veterinario", $cliente->obtenerRol());
    }

    public function test_debe_mapear_el_cliente_a_un_arreglo_plano_correctamente(): void {
        $attrs = $this->crearAtributosValidos();
        $id = new ClienteId(100);
        
        $cliente = new Cliente(
            $attrs['nombre'],
            $attrs['correo'],
            $attrs['telefono'],
            $attrs['contrasena'],
            'admin',
            $id
        );

        $arreglo = $cliente->mapearAArreglo();

        $this->assertEquals(100, $arreglo['id']);
        $this->assertEquals('Carlos Mendoza', $arreglo['nombre']);
        $this->assertEquals('carlos@correo.com', $arreglo['correo_electronico']);
        $this->assertEquals('987654321', $arreglo['telefono']);       
        $this->assertEquals('admin', $arreglo['rol']);
    }

    public function test_mapear_a_arreglo_debe_retornar_id_nulo_si_no_tiene_id(): void {
        $attrs = $this->crearAtributosValidos();
        $cliente = new Cliente($attrs['nombre'], $attrs['correo'], $attrs['telefono'], $attrs['contrasena']);

        $arreglo = $cliente->mapearAArreglo();

        $this->assertNull($arreglo['id']);
    }
}