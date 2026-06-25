# language: es
Característica: Eliminar Cliente
  Como Administrador de la veterinaria
  Quiero dar de baja a un cliente del sistema
  Para cumplir con las normativas de privacidad de datos

  Escenario: Eliminación de cliente por su correo electrónico
    Dado que existe un cliente registrado con el correo "carlos@gmail.com"
    Cuando el administrador solicita eliminar al cliente con el correo "carlos@gmail.com"
    Entonces el cliente es eliminado del sistema con éxito

  Escenario: Intento de eliminación de un cliente que no existe
    Dado que no existe ningún cliente registrado con el correo "inexistente@gmail.com"
    Cuando el administrador solicita eliminar al cliente con el correo "inexistente@gmail.com"
    Entonces la solicitud es rechazada por cliente no encontrado