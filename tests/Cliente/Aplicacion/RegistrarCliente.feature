# language: es
Característica: Registro de Clientes
  Reglas de la aplicación para procesar la creación de cuentas nuevas de forma segura y válida.

  @Exitoso
  Escenario: Crear una cuenta exitosamente
    Dado que el correo "juan@gmail.com" está libre para registrarse
    Cuando se intenta registrar a "Juan" con el correo "juan@gmail.com"
    Entonces el registro se completa con éxito

  @ErrorDuplicado
  Escenario: Error porque el correo ya está en uso
    Dado que el correo "maria@gmail.com" ya está registrado
    Cuando otra persona intenta registrarse con el correo "maria@gmail.com"
    Entonces el registro es rechazado por correo duplicado

  @ErrorCorreoInvalido
  Escenario: Error porque el correo no existe
    Dado que el sistema requiere un correo electrónico vigente
    Cuando una persona intenta registrarse con el correo "pedro@outlook.com"
    Entonces el registro es rechazado porque el correo no esta vigente