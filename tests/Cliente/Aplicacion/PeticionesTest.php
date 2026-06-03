<?php

namespace Tests\Cliente\Aplicacion;

use App\Cliente\Aplicacion\AutenticarClientePeticion;
use App\Cliente\Aplicacion\RegistrarClientePeticion;
use App\Cliente\Aplicacion\ModificarPerfilClientePeticion;
use App\Cliente\Aplicacion\RecuperarContrasenaPeticion;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(AutenticarClientePeticion::class)]
#[CoversClass(RegistrarClientePeticion::class)]
#[CoversClass(ModificarPerfilClientePeticion::class)]
#[CoversClass(RecuperarContrasenaPeticion::class)]
class PeticionesTest extends TestCase {

    public function test_debe_construir_autenticar_cliente_peticion_correctamente(): void {
        $peticion = new AutenticarClientePeticion("juan@correo.com", "Clave123");

        $this->assertEquals("juan@correo.com", $peticion->correoElectronico);
        $this->assertEquals("Clave123", $peticion->contrasenaPlana);
    }

    public function test_debe_construir_registrar_cliente_peticion_correctamente(): void {
        // 🟢 Ponemos un celular local válido de 9 dígitos que empieza con 9
        $peticion = new RegistrarClientePeticion("Juan", "juan@correo.com", "923456789", "Clave123");

        $this->assertEquals("Juan", $peticion->nombre);
        $this->assertEquals("juan@correo.com", $peticion->correoElectronico);
        $this->assertEquals("923456789", $peticion->telefono); // 🟢 Sincronizado exactamente con el input
        $this->assertEquals("Clave123", $peticion->contrasenaPlana);
    }

    public function test_debe_construir_modificar_perfil_cliente_peticion_correctamente(): void {
        // 🟢 Ponemos un celular local válido de 9 dígitos que empieza con 9
        $peticion = new ModificarPerfilClientePeticion(1, "Carlos", "carlos@correo.com", "987654321");

        $this->assertEquals(1, $peticion->clienteId);
        $this->assertEquals("Carlos", $peticion->nuevoNombre);
        $this->assertEquals("carlos@correo.com", $peticion->nuevoCorreo);
        $this->assertEquals("987654321", $peticion->nuevoTelefono); // 🟢 Sincronizado exactamente con el input
    }

    public function test_debe_construir_recuperar_contrasena_peticion_correctamente(): void {
        $peticion = new RecuperarContrasenaPeticion("recuperar@correo.com");

        $this->assertEquals("recuperar@correo.com", $peticion->correoElectronico);
    }
}