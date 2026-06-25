# language: es
Característica: Autenticar Cliente
  Como Cliente de la veterinaria quiero iniciar sesión en el sistema

  Escenario: Inicio de sesión completado con éxito
    Dado que existe un cliente registrado con el correo "juan@gmail.com" y contraseña "ClaveSegura123"
    Cuando intenta iniciar sesión con el correo "juan@gmail.com" y la contraseña "ClaveSegura123"
    Entonces la autenticación es exitosa y se genera un token de acceso

  Escenario: Inicio de sesión rechazado por contraseña incorrecta
    Dado que existe un cliente registrado con el correo "juan@gmail.com" y contraseña "ClaveSegura123"
    Cuando intenta iniciar sesión con el correo "juan@gmail.com" y la contraseña "ClaveErronea999"
    Entonces el acceso es rechazado por credenciales inválidas

  Escenario: Inicio de sesión rechazado por usuario inexistente
    Dado que no existe ningún cliente registrado con el correo "no_existe@gmail.com"
    Cuando intenta iniciar sesión con el correo "no_existe@gmail.com" y la contraseña "Cualquiera123"
    Entonces el acceso es rechazado por credenciales inválidas