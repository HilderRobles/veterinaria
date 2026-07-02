<?php
namespace App\Cliente\Aplicacion;

interface ValidadorExistenciaCorreo 
{
    /**
     * Verifica con el proveedor (Gmail, Outlook, etc.) si la cuenta existe.
     */
    public function existe(string $correo): bool;
}