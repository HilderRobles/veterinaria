# language: es

Característica: Eliminar Cliente
  Como Administrador de la veterinaria
  Quiero revocar el acceso de un cliente mediante su identidad única
  Para cumplir con las normativas de privacidad de datos

  Escenario: Cierre de cuenta de un cliente existente
    Dado que existe un cliente activo con la identidad 42
    Cuando el administrador solicita la baja del cliente con identidad 42
    Entonces el cliente deja de formar parte del sistema

  Escenario: Intento de dar de baja a un cliente que no existe
    Dado que no existe el cliente con identidad 999
    Cuando el administrador solicita la baja del cliente con identidad 999
    Entonces la solicitud es rechazada por identidad no encontrada