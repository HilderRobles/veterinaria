# language: es
Característica: Listar Clientes
  Como Administrador de la veterinaria quiero ver una lista de los clientes registrados

  Escenario: Listar todos los clientes activos del sistema
    Dado que existen los siguientes clientes registrados:
      | nombre | correo            | telefono  |
      | Carlos | carlos@gmail.com  | 944555666 |
      | Maria  | maria@gmail.com   | 987654321 |
    Cuando el administrador solicita la lista de clientes
    Entonces debe ver una colección con 2 clientes