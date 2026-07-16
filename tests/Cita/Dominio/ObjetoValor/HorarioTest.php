<?php
declare(strict_types=1);
namespace Tests\Cita\Dominio\ObjetoValor;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use App\Cita\Dominio\ObjetoValor\Horario;
use Exception;

#[CoversClass(Horario::class)]
final class HorarioTest extends TestCase
{
    public function test_crea_horario_valido_en_futuro_y_en_horario_laboral(): void
    {
        // Usamos un año seguro en el futuro para que el test nunca falle por la fecha
        $horario = new Horario('2028-10-10', '10:30:00');
        $this->assertSame('2028-10-10', $horario->getFecha());
        $this->assertSame('10:30:00', $horario->getHora());
    }

    public function test_inv02_impide_agendar_en_el_pasado(): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Invariante Violada [INV-02]');
        new Horario('2020-01-01', '10:00:00');
    }

    public function test_impide_agendar_fuera_del_horario_de_atencion(): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('El horario debe estar dentro del bloque de atención');
        new Horario('2028-10-10', '07:00:00'); // 7 AM no es válido
    }
}