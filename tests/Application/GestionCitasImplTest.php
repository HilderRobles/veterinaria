<?php

use PHPUnit\Framework\TestCase;
use Application\GestionCitasImpl;
use Domain\RepositorioCitas;
use Domain\Cita;

// Repositorio en memoria para pruebas (NO TOCA BASE DE DATOS)
class RepositorioCitasEnMemoria implements RepositorioCitas
{
    private $citas = [];
    private $nextId = 1;

    public function guardar(Cita $cita)
    {
        $data = $cita->toArray();
        if (!$data['id']) {
            $data['id'] = $this->nextId++;
            $cita = new Cita(
                $data['cliente_nombre'],
                $data['mascota_nombre'],
                $data['fecha'],
                $data['hora'],
                $data['id']
            );
        }
        $this->citas[$data['id']] = $data;
        return $cita->toArray();
    }

    public function buscarPorId($id)
    {
        return $this->citas[$id] ?? null;
    }

    public function actualizar(Cita $cita)
    {
        $this->citas[$cita->getId()] = $cita->toArray();
    }

    public function listar()
    {
        return array_values($this->citas);
    }
}

class GestionCitasImplTest extends TestCase
{
    private $repositorio;
    private $gestionCitas;

    protected function setUp(): void
    {
        // Repositorio FRESCO para cada prueba (sin contaminación)
        $this->repositorio = new RepositorioCitasEnMemoria();
        $this->gestionCitas = new GestionCitasImpl($this->repositorio);
    }

    // ==========================================
    // PRUEBA 1: Crear cita
    // ==========================================
    public function testCrearCita()
    {
        $resultado = $this->gestionCitas->crear("Ana Lopez", "Luna", "2025-02-15", "14:30:00");
        
        $this->assertEquals("Ana Lopez", $resultado['cliente_nombre']);
        $this->assertEquals("Luna", $resultado['mascota_nombre']);
        $this->assertEquals("pendiente", $resultado['estado']);
        $this->assertEquals(1, $resultado['id']);
    }

    // ==========================================
    // PRUEBA 2: Listar citas (vacío al inicio)
    // ==========================================
    public function testListarCitasVacio()
    {
        $citas = $this->gestionCitas->listar();
        $this->assertEmpty($citas);
    }

    // ==========================================
    // PRUEBA 3: Listar citas después de crear una
    // ==========================================
    public function testListarCitasDespuesDeCrear()
    {
        $this->gestionCitas->crear("Ana Lopez", "Luna", "2025-02-15", "14:30:00");
        $citas = $this->gestionCitas->listar();
        
        $this->assertCount(1, $citas);
    }

    // ==========================================
    // PRUEBA 4: Confirmar cita existente
    // ==========================================
    public function testConfirmarCita()
    {
        $this->gestionCitas->crear("Ana Lopez", "Luna", "2025-02-15", "14:30:00");
        $resultado = $this->gestionCitas->confirmar(1);
        
        $this->assertEquals("confirmada", $resultado['estado']);
    }

    // ==========================================
    // PRUEBA 5: Confirmar cita que no existe
    // ==========================================
    public function testConfirmarCitaInexistenteLanzaExcepcion()
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage("Cita no encontrada");
        
        $this->gestionCitas->confirmar(999);
    }

    // ==========================================
    // PRUEBA 6: Cancelar cita existente
    // ==========================================
    public function testCancelarCita()
    {
        $this->gestionCitas->crear("Ana Lopez", "Luna", "2025-02-15", "14:30:00");
        $resultado = $this->gestionCitas->cancelar(1);
        
        $this->assertEquals("cancelada", $resultado['estado']);
    }

    // ==========================================
    // PRUEBA 7: Flujo completo (crear → confirmar → cancelar)
    // ==========================================
    public function testFlujoCompleto()
    {
        // Crear
        $creada = $this->gestionCitas->crear("Ana", "Luna", "2025-02-15", "14:30:00");
        $this->assertEquals("pendiente", $creada['estado']);
        
        // Confirmar
        $confirmada = $this->gestionCitas->confirmar($creada['id']);
        $this->assertEquals("confirmada", $confirmada['estado']);
        
        // Cancelar (desde confirmada)
        $cancelada = $this->gestionCitas->cancelar($creada['id']);
        $this->assertEquals("cancelada", $cancelada['estado']);
    }
}