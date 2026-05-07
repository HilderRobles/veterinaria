<?php

use PHPUnit\Framework\TestCase;
use Domain\Cita;

class CitaTest extends TestCase
{
    // ==========================================
    // PRUEBA 1: Crear una cita válida
    // ==========================================
    public function testCrearCitaValida()
    {
        $cita = new Cita("Ana Lopez", "Luna", "2025-02-15", "14:30:00");
        
        $this->assertEquals("Ana Lopez", $cita->getClienteNombre());
        $this->assertEquals("Luna", $cita->getMascotaNombre());
        $this->assertEquals("2025-02-15", $cita->getFecha());
        $this->assertEquals("14:30:00", $cita->getHora());
        $this->assertEquals("pendiente", $cita->getEstado());
    }

    // ==========================================
    // PRUEBA 2: Crear cita sin cliente
    // ==========================================
    public function testCrearCitaSinClienteLanzaExcepcion()
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage("Cliente requerido");
        
        new Cita("", "Luna", "2025-02-15", "14:30:00");
    }

    // ==========================================
    // PRUEBA 3: Crear cita sin mascota
    // ==========================================
    public function testCrearCitaSinMascotaLanzaExcepcion()
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage("Mascota requerida");
        
        new Cita("Ana Lopez", "", "2025-02-15", "14:30:00");
    }

    // ==========================================
    // PRUEBA 4: Confirmar cita pendiente
    // ==========================================
    public function testConfirmarCitaPendiente()
    {
        $cita = new Cita("Ana Lopez", "Luna", "2025-02-15", "14:30:00");
        $cita->confirmar();
        
        $this->assertEquals("confirmada", $cita->getEstado());
    }

    // ==========================================
    // PRUEBA 5: Confirmar cita cancelada (debe fallar)
    // ==========================================
    public function testConfirmarCitaCanceladaLanzaExcepcion()
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage("No se puede confirmar cita cancelada"); // CORREGIDO
        
        $cita = new Cita("Ana Lopez", "Luna", "2025-02-15", "14:30:00");
        $cita->cancelar();
        $cita->confirmar();
    }

    // ==========================================
    // PRUEBA 6: Cancelar cita confirmada (debe fallar según tu código)
    // ==========================================
    public function testCancelarCitaConfirmadaLanzaExcepcion()
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage("No se puede cancelar cita confirmada");
        
        $cita = new Cita("Ana Lopez", "Luna", "2025-02-15", "14:30:00");
        $cita->confirmar();
        $cita->cancelar();
    }

    // ==========================================
    // PRUEBA 7: Cancelar cita pendiente
    // ==========================================
    public function testCancelarCitaPendiente()
    {
        $cita = new Cita("Ana Lopez", "Luna", "2025-02-15", "14:30:00");
        $cita->cancelar();
        
        $this->assertEquals("cancelada", $cita->getEstado());
    }

    // ==========================================
    // PRUEBA 8: Cancelar cita ya cancelada
    // ==========================================
    public function testCancelarCitaYaCancelada()
    {
        $cita = new Cita("Ana Lopez", "Luna", "2025-02-15", "14:30:00");
        $cita->cancelar();
        $cita->cancelar(); // Segunda cancelación, no debe fallar
        
        $this->assertEquals("cancelada", $cita->getEstado());
    }

    // ==========================================
    // PRUEBA 9: toArray devuelve datos correctos
    // ==========================================
    public function testToArrayDevuelveDatosCorrectos()
    {
        $cita = new Cita("Ana Lopez", "Luna", "2025-02-15", "14:30:00", 1);
        $array = $cita->toArray();
        
        $expected = [
            'id' => 1,
            'cliente_nombre' => 'Ana Lopez',
            'mascota_nombre' => 'Luna',
            'fecha' => '2025-02-15',
            'hora' => '14:30:00',
            'estado' => 'pendiente'
        ];
        
        $this->assertEquals($expected, $array);
    }
}