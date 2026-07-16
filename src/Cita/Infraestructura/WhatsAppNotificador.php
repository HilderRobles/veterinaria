<?php
declare(strict_types=1);
namespace App\Cita\Infraestructura;

use App\Cita\Aplicacion\NotificadorCita;
use App\Cita\Dominio\Cita;

final class WhatsAppNotificador implements NotificadorCita
{
    public function notificarCreacion(Cita $cita): void
    {
        $mensaje = "[WhatsApp API ACL] 📲 Mensaje enviado al Cliente ID {$cita->getIdCliente()}: " . 
                   "Su cita médica ha sido agendada para el {$cita->getFecha()} a las {$cita->getHora()}.";
        
        // Esto imprime el mensaje en la consola del servidor donde corre PHP
        error_log($mensaje);
    }

    public function notificarConfirmacion(Cita $cita): void
    {
        $mensaje = "[WhatsApp API ACL] 📲 Mensaje enviado al Cliente ID {$cita->getIdCliente()}: " . 
                   "¡Su cita ha sido CONFIRMADA con éxito!";
        
        error_log($mensaje);
    }
}