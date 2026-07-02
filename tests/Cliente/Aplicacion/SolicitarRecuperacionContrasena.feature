# language: es
Característica: Solicitar Recuperación de Contraseña con UX Clara
  Como Cliente de la veterinaria que olvidó su clave
  Quiero que el sistema me avise si escribí mal mi correo
  Para corregirlo de inmediato, sabiendo que el sistema está protegido contra abusos

  Escenario: Solicitud de recuperación exitosa
    Dado que existe un cliente registrado con el correo "carlos@gmail.com"
    Cuando solicita recuperar la contraseña para el correo "carlos@gmail.com"
    Entonces el sistema genera el token y envía el correo de recuperación

  Escenario: Intento de recuperación con un correo mal escrito o inexistente
    Dado que existe un cliente registrado con el correo "carlos@gmail.com"
    Cuando solicita recuperar la contraseña para el correo "carlso@gmail.com"
    Entonces la solicitud es rechazada indicando que no se pudo restablecer.