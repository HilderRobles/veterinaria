<?php

namespace App\Cliente\Infraestructura;

use App\Cliente\Aplicacion\ValidadorExistenciaCorreo;


class PhpValidadorExistenciaCorreo implements ValidadorExistenciaCorreo {
    
    public function existe(string $correo): bool {
        // 1. Limpieza básica y validación de formato sintáctico
        $correo = filter_var(trim($correo), FILTER_SANITIZE_EMAIL);
        if (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
            return false;
        }

        // 2. Extraer el dominio del correo (ej: de "carlos@gmail.com" extrae "gmail.com")
        $partes = explode('@', $correo);
        $dominio = array_pop($partes);

        // 3. 🎯 VALIDACIÓN DE INFRAESTRUCTURA: Comprobar registros DNS del tipo MX (Mail Exchanger)
        // Esto verifica si el dominio tiene servidores configurados para recibir correos reales.
        if (!checkdnsrr($dominio, 'MX')) {
            return false;
        }

        return true;
    }
}