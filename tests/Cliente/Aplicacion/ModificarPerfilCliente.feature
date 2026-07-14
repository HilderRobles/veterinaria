# language: es
Característica: Modificar Perfil Completo de Cliente
  Como Cliente de la veterinaria
  Quiero tener escenarios claros para editar mi perfil
  Para actualizar mis datos comunes libremente o cambiar datos sensibles bajo verificación

  Antecedentes:
    Dado que existe un cliente registrado con el correo "carlos@gmail.com" y contraseña "ClaveActual123"

  @datos-comunes
  Escenario: Modificar datos comunes sin restricciones ni verificaciones extras
    Cuando intenta actualizar sus datos comunes a nombre "Carlos Alberto", apellido "Pérez" y teléfono "911222333"
    Entonces el perfil se actualiza con éxito

  @foto
  Escenario: Modificar únicamente la foto de perfil
    Cuando intenta actualizar su foto de perfil con la ruta "/uploads/perfil_nuevo.jpg"
    Entonces el perfil se actualiza con éxito y se almacena la nueva imagen

  @correo
  Escenario: Modificar el correo electrónico validando que no esté duplicado
    Dado que también existe otro cliente registrado con el correo "maria@gmail.com"
    Cuando intenta cambiar su correo de "carlos@gmail.com" al nuevo correo "maria@gmail.com"
    Entonces la modificación es rechazada por correo duplicado

  @contrasena
  Escenario: Modificar la contraseña requiriendo la clave actual como verificación
    Cuando intenta cambiar su contraseña a "NuevaClave999" pero ingresa "CLAVE_ERRONEA" como clave actual
    Entonces la modificación es rechazada por credenciales inválidas