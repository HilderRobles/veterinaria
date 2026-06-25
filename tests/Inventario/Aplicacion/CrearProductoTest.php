<?php

namespace App\Tests\Inventario\Aplicacion;

use App\Inventario\Aplicacion\CrearProducto;
use App\Inventario\Dominio\Producto;
use App\Inventario\Dominio\RepositorioProducto;
use PHPUnit\Framework\TestCase;

// =========================================================================
// 1. EL REPOSITORIO FALSO (EN MEMORIA)
// =========================================================================
class RepositorioProductoEnMemoria implements RepositorioProducto {
    private array $productos = [];

    public function guardar(Producto $producto): void {
        $this->productos[$producto->id()] = $producto;
    }

    public function buscarPorId(string $id): ?Producto {
        return $this->productos[$id] ?? null;
    }
}

// =========================================================================
// 2. LA CLASE DE PRUEBAS CON SU ANOTACIÓN DE COBERTURA CORREGIDA
// =========================================================================
/**
 * @covers \App\Inventario\Aplicacion\CrearProducto
 */
class CrearProductoTest extends TestCase {
    
    //  PRUEBA 1: Verificar que un producto válido se cree correctamente
    public function test_debe_crear_un_producto_valido_exitosamente(): void {
        $repositorioFalso = new RepositorioProductoEnMemoria();
        $casoDeUso = new CrearProducto($repositorioFalso);

        $casoDeUso->ejecutar(
            "PROD-100",
            "Shampoo Antipulgas",
            "Shampoo especial para mascotas de pelo corto",
            25.50
        );

        $productoGuardado = $repositorioFalso->buscarPorId("PROD-100");
        
        $this->assertNotNull($productoGuardado);
        $this->assertEquals("Shampoo Antipulgas", $productoGuardado->nombre());
        $this->assertEquals(25.50, $productoGuardado->precio());
    }

    //  PRUEBA 2: Verificar que falle si el precio es cero o negativo
    public function test_debe_lanzar_excepcion_si_el_precio_es_invalido(): void {
        $repositorioFalso = new RepositorioProductoEnMemoria();
        $casoDeUso = new CrearProducto($repositorioFalso);

        $this->expectException(\DomainException::class);
        $this->expectExceptionMessage("El precio del producto debe ser mayor a cero.");

        $casoDeUso->ejecutar(
            "PROD-200",
            "Collar de Gato",
            "Collar reflectivo",
            -5.00
        );
    }

    // PRUEBA 3: Verificar que falle si el ID ya existe (Producto duplicado)
    public function test_debe_lanzar_excepcion_si_el_producto_ya_existe(): void {
        $repositorioFalso = new RepositorioProductoEnMemoria();
        $casoDeUso = new CrearProducto($repositorioFalso);

        $casoDeUso->ejecutar("PROD-100", "Producto Existente", "Detalle", 10.00);

        $this->expectException(\DomainException::class);
        $this->expectExceptionMessage("El producto con ID 'PROD-100' ya se encuentra registrado");

        $casoDeUso->ejecutar("PROD-100", "Producto Nuevo", "Otro Detalle", 15.00);
    }
}