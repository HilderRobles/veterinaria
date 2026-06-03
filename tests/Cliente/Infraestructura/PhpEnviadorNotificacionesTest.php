<?php

namespace Tests\Cliente\Infraestructura;

use App\Cliente\Infraestructura\PhpEnviadorNotificaciones;
use App\Cliente\Dominio\ObjetoValor\CorreoElectronico;
use App\Cliente\Dominio\ObjetoValor\Telefono;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\UsesClass;

#[CoversClass(PhpEnviadorNotificaciones::class)]
#[UsesClass(CorreoElectronico::class)]
#[UsesClass(Telefono::class)]
class PhpEnviadorNotificacionesTest extends TestCase {

    public function test_debe_ejecutar_todos_los_canales_de_notificacion_sin_errores(): void {
        $enviador = new PhpEnviadorNotificaciones();
        
        try {
            $enviador->enviarCorreoRestablecimiento(new CorreoElectronico("test@railway.com"), "http://link.com");
            $enviador->enviarSmsAlerta(new Telefono("923456789"), "Alerta SMS");
            $enviador->enviarEmailAlerta(new CorreoElectronico("test@railway.com"), "Alerta Email");
            $this->assertTrue(true); 
        } catch (\Throwable $e) {
            $this->fail("Fallo en la notificación: " . $e->getMessage());
        }
    }
}